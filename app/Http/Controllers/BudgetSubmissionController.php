<?php

namespace App\Http\Controllers;

use App\Models\BudgetSubmission;
use App\Models\BudgetRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class BudgetSubmissionController extends Controller
{
    /**
     * Display a listing of budget submissions.
     */
    public function index()
    {
        $this->authorize('viewAny', BudgetSubmission::class);
        $user = auth()->user();

        if ($user->isAdmin()) {
            $submissions = BudgetSubmission::with(['user', 'budgetRequest'])->orderBy('created_at', 'desc')->paginate(15);
        } elseif ($user->isDepartment()) {
            $submissions = BudgetSubmission::whereHas('budgetRequest', function ($query) use ($user) {
                $query->where('department_id', $user->department_id);
            })->with(['user', 'budgetRequest'])->orderBy('created_at', 'desc')->paginate(15);
        } else {
            $submissions = BudgetSubmission::where('user_id', $user->id)
                ->with(['user', 'budgetRequest'])
                ->orderBy('created_at', 'desc')
                ->paginate(15);
        }

        return response()->json($submissions);
    }

    /**
     * Show the form for creating a new submission.
     */
    public function create(Request $request)
    {
        $this->authorize('create', BudgetSubmission::class);
        
        $budgetRequest = BudgetRequest::findOrFail($request->budget_request_id);
        return response()->json(['budget_request' => $budgetRequest]);
    }

    /**
     * Store a newly created submission.
     */
    public function store(Request $request)
    {
        $this->authorize('create', BudgetSubmission::class);

        $validated = $request->validate([
            'budget_request_id' => 'required|exists:budget_requests,id',
            'submission_content' => 'required|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        $user = auth()->user();
        $budgetRequest = BudgetRequest::findOrFail($validated['budget_request_id']);

        // Check if user owns this budget request
        if ($budgetRequest->user_id !== $user->id) {
            return response()->json(['message' => 'You can only submit for your own budget requests'], 403);
        }

        $documentPath = null;
        if ($request->hasFile('document')) {
            $documentPath = $request->file('document')->store('submissions', 'public');
        }

        $submission = BudgetSubmission::create([
            'user_id' => $user->id,
            'budget_request_id' => $validated['budget_request_id'],
            'submission_content' => $validated['submission_content'],
            'document_path' => $documentPath,
            'status' => 'pending',
            'submitted_at' => Carbon::now(),
        ]);

        return response()->json(['message' => 'Submission created successfully', 'submission' => $submission], 201);
    }

    /**
     * Display the specified submission.
     */
    public function show(BudgetSubmission $budgetSubmission)
    {
        $this->authorize('view', $budgetSubmission);
        return response()->json($budgetSubmission->load(['user', 'budgetRequest']));
    }

    /**
     * Show the form for editing the specified submission.
     */
    public function edit(BudgetSubmission $budgetSubmission)
    {
        $this->authorize('update', $budgetSubmission);
        return response()->json($budgetSubmission);
    }

    /**
     * Update the specified submission.
     */
    public function update(Request $request, BudgetSubmission $budgetSubmission)
    {
        $this->authorize('update', $budgetSubmission);

        $validated = $request->validate([
            'submission_content' => 'sometimes|string',
            'document' => 'nullable|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if (isset($validated['submission_content'])) {
            $budgetSubmission->submission_content = $validated['submission_content'];
        }

        if ($request->hasFile('document')) {
            $budgetSubmission->document_path = $request->file('document')->store('submissions', 'public');
        }

        $budgetSubmission->save();

        return response()->json(['message' => 'Submission updated successfully', 'submission' => $budgetSubmission]);
    }

    /**
     * Remove the specified submission.
     */
    public function destroy(BudgetSubmission $budgetSubmission)
    {
        $this->authorize('delete', $budgetSubmission);
        
        $budgetSubmission->delete();
        return response()->json(['message' => 'Submission deleted successfully']);
    }

    /**
     * Review submission at department level.
     */
    public function reviewDepartment(Request $request, BudgetSubmission $budgetSubmission)
    {
        $this->authorize('reviewDepartment', $budgetSubmission);

        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $budgetSubmission->update([
            'status' => 'department_reviewed',
            'department_feedback' => $validated['feedback'],
        ]);

        return response()->json(['message' => 'Submission reviewed by department', 'submission' => $budgetSubmission]);
    }

    /**
     * Review submission at admin level.
     */
    public function reviewAdmin(Request $request, BudgetSubmission $budgetSubmission)
    {
        $this->authorize('reviewAdmin', $budgetSubmission);

        $validated = $request->validate([
            'feedback' => 'required|string',
        ]);

        $budgetSubmission->update([
            'status' => 'admin_reviewed',
            'admin_feedback' => $validated['feedback'],
        ]);

        return response()->json(['message' => 'Submission reviewed by admin', 'submission' => $budgetSubmission]);
    }
}
