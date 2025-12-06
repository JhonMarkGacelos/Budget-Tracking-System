@component('mail::message')
# Budget Request Rejected - Final

Hello {{ $budgetRequest->user->name }},

Unfortunately, your budget request has been **rejected by the administrator**.

## Request Details

- **Title:** {{ $budgetRequest->title }}
- **Amount:** ${{ number_format($budgetRequest->amount ?? $budgetRequest->requested_amount ?? 0, 2) }}
- **Department:** {{ $budgetRequest->department->name }}
- **Status:** Rejected
- **Reviewed At:** {{ $budgetRequest->admin_reviewed_at->format('M d, Y H:i A') }}

## Feedback from Administrator

{{ $budgetRequest->admin_feedback ?? 'No additional feedback provided.' }}

## Next Steps

Please review the feedback above and contact the administrator if you have any questions or would like to submit a revised request.

@component('mail::button', ['url' => route('budget-requests.show', $budgetRequest->id)])
View Request
@endcomponent

Best regards,  
{{ config('app.name') }}
@endcomponent
