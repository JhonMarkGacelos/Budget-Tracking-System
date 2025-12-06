@component('mail::message')
# Budget Request Approved - Final

Hello {{ $budgetRequest->user->name }},

Your budget request has been **approved by the administrator**.

## Request Details

- **Title:** {{ $budgetRequest->title }}
- **Amount:** ${{ number_format($budgetRequest->amount ?? $budgetRequest->requested_amount ?? 0, 2) }}
- **Department:** {{ $budgetRequest->department->name }}
- **Status:** Approved
- **Reviewed At:** {{ $budgetRequest->admin_reviewed_at->format('M d, Y H:i A') }}

## Next Steps

Your request has been fully approved and is now ready for budget allocation. You can view the status in your dashboard.

@component('mail::button', ['url' => route('budget-requests.show', $budgetRequest->id)])
View Request
@endcomponent

Thank you for your submission.

Best regards,  
{{ config('app.name') }}
@endcomponent
