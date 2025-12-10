<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class AuthTokenController extends Controller
{
    /**
     * Issue a personal access token using email/password.
     */
    public function store(Request $request)
    {
        $request->validate([
            'login_id' => ['required', 'string'],
            'password' => ['required', 'string'],
            'device_name' => ['nullable', 'string', 'max:191'],
        ]);

        $loginId = $request->login_id;

        // Determine if loginId is email
        $user = null;
        if (filter_var($loginId, FILTER_VALIDATE_EMAIL)) {
            $user = User::where('email', $loginId)->first();
        } else {
            // Treat as employee code
            $employee = \App\Models\Employee::where('employee_code', $loginId)->first();
            if ($employee && $employee->email) {
                $user = User::where('email', $employee->email)->first();
            }
        }

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'login_id' => ['Kredensial yang diberikan salah.'],
            ]);
        }

        $token = $user->createToken($request->input('device_name', 'api'))->plainTextToken;

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'company_id' => $user->company_id,
            'role' => $user->role,
        ]);
    }
}
