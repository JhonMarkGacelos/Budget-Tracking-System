# Budget Approval & Allocation Workflow Guide

## Overview
The budget tracking system implements a two-level approval workflow for budget requests and allows admins to allocate budgets to departments.

---

## 1. BUDGET REQUEST APPROVAL WORKFLOW

### Request Flow
1. **Faculty submits request** → Status: `submitted`
2. **Department head reviews** → Approve/Reject at department level
3. **Admin reviews approved requests** → Final approval/rejection

### Approval Levels

#### Level 1: Department Head Approval
**Who Can Approve:** Department Head (for requests in their department)

**When Available:** When request status is `submitted`

**Actions:**
- **Approve:** Moves request to `department_approved` status
- **Reject:** Moves request to `department_rejected` with feedback

**Location:** Budget Request Details Page → "Department Approval" section

**Code:**
```javascript
POST /api/budget-requests/{id}/approve-department
POST /api/budget-requests/{id}/reject-department
```

#### Level 2: Admin Approval
**Who Can Approve:** Admin/System Administrator

**When Available:** When request status is `department_approved`

**Purpose:** Final approval for budget execution

**Actions:**
- **Approve:** Moves request to `admin_approved` status
- **Reject:** Moves request to `admin_rejected` with feedback

**Location:** Budget Request Details Page → "Admin Approval" section

**Code:**
```javascript
POST /api/budget-requests/{id}/approve-admin
POST /api/budget-requests/{id}/reject-admin
```

### Request Status Timeline
```
submitted 
    ↓ (Department Head Action)
department_approved OR department_rejected
    ↓ (if approved, Admin Action)
admin_approved OR admin_rejected
    ↓
Final Status (Ready for Budget Allocation)
```

---

## 2. BUDGET ALLOCATION

### What is Budget Allocation?
Budget Allocation is the process of assigning available funds to departments for a specific fiscal year.

### Creating a Budget Allocation

#### Who Can Create?
- Admin/System Administrator only

#### How to Create
1. Navigate to **Budget Allocations** page
2. Click **"Create Allocation"** button
3. Fill in the form:
   - **Department:** Select the department
   - **Fiscal Year:** Enter fiscal year (e.g., 2024-2025)
   - **Allocated Amount:** Enter total budget for this department
   - **Status:** Choose Active/Inactive/Pending
   - **Notes:** Optional additional information

4. Click **"Create Allocation"** to submit

#### API Endpoint
```
POST /api/budget-allocations
Body: {
    "department_id": 1,
    "fiscal_year": "2024-2025",
    "allocated_amount": 50000.00,
    "status": "active",
    "notes": "Optional notes"
}
```

### Viewing Budget Allocations

#### For Admins
- View all department allocations
- See utilization percentages
- Access detailed allocation views

#### For Department Heads
- View only their department's allocation
- Monitor budget utilization
- Track spending vs allocated amount

#### Allocation Details Include
- **Allocated Amount:** Total budget assigned
- **Spent Amount:** Total approved requests against this allocation
- **Remaining:** Available budget (Allocated - Spent)
- **Utilization %:** Percentage of budget used
- **Status Indicators:**
  - Green (Optimal): < 50% utilization
  - Yellow (High): 50-80% utilization
  - Red (Critical): > 80% utilization

---

## 3. APPROVAL WORKFLOW EXAMPLE

### Scenario: Faculty Request for $5,000
1. **Faculty submits request** with title and amount
   - Status: `submitted`
   
2. **Department Head reviews** the request
   - Opens Budget Requests → Views Request Details
   - Sees "Department Approval" section with Approve/Reject buttons
   - Clicks "Approve" (or rejects with feedback)
   - Status changes to: `department_approved`
   
3. **Admin reviews approved request**
   - Sees "Admin Approval" section (only if dept approved)
   - Clicks "Approve" for final approval
   - Status changes to: `admin_approved`
   - Request is now ready for allocation
   
4. **Budget is allocated** against department's allocation
   - Dept allocation "Spent Amount" increases by $5,000
   - Utilization percentage updates automatically

---

## 4. KEY RULES & CONSTRAINTS

### Approval Rules
- **Only Pending Requests Show Approval Buttons:** Already approved/rejected requests don't show action buttons
- **Department Head Can Only Approve Own Department:** Cannot approve requests from other departments
- **Admin Cannot Approve Submitted Requests Directly:** Must wait for department approval first
- **Feedback Required for Rejection:** Both levels require feedback when rejecting

### Allocation Rules
- **One Allocation Per Department Per Fiscal Year:** Cannot have duplicate allocations
- **Only Admins Can Create Allocations:** Department heads can only view
- **Status Affects Visibility:** Inactive allocations may not be available for new requests
- **Utilization Tracking:** System automatically tracks spending against allocations

---

## 5. USER INTERFACE LOCATIONS

### For Faculty
- **My Dashboard:** View my requests and approvals status
- **Budget Requests → My Requests:** See all my submissions
- **Budget Request Details:** View status and feedback

### For Department Head
- **Department Dashboard:** 
  - "Pending Requests for Review" table
  - Quick action buttons to approve/reject
- **Budget Allocations:** View department allocation and utilization
- **Budget Request Details:** Full approval interface

### For Admin
- **Admin Dashboard:**
  - Total Requests count
  - Pending Approvals count
  - Recent Activity log
- **Budget Allocations:** Create/manage all allocations
- **Budget Request Details:** View and approve all requests
- **Quick Actions:** Links to manage users, requests, and allocations

---

## 6. API ENDPOINTS REFERENCE

### Approval Endpoints
```
POST   /api/budget-requests/{id}/approve-department
POST   /api/budget-requests/{id}/reject-department
POST   /api/budget-requests/{id}/approve-admin
POST   /api/budget-requests/{id}/reject-admin
```

### Allocation Endpoints
```
GET    /api/budget-allocations              # List all
POST   /api/budget-allocations              # Create new
GET    /api/budget-allocations/{id}         # View details
PUT    /api/budget-allocations/{id}         # Update
DELETE /api/budget-allocations/{id}         # Delete
GET    /api/budget-allocations/statistics   # Get stats
```

---

## 7. TROUBLESHOOTING

### Approval Buttons Not Showing?
- Check if request status is `submitted` (for department) or `department_approved` (for admin)
- Verify user role and department assignment
- Check browser console for errors

### Can't Create Allocation?
- Verify you're logged in as admin
- Check that the department exists
- Ensure allocated amount is greater than 0

### Utilization Not Updating?
- Allocations update when requests are approved
- Refresh the page to see latest numbers
- Check that approved requests have correct amounts

---

## 8. STATUS VALUES

```
submitted              - Initial submission by faculty
department_approved   - Approved by department head
department_rejected   - Rejected by department head
admin_approved        - Final approval by admin
admin_rejected        - Rejected by admin
```

