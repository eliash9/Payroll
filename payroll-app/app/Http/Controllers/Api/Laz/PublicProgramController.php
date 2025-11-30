<?php

namespace App\Http\Controllers\Api\Laz;

use App\Http\Controllers\Controller;
use App\Http\Resources\Laz\ProgramResource;
use App\Models\Program;
use Illuminate\Http\Request;

class PublicProgramController extends Controller
{
    public function index()
    {
        $programs = Program::where('is_active', true)
            ->with(['activePeriods'])
            ->get();

        return ProgramResource::collection($programs);
    }

    public function show($id)
    {
        $program = Program::where('is_active', true)
            ->with(['activePeriods'])
            ->findOrFail($id);

        return new ProgramResource($program);
    }
}
