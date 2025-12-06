@component('mail::message')
# Budget Request Rejected - Department Level

Hello {{ $budgetRequest->user->name }},

Unfortunately, your budget request has been **rejected at the department level**.

## Request Details

- **Title:** {{ $budgetRequest->title }}
- **Amount:** ${{ number_format($budgetRequest->amount ?? $budgetRequest->requested_amount ?? 0, 2) }}
- **Department:** {{ $budgetRequest->department->name }}
- **Status:** Department Rejected
- **Reviewed At:** {{ $budgetRequest->department_reviewed_at->format('M d, Y H:i A') }}

## Feedback from Department Head

{{ $budgetRequest->department_feedback }}

## Next Steps

Please review the feedback above and contact your department head if you have any questions.

@component('mail::button', ['url' => route('budget-requests.show', $budgetRequest->id)])
View Request
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
