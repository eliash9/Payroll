<?php

namespace App\Http\Controllers;

    use App\Models\Job;
    use App\Models\JobResponsibility;
    use App\Models\JobRequirement;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Auth;
    use Illuminate\Support\Facades\DB;

class JobController extends Controller
{
    public function index()
    {
        $query = Job::query();
        
        if (Auth::user()->company_id) {
            $query->where('company_id', Auth::user()->company_id);
        }

        $jobs = $query->orderBy('title')->paginate(20);

        return view('masters.jobs.index', compact('jobs'));
    }

    public function create()
    {
        return view('masters.jobs.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:job_profiles,code',
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|array',
            'responsibilities.*.responsibility' => 'required|string',
            'responsibilities.*.is_primary' => 'nullable|boolean',
            'requirements' => 'nullable|array',
            'requirements.*.requirement' => 'required|string',
            'requirements.*.type' => 'required|in:education,skill,experience,certification,other',
        ]);

        DB::transaction(function () use ($data) {
            $jobData = [
                'company_id' => Auth::user()->company_id ?? 1, // Default to 1 if not set, or handle appropriately
                'title' => $data['title'],
                'code' => $data['code'] ?? null,
                'description' => $data['description'] ?? null,
            ];

            $job = Job::create($jobData);

            if (!empty($data['responsibilities'])) {
                foreach ($data['responsibilities'] as $resp) {
                    $job->responsibilities()->create([
                        'responsibility' => $resp['responsibility'],
                        'is_primary' => $resp['is_primary'] ?? false,
                    ]);
                }
            }

            if (!empty($data['requirements'])) {
                foreach ($data['requirements'] as $req) {
                    $job->requirements()->create([
                        'requirement' => $req['requirement'],
                        'type' => $req['type'],
                    ]);
                }
            }
        });

        return redirect()->route('jobs.index')->with('success', 'Job profile created successfully.');
    }

    public function edit(Job $job)
    {
        if (Auth::user()->company_id && $job->company_id != Auth::user()->company_id) {
            abort(403);
        }
        
        $job->load(['responsibilities', 'requirements']);
        
        return view('masters.jobs.edit', compact('job'));
    }

    public function update(Request $request, Job $job)
    {
        if (Auth::user()->company_id && $job->company_id != Auth::user()->company_id) {
            abort(403);
        }

        $data = $request->validate([
            'title' => 'required|string|max:191',
            'code' => 'nullable|string|max:50|unique:job_profiles,code,' . $job->id,
            'description' => 'nullable|string',
            'responsibilities' => 'nullable|array',
            'responsibilities.*.responsibility' => 'required|string',
            'responsibilities.*.is_primary' => 'nullable|boolean',
            'requirements' => 'nullable|array',
            'requirements.*.requirement' => 'required|string',
            'requirements.*.type' => 'required|in:education,skill,experience,certification,other',
        ]);

        DB::transaction(function () use ($job, $data) {
            $job->update([
                'title' => $data['title'],
                'code' => $data['code'] ?? null,
                'description' => $data['description'] ?? null,
            ]);

            // Simple replacement strategy for now (delete all and recreate)
            // Ideally we should sync by ID, but this is cleaner for simple lists without complex history
            $job->responsibilities()->delete();
            if (!empty($data['responsibilities'])) {
                foreach ($data['responsibilities'] as $resp) {
                    $job->responsibilities()->create([
                        'responsibility' => $resp['responsibility'],
                        'is_primary' => $resp['is_primary'] ?? false,
                    ]);
                }
            }

            $job->requirements()->delete();
            if (!empty($data['requirements'])) {
                foreach ($data['requirements'] as $req) {
                    $job->requirements()->create([
                        'requirement' => $req['requirement'],
                        'type' => $req['type'],
                    ]);
                }
            }
        });

        return redirect()->route('jobs.index')->with('success', 'Job profile updated successfully.');
    }

    public function destroy(Job $job)
    {
        if (Auth::user()->company_id && $job->company_id != Auth::user()->company_id) {
            abort(403);
        }

        $job->delete();
        return redirect()->route('jobs.index')->with('success', 'Job profile deleted.');
    }
}
