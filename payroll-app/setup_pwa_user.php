<?php

use App\Models\User;
use App\Models\Employee;
use Illuminate\Support\Facades\Hash;

// 1. Find an employee to test with (e.g., the first one)
$employee = Employee::first();

if (!$employee) {
    echo "No employees found! Please create an employee first.\n";
    exit;
}

echo "Found Employee: {$employee->full_name} ({$employee->email})\n";

if (!$employee->email) {
    echo "Employee has no email! Updating email to 'test.employee@example.com'...\n";
    $employee->email = 'test.employee@example.com';
    $employee->save();
}

// 2. Create or Update User
$user = User::updateOrCreate(
    ['email' => $employee->email],
    [
        'name' => $employee->full_name,
        'password' => Hash::make('password'), // Default password
        'company_id' => $employee->company_id,
        'branch_id' => $employee->branch_id,
        'role' => 'staff' // Assuming 'staff' role exists or is just a string
    ]
);

echo "User created/updated successfully!\n";
echo "Email: {$user->email}\n";
echo "Password: password\n";
echo "You can now login to the PWA with these credentials.\n";
