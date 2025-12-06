# Budget Tracking System - System Architecture
## For Capstone/Research Paper

---

## System Architecture Diagram

```mermaid
flowchart TD
    START([Budget Tracking System<br/>Architecture]) --> CLIENT[Client Layer<br/>Web Browser<br/>Admin, Dept Head, Faculty]
    
    CLIENT --> FRONTEND[Frontend<br/>HTML5, Tailwind, JS, Blade]
    
    FRONTEND --> API[API Gateway<br/>Laravel Routes<br/>Sanctum Auth]
    
    API --> CONTROLLERS[Application Controllers<br/>User, Budget, Analytics]
    
    CONTROLLERS --> AUTH[Authorization<br/>4 Policies + RBAC]
    
    CONTROLLERS --> MODELS[7 Models<br/>User, Department, Requests<br/>Submissions, Reports, Allocations]
    
    CONTROLLERS --> SERVICES[Services<br/>Email, Queue, Storage]
    
    CONTROLLERS --> WORKFLOW[Two-Level Approval<br/>Dept Head → Admin]
    
    WORKFLOW --> MONITORING[Budget Monitoring<br/>4-Level Alert System]
    
    MONITORING --> ALERTS[Green <50%<br/>Yellow 50-80%<br/>Orange 80-95%<br/>Red >95%]
    
    MODELS --> DATABASE[(MySQL Database<br/>11 Tables)]
    
    SERVICES --> SMTP[SMTP Email]
    SERVICES --> FILES[(File Storage)]
    
    CONTROLLERS --> LOGS[Monitoring & Audit Logs]
    
    DATABASE --> END([Complete System])
    FILES --> END
    AUTH --> END
    LOGS --> END
    ALERTS --> END

    %% Styling
    style START fill:#e3f2fd,stroke:#1976d2,stroke-width:2px
    style CLIENT fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    style FRONTEND fill:#fff3e0,stroke:#f57c00,stroke-width:2px
    style API fill:#ffe0b2,stroke:#ef6c00,stroke-width:2px
    style CONTROLLERS fill:#ffebee,stroke:#c62828,stroke-width:2px
    style AUTH fill:#ffcdd2,stroke:#c62828,stroke-width:2px
    style MODELS fill:#e8f5e9,stroke:#2e7d32,stroke-width:2px
    style SERVICES fill:#fff9c4,stroke:#f9a825,stroke-width:2px
    style WORKFLOW fill:#e1f5fe,stroke:#0277bd,stroke-width:2px
    style MONITORING fill:#b2ebf2,stroke:#00838f,stroke-width:2px
    style DATABASE fill:#4479a1,color:#fff,stroke:#1565c0,stroke-width:2px
    style FILES fill:#4caf50,color:#fff,stroke:#2e7d32,stroke-width:2px
    style LOGS fill:#b2ebf2,stroke:#00838f,stroke-width:2px
    style END fill:#81c784,stroke:#2e7d32,stroke-width:2px
```

---

## Key Components

### 1. Presentation Layer
**Users:** Admin (full access), Department Head (dept management), Faculty (personal requests)  
**Tech:** HTML5, Tailwind CSS, JavaScript, Alpine.js, Blade Templates

### 2. API Gateway
**Laravel Routes** - RESTful endpoints  
**Sanctum Auth** - API token security

### 3. Application Layer
**9 Controllers:** User, Department, BudgetRequest, BudgetSubmission, LiquidationReport, BudgetAllocation, Approval, AuditLog, Analytics

### 4. Authorization
**4 Policies** - Role-based access control (RBAC)  
**Roles:** Admin, Department Head, Faculty

### 5. Domain Models
**7 Eloquent Models:** User, Department, BudgetRequest, BudgetSubmission, LiquidationReport, BudgetAllocation, AuditLog

### 6. Two-Level Approval Workflow
**Level 1:** Department Head approval  
**Level 2:** Admin final approval

### 7. Budget Monitoring
**4 Alert Levels:**
- Green (<50%) - Optimal
- Yellow (50-80%) - Warning
- Orange (80-95%) - Critical
- Red (>95%) - Danger

**Action:** Auto email notifications

### 8. Services
- Email notifications
- Background job queue
- File storage

### 9. Data Persistence
**MySQL** - 11 tables  
**File Storage** - Documents & uploads

### 10. Monitoring
- Application logs (Monolog)
- Audit logs (user activity)
- Budget alerts (email)

---

## Technology Stack

**Backend:** Laravel 11.x, PHP 8.2+, MySQL  
**Frontend:** HTML5, Tailwind CSS, JavaScript, Alpine.js, Blade  
**Auth:** Laravel Sanctum (API Tokens)  
**Tools:** Composer, Vite, Git, PHPUnit

---

## Key Features

✅ Three-tier role system  
✅ Two-level approval workflow  
✅ Real-time budget monitoring  
✅ Complete audit trail  
✅ Automated email notifications  
✅ Document management  
✅ Analytics & forecasting
