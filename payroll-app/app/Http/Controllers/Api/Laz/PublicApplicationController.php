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

use App\Traits\LazWhatsAppSender;

class PublicApplicationController extends Controller
{
    use LazWhatsAppSender;

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

            if ($applicant->email) {
                try {
                    \Illuminate\Support\Facades\Mail::to($applicant->email)->send(new \App\Mail\ApplicationCreatedMail($application));
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send application created email: ' . $e->getMessage());
                }
            }

            // WhatsApp Notification
            if ($applicant->phone) {
                try {
                    $template = \App\Models\LazSetting::where('key', 'email_new_request_body')->value('value') 
                        ?? "Halo {applicant_name},\n\nTerima kasih telah mengajukan permohonan bantuan.\nKode Tiket Anda: {code}\nProgram: {program_name}\nTanggal: {date}\n\nMohon simpan kode tiket ini untuk pengecekan status.\n\nSalam,\nTim LAZ";
                    
                    $message = str_replace(
                        ['{applicant_name}', '{code}', '{program_name}', '{date}'],
                        [
                            $applicant->full_name,
                            $application->code,
                            $application->program->name ?? '-',
                            $application->created_at->format('d-m-Y')
                        ],
                        $template
                    );

                    $this->sendWhatsAppMessage($applicant->phone, $message);
                } catch (\Exception $e) {
                    \Illuminate\Support\Facades\Log::error('Failed to send application created WA: ' . $e->getMessage());
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
