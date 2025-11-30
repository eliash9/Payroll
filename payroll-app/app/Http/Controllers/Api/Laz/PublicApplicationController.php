<?php

namespace App\Http\Controllers\Api\Laz;

use App\Http\Controllers\Controller;
use App\Http\Resources\Laz\ApplicationResource;
use App\Models\Applicant;
use App\Models\Application;
use App\Models\ApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PublicApplicationController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'program_id' => 'required|exists:programs,id',
            'program_period_id' => 'required|exists:program_periods,id',
            
            // Applicant Data
            'national_id' => 'required|string|max:20',
            'full_name' => 'required|string|max:255',
            'birth_date' => 'required|date',
            'address' => 'required|string',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            
            // Application Data
            'requested_amount' => 'required|numeric|min:0',
            'need_description' => 'required|string',
            'location_province' => 'required|string',
            'location_regency' => 'required|string',
            
            // Documents
            'documents' => 'nullable|array',
            'documents.*.file' => 'required|file|max:5120', // 5MB max
            'documents.*.type' => 'required|string',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            // 1. Create or Update Applicant
            $applicant = Applicant::updateOrCreate(
                ['national_id' => $validated['national_id']],
                [
                    'full_name' => $validated['full_name'],
                    'birth_date' => $validated['birth_date'],
                    'address' => $validated['address'],
                    'phone' => $validated['phone'],
                    'email' => $validated['email'] ?? null,
                ]
            );

            // 2. Create Application
            $code = 'APP-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5));
            
            $application = Application::create([
                'code' => $code,
                'program_id' => $validated['program_id'],
                'program_period_id' => $validated['program_period_id'],
                'applicant_type' => 'individual',
                'applicant_id' => $applicant->id,
                'requested_amount' => $validated['requested_amount'],
                'requested_aid_type' => 'uang', // Default to 'uang' for now, or add to form
                'need_description' => $validated['need_description'],
                'location_province' => $validated['location_province'],
                'location_regency' => $validated['location_regency'],
                'status' => 'submitted',
            ]);

            // 3. Handle Documents
            // 3. Handle Documents
            if ($request->has('documents')) {
                $documents = $request->all()['documents'];
                
                foreach ($documents as $index => $docData) {
                    // Check if file exists in the request for this index
                    if ($request->hasFile("documents.{$index}.file")) {
                        $file = $request->file("documents.{$index}.file");
                        $type = $docData['type'];
                        
                        $path = $file->store('application_documents', 'public');
                        
                        ApplicationDocument::create([
                            'application_id' => $application->id,
                            'document_type' => $type,
                            'file_path' => $path,
                            'description' => $file->getClientOriginalName(),
                        ]);
                    }
                }
            }

            return response()->json([
                'message' => 'Application submitted successfully',
                'data' => new ApplicationResource($application),
            ], 201);
        });
    }

    public function checkStatus(Request $request)
    {
        $validated = $request->validate([
            'code' => 'required|string',
            'national_id' => 'required|string',
        ]);

        $application = Application::where('code', $validated['code'])
            ->whereHas('applicant', function ($q) use ($validated) {
                $q->where('national_id', $validated['national_id']);
            })
            ->first();

        if (!$application) {
            return response()->json(['message' => 'Application not found or NIK does not match.'], 404);
        }

        return new ApplicationResource($application);
    }
}
