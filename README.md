# Budget Tracking System - Laravel

A comprehensive hierarchical budget tracking system built with Laravel that manages budget requests, financial submissions, and approvals following a defined workflow.

## System Architecture

### User Roles

1. **Admin**: Highest authority with full access to all users, departments, and budget requests
2. **Department Head**: Manages faculty in their department and reviews submissions for their department
3. **Faculty/Staff**: Submits budget requests, liquidation reports, and financial documents

### Departments

The system includes six predefined colleges/departments:
- College of Graduate Studies
- College of Nursing and Health Sciences
- College of Engineering
- College of Education
- College of Arts and Sciences
- College of Industrial Technology

## Workflow

### Budget Request Workflow
1. Faculty submits → Department reviews → Admin gives final approval
2. Status flow: `pending` → `department_approved` → `admin_approved`
3. At any stage: rejection possible with feedback

### Submission & Liquidation Report Workflow
1. Faculty submits financial documents
2. Department reviews with feedback
3. Admin conducts final review

## Installation & Setup

### Prerequisites
- PHP 8.2+
- MySQL 8.0+
- Composer

### Step 1: Environment Configuration
The `.env` file is already configured for MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=budget_tracking_system
DB_USERNAME=root
DB_PASSWORD=
```

### Step 2: Start the Application
```bash
php artisan serve
```

The API will be available at `http://localhost:8000`

## Default Admin User

- **Email**: admin@budgettracking.com
- **Password**: admin@123456

## API Endpoints

### Authentication

#### Login
```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@budgettracking.com",
  "password": "admin@123456"
}
```

Response includes a Bearer token for subsequent requests.

#### Logout
```bash
POST /api/logout
Authorization: Bearer {token}
```

### User Management

#### List Users (Admin/Department Head)
```bash
GET /api/users
Authorization: Bearer {token}
```

#### Create User (Admin/Department Head)
```bash
POST /api/users
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "faculty",
  "department_id": 1
}
```

#### Get User
```bash
GET /api/users/{id}
Authorization: Bearer {token}
```

#### Update User
```bash
PUT /api/users/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "name": "Updated Name",
  "email": "newemail@example.com"
}
```

#### Delete User (Admin only)
```bash
DELETE /api/users/{id}
Authorization: Bearer {token}
```

### Budget Requests

#### List Budget Requests
```bash
GET /api/budget-requests
Authorization: Bearer {token}
```
- Admin: sees all requests
- Department Head: sees requests from their department
- Faculty: sees their own requests

#### Create Budget Request (Faculty only)
```bash
POST /api/budget-requests
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Equipment Purchase",
  "description": "Need to purchase laboratory equipment",
  "amount": 5000.00
}
```

#### Get Budget Request
```bash
GET /api/budget-requests/{id}
Authorization: Bearer {token}
```

#### Update Budget Request (Pending requests only)
```bash
PUT /api/budget-requests/{id}
Authorization: Bearer {token}
Content-Type: application/json

{
  "title": "Updated Title",
  "description": "Updated description",
  "amount": 6000.00
}
```

#### Delete Budget Request (Pending requests only)
```bash
DELETE /api/budget-requests/{id}
Authorization: Bearer {token}
```

### Department Level Approvals

#### Approve at Department Level
```bash
POST /api/budget-requests/{id}/approve-department
Authorization: Bearer {token}
```

#### Reject at Department Level
```bash
POST /api/budget-requests/{id}/reject-department
Authorization: Bearer {token}
Content-Type: application/json

{
  "feedback": "Requires more details"
}
```

### Admin Level Approvals

#### Approve at Admin Level
```bash
POST /api/budget-requests/{id}/approve-admin
Authorization: Bearer {token}
```

#### Reject at Admin Level
```bash
POST /api/budget-requests/{id}/reject-admin
Authorization: Bearer {token}
Content-Type: application/json

{
  "feedback": "Does not meet requirements"
}
```

### Budget Submissions

#### List Submissions
```bash
GET /api/budget-submissions
Authorization: Bearer {token}
```

#### Create Submission (Faculty only)
```bash
POST /api/budget-submissions
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "budget_request_id": 1,
  "submission_content": "Detailed submission content",
  "document": <file> (optional)
}
```

#### Get Submission
```bash
GET /api/budget-submissions/{id}
Authorization: Bearer {token}
```

#### Review at Department Level
```bash
POST /api/budget-submissions/{id}/review-department
Authorization: Bearer {token}
Content-Type: application/json

{
  "feedback": "Additional details needed"
}
```

#### Review at Admin Level
```bash
POST /api/budget-submissions/{id}/review-admin
Authorization: Bearer {token}
Content-Type: application/json

{
  "feedback": "Approved as submitted"
}
```

### Liquidation Reports

#### List Reports
```bash
GET /api/liquidation-reports
Authorization: Bearer {token}
```

#### Create Report (Faculty only)
```bash
POST /api/liquidation-reports
Authorization: Bearer {token}
Content-Type: multipart/form-data

{
  "budget_request_id": 1,
  "report_content": "Detailed liquidation report",
  "document": <file> (optional)
}
```

#### Get Report
```bash
GET /api/liquidation-reports/{id}
Authorization: Bearer {token}
```

#### Review at Department Level
```bash
POST /api/liquidation-reports/{id}/review-department
Authorization: Bearer {token}
Content-Type: application/json

{
  "feedback": "Needs clarification"
}
```

#### Review at Admin Level
```bash
POST /api/liquidation-reports/{id}/review-admin
Authorization: Bearer {token}
Content-Type: application/json

{
  "feedback": "Report accepted"
}
```

### Approvals & Dashboard

#### Get Pending Approvals
```bash
GET /api/approvals/pending
Authorization: Bearer {token}
```

Returns pending items for the current user based on their role.

#### Get Approval Statistics
```bash
GET /api/approvals/statistics
Authorization: Bearer {token}
```

Returns dashboard statistics based on user role.

## Authorization & Visibility

### Role-Based Access Control

**Admin Access:**
- View all users and requests
- Create any user
- Approve/reject all budget requests
- Review all submissions and reports
- Access dashboard statistics

**Department Head Access:**
- View users in their department only
- Create faculty users in their department
- Approve/reject budget requests from their department
- Review submissions and reports from their department
- Access department statistics

**Faculty/Staff Access:**
- Submit budget requests
- Submit financial documents and liquidation reports
- View only their own submissions
- Cannot create or manage users

## File Structure

### Controllers
- `app/Http/Controllers/UserController.php`
- `app/Http/Controllers/BudgetRequestController.php`
- `app/Http/Controllers/BudgetSubmissionController.php`
- `app/Http/Controllers/LiquidationReportController.php`
- `app/Http/Controllers/ApprovalController.php`

### Models
- `app/Models/User.php`
- `app/Models/Department.php`
- `app/Models/BudgetRequest.php`
- `app/Models/BudgetSubmission.php`
- `app/Models/LiquidationReport.php`

### Policies
- `app/Policies/UserPolicy.php`
- `app/Policies/BudgetRequestPolicy.php`
- `app/Policies/BudgetSubmissionPolicy.php`
- `app/Policies/LiquidationReportPolicy.php`

### Routes
- `routes/api.php`

### Database
- Migrations in `database/migrations/`
- Seeders in `database/seeders/`

## License

This project is open-source and available under the MIT license.
