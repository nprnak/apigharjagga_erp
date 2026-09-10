<?php

namespace App\Http\Controllers;

use App\Models\JobApplication;
use App\Models\JobOpening;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response as InertiaResponse;

class CareersController extends Controller
{
    public function index(): InertiaResponse
    {
        return Inertia::render('Careers', [
            'jobs' => JobOpening::where('is_active', true)
                ->orderByDesc('posted_date')
                ->get(['job_id', 'title', 'department', 'location', 'employment_type', 'description', 'requirements', 'posted_date', 'closing_date']),
        ]);
    }

    public function apply(Request $request, JobOpening $job): RedirectResponse
    {
        $validated = $request->validate([
            'applicant_name' => ['required', 'string', 'max:150'],
            'email' => ['required', 'email', 'max:150'],
            'phone' => ['nullable', 'string', 'max:20'],
            'cover_letter' => ['nullable', 'string'],
            'resume' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ]);

        $resumePath = $request->file('resume')->store('careers/resumes', 'public');

        JobApplication::create([
            'job_id' => $job->job_id,
            'applicant_name' => $validated['applicant_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'cover_letter' => $validated['cover_letter'] ?? null,
            'resume_path' => $resumePath,
            'status' => 'received',
        ]);

        return back()->with('success', 'Application submitted successfully. We will contact you if shortlisted.');
    }
}
