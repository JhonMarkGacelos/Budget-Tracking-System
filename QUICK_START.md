# Budget Tracking System - Quick Start Guide

## Project Overview

This is a complete Laravel-based Budget Tracking System with:
- ✅ Hierarchical role-based access (Admin, Department Head, Faculty)
- ✅ Budget request workflow (Faculty → Department → Admin approval)
- ✅ Financial submissions and liquidation reports
- ✅ MySQL database with comprehensive migrations
- ✅ RESTful API with token authentication (Laravel Sanctum)
- ✅ Authorization policies for fine-grained access control
- ✅ 6 predefined departments/colleges

## What's Been Implemented

### 1. Database & Models
- ✅ Users table with roles (admin, department, faculty)
- ✅ Departments table with 6 colleges
- ✅ BudgetRequests with multi-stage approval workflow
- ✅ BudgetSubmissions for financial documents
- ✅ LiquidationReports for final accounting
- ✅ Personal Access Tokens (Sanctum) for API authentication

### 2. Controllers (5 Total)
- **UserController**: User management, creation, deletion
- **BudgetRequestController**: Full CRUD + approval/rejection at department & admin levels
- **BudgetSubmissionController**: Submission management with department/admin review
- **LiquidationReportController**: Report management with multi-level review
- **ApprovalController**: Dashboard statistics and pending approvals

### 3. Authorization Policies (4 Total)
- **UserPolicy**: Role-based user visibility and management
- **BudgetRequestPolicy**: Access control for budget workflows
- **BudgetSubmissionPolicy**: Submission visibility and review permissions
- **LiquidationReportPolicy**: Report visibility and review permissions

### 4. API Routes (routes/api.php)
- Full RESTful API for all resources
- Login/logout endpoints
- Approval/rejection endpoints
- Review endpoints for submissions and reports
- Dashboard statistics endpoints

### 5. Database Seeders
- DepartmentSeeder: Creates 6 colleges
- AdminUserSeeder: Creates default admin account

## Quick Start

### 1. Start the Server
```bash
cd /Users/jhonmarkgacelos/Desktop/projects/bunget_tracking_system
php artisan serve
```

### 2. Login
Use any API client (Postman, cURL, Insomnia):
```bash
POST http://localhost:8000/api/login
{
  "email": "admin@budgettracking.com",
  "password": "admin@123456"
}
```

You'll receive a Bearer token to use for subsequent requests.

### 3. Create Users
```bash
POST http://localhost:8000/api/users
Authorization: Bearer {token}
{
  "name": "Department Head",
  "email": "dept@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "department",
  "department_id": 1
}
```

### 4. Submit Budget Request (as Faculty)
```bash
POST http://localhost:8000/api/budget-requests
Authorization: Bearer {faculty_token}
{
  "title": "Lab Equipment",
  "description": "Need new microscopes",
  "amount": 5000
}
```

### 5. Approve at Department Level
```bash
POST http://localhost:8000/api/budget-requests/1/approve-department
Authorization: Bearer {dept_token}
```

### 6. Approve at Admin Level
```bash
POST http://localhost:8000/api/budget-requests/1/approve-admin
Authorization: Bearer {admin_token}
```

## Departments Available

1. College of Graduate Studies
2. College of Nursing and Health Sciences
3. College of Engineering
4. College of Education
5. College of Arts and Sciences
6. College of Industrial Technology

## User Roles & Permissions

### Admin
- Create any user (Admin or Department Head)
- View all users, budget requests, submissions
- Approve/reject budget requests
- Final review of submissions and reports
- Access system statistics

### Department Head
- Create Faculty users in their department
- View faculty in their department
- View budget requests from their department
- Approve/reject budget requests
- Review submissions and reports
- Access department statistics

### Faculty/Staff
- Submit budget requests
- Submit financial documents
- Submit liquidation reports
- View only their own submissions

## Key Features

### Role-Based Visibility
- Admin sees everything
- Department heads see only their department data
- Faculty see only their own submissions

### Multi-Stage Approval
1. Faculty submits budget request
2. Department head reviews and approves/rejects
3. If approved, sent to Admin for final review
4. Admin can approve or reject

### Document Management
- Upload support for PDFs and documents
- Track submission status at each level
- Feedback from both department and admin

### Status Tracking
Budget Requests: pending → department_approved → admin_approved
Submissions: pending → department_reviewed → admin_reviewed
Reports: pending → department_reviewed → admin_reviewed

## File Locations

```
bunget_tracking_system/
├── app/
│   ├── Http/Controllers/
│   │   ├── UserController.php
│   │   ├── BudgetRequestController.php
│   │   ├── BudgetSubmissionController.php
│   │   ├── LiquidationReportController.php
│   │   └── ApprovalController.php
│   ├── Models/
│   │   ├── User.php
│   │   ├── Department.php
│   │   ├── BudgetRequest.php
│   │   ├── BudgetSubmission.php
│   │   └── LiquidationReport.php
│   └── Policies/
│       ├── UserPolicy.php
│       ├── BudgetRequestPolicy.php
│       ├── BudgetSubmissionPolicy.php
│       └── LiquidationReportPolicy.php
├── database/
│   ├── migrations/
│   └── seeders/
├── routes/
│   └── api.php
├── .env
└── README.md
```

## Environment Configuration

The `.env` file is pre-configured for MySQL:
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=budget_tracking_system
DB_USERNAME=root
DB_PASSWORD=
```

## Testing the Full Workflow

### Step 1: Create Department Head
Login as Admin, create a department head for College of Engineering:
```bash
POST /api/users
{
  "name": "Dr. Smith",
  "email": "smith@college-eng.com",
  "password": "pass1234",
  "password_confirmation": "pass1234",
  "role": "department",
  "department_id": 3
}
```

### Step 2: Create Faculty Member
Still as Admin, create a faculty member:
```bash
POST /api/users
{
  "name": "Prof. Johnson",
  "email": "johnson@college-eng.com",
  "password": "pass1234",
  "password_confirmation": "pass1234",
  "role": "faculty",
  "department_id": 3
}
```

### Step 3: Login as Faculty
```bash
POST /api/login
{
  "email": "johnson@college-eng.com",
  "password": "pass1234"
}
```

### Step 4: Submit Budget Request
```bash
POST /api/budget-requests
Authorization: Bearer {faculty_token}
{
  "title": "Research Equipment",
  "description": "Need advanced laboratory testing equipment",
  "amount": 15000
}
```

### Step 5: Login as Department Head
```bash
POST /api/login
{
  "email": "smith@college-eng.com",
  "password": "pass1234"
}
```

### Step 6: Review and Approve at Department Level
```bash
POST /api/budget-requests/1/approve-department
Authorization: Bearer {dept_token}
```

### Step 7: Login as Admin
```bash
POST /api/login
{
  "email": "admin@budgettracking.com",
  "password": "admin@123456"
}
```

### Step 8: Final Approval
```bash
POST /api/budget-requests/1/approve-admin
Authorization: Bearer {admin_token}
```

## API Documentation

For complete API documentation with all endpoints, parameters, and examples, see `README.md`.

## Support & Troubleshooting

### Database Issues
If migrations fail, ensure MySQL is running and the database exists:
```bash
mysql -u root -e "CREATE DATABASE IF NOT EXISTS budget_tracking_system;"
php artisan migrate:refresh --seed
```

### Authentication Issues
- Always include the Authorization header: `Authorization: Bearer {token}`
- Tokens are obtained from the login endpoint
- Each user role has different access levels

### Permission Issues
Check that your user has the correct role and department assignment for the action you're trying to perform.

## Next Steps

You can extend this system with:
- Email notifications for approvals
- File storage (AWS S3, etc.)
- Advanced reporting and analytics
- Dashboard UI (Vue.js, React, etc.)
- Audit logging
- Batch approval features
- Budget tracking over time

## Notes

- All passwords are hashed using Bcrypt
- API uses token-based authentication (Sanctum)
- All endpoints require authentication except login
- Role-based access is enforced at the controller level via policies
- Department-specific data is automatically filtered based on user role
