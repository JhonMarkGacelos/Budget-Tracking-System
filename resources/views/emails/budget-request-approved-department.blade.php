@component('mail::message')
# Budget Request Approved - Department Level

Hello {{ $budgetRequest->user->name }},

Your budget request has been **approved at the department level**.

## Request Details

- **Title:** {{ $budgetRequest->title }}
- **Amount:** ${{ number_format($budgetRequest->amount ?? $budgetRequest->requested_amount ?? 0, 2) }}
- **Department:** {{ $budgetRequest->department->name }}
- **Status:** Department Approved
- **Reviewed At:** {{ $budgetRequest->department_reviewed_at->format('M d, Y H:i A') }}

## Next Steps

@if($budgetRequest->amount > 20000)
This request exceeds $20,000 and will now be reviewed by the administrator for final approval.
@else
This request has been fully approved and will proceed to budget allocation.
@endif

@component('mail::button', ['url' => route('budget-requests.show', $budgetRequest->id)])
View Request
@endcomponent

Thank you for your submission.

Best regards,  
{{ config('app.name') }}
@endcomponent
