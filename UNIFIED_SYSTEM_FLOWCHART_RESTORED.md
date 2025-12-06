# Budget Tracking System - Unified System Flowchart
## Complete Vertical Flow - For Capstone/Research Paper

---

## Complete End-to-End System Workflow
### Optimized for Bond Paper (Portrait/Vertical Layout)

```mermaid
flowchart TD
    %% ========================================
    %% AUTHENTICATION & LOGIN PHASE
    %% ========================================
    START([BUDGET TRACKING SYSTEM<br/>System Access]) --> LOGIN[User Access Login Page]
    LOGIN --> ENTER_CRED[Enter Login Credentials<br/>Email & Password]
    ENTER_CRED --> VALIDATE_AUTH{Validate<br/>Credentials}
    
    VALIDATE_AUTH -->|Invalid Credentials| AUTH_FAIL[Authentication Failed<br/>Display Error Message]
    AUTH_FAIL --> RETRY_LOGIN{Retry<br/>Login?}
    RETRY_LOGIN -->|Yes| ENTER_CRED
    RETRY_LOGIN -->|No| END_DENIED([Access Denied<br/>Session Terminated])
    
    VALIDATE_AUTH -->|Valid Credentials| CREATE_TOKEN[Create API Token<br/>Laravel Sanctum<br/>Generate Bearer Token]
    CREATE_TOKEN --> STORE_SESSION[Store Session Data<br/>User Information<br/>Authentication Token]
    STORE_SESSION --> GET_USER_ROLE[Retrieve User Role<br/>Query Database]
    
    %% ========================================
    %% ROLE-BASED ROUTING
    %% ========================================
    GET_USER_ROLE --> ROLE_DECISION{Check<br/>User Role}
    
    ROLE_DECISION -->|Role: Admin| LOAD_ADMIN_DASH[Load Admin Dashboard<br/>Full System Access]
    ROLE_DECISION -->|Role: Department Head| LOAD_DEPT_DASH[Load Department Dashboard<br/>Department-Level Access]
    ROLE_DECISION -->|Role: Faculty| LOAD_FACULTY_DASH[Load Faculty Dashboard<br/>Personal Access Only]
    
    %% ========================================
    %% ADMIN WORKFLOW
    %% ========================================
    LOAD_ADMIN_DASH --> ADMIN_MAIN_MENU{ADMIN<br/>Select Function}
    
    ADMIN_MAIN_MENU -->|User Management| ADMIN_USER_MGMT[Manage System Users]
    ADMIN_USER_MGMT --> ADMIN_CREATE_USER[Create New User<br/>Any Role, Any Department]
    ADMIN_CREATE_USER --> ADMIN_USER_FORM[Enter User Details<br/>Name, Email, Password<br/>Role, Department]
    ADMIN_USER_FORM --> ADMIN_VALIDATE_USER{Validate<br/>User Input}
    ADMIN_VALIDATE_USER -->|Invalid| ADMIN_USER_ERROR[Display Validation Errors]
    ADMIN_USER_ERROR --> ADMIN_USER_FORM
    ADMIN_VALIDATE_USER -->|Valid| ADMIN_SAVE_USER[Save User to Database<br/>Hash Password with Bcrypt]
    ADMIN_SAVE_USER --> ADMIN_SEND_EMAIL[Send Welcome Email<br/>Login Credentials]
    ADMIN_SEND_EMAIL --> ADMIN_USER_SUCCESS[User Created Successfully]
    
    ADMIN_MAIN_MENU -->|Budget Allocation| ADMIN_BUDGET_ALLOC[Create Budget Allocation]
    ADMIN_BUDGET_ALLOC --> ADMIN_SELECT_DEPT[Select Target Department]
    ADMIN_SELECT_DEPT --> ADMIN_FISCAL_YEAR[Enter Fiscal Year<br/>Example: 2024-2025]
    ADMIN_FISCAL_YEAR --> ADMIN_ENTER_AMOUNT[Enter Allocation Amount<br/>Total Department Budget]
    ADMIN_ENTER_AMOUNT --> ADMIN_SET_STATUS[Set Allocation Status<br/>Active / Inactive / Pending]
    ADMIN_SET_STATUS --> ADMIN_ADD_NOTES[Add Optional Notes]
    ADMIN_ADD_NOTES --> ADMIN_VALIDATE_ALLOC{Validate<br/>Allocation Data}
    ADMIN_VALIDATE_ALLOC -->|Invalid| ADMIN_ALLOC_ERROR[Display Validation Errors]
    ADMIN_ALLOC_ERROR --> ADMIN_SELECT_DEPT
    ADMIN_VALIDATE_ALLOC -->|Valid| ADMIN_CHECK_DUP{Check<br/>Duplicate<br/>Allocation}
    ADMIN_CHECK_DUP -->|Exists| ADMIN_DUP_ERROR[Error: Allocation Exists<br/>for This Fiscal Year]
    ADMIN_DUP_ERROR --> ADMIN_SELECT_DEPT
    ADMIN_CHECK_DUP -->|Not Exists| ADMIN_SAVE_ALLOC[Save Budget Allocation<br/>Initialize Spent Amount = 0.00]
    ADMIN_SAVE_ALLOC --> ADMIN_NOTIFY_DEPT[Send Email Notification<br/>To: Department Head]
    ADMIN_NOTIFY_DEPT --> ADMIN_LOG_ALLOC[Create Audit Log Entry<br/>Action: allocation_created]
    ADMIN_LOG_ALLOC --> ADMIN_ALLOC_SUCCESS[Budget Allocation Created<br/>Monitoring Activated]
    
    ADMIN_MAIN_MENU -->|Final Approval| ADMIN_FINAL_APPROVAL[Review Pending Requests<br/>Department-Approved Requests]
    ADMIN_FINAL_APPROVAL --> ADMIN_LIST_PENDING[Display Pending Requests<br/>Status: department_approved]
    ADMIN_LIST_PENDING --> ADMIN_SELECT_REQUEST[Select Request to Review]
    ADMIN_SELECT_REQUEST --> ADMIN_VIEW_DETAILS[View Complete Details<br/>Title, Amount, Description<br/>Faculty Info<br/>Department Feedback]
    ADMIN_VIEW_DETAILS --> ADMIN_REVIEW_DECISION{Admin<br/>Decision}
    
    ADMIN_REVIEW_DECISION -->|Approve| ADMIN_APPROVE_REQ[Approve Request<br/>Final Approval]
    ADMIN_APPROVE_REQ --> ADMIN_UPDATE_APPROVED[Update Request Status<br/>Status: admin_approved]
    ADMIN_UPDATE_APPROVED --> ADMIN_ADD_FEEDBACK[Add Admin Feedback<br/>Optional Comments]
    ADMIN_ADD_FEEDBACK --> ADMIN_UPDATE_BUDGET[Update Budget Allocation<br/>Spent Amount += Request Amount]
    ADMIN_UPDATE_BUDGET --> ADMIN_CALC_UTIL[Calculate Budget Utilization<br/>Utilization % = Spent / Allocated × 100]
    ADMIN_CALC_UTIL --> ADMIN_SEND_NOTIF[Send Email Notifications<br/>To: Faculty + Department Head<br/>Subject: Request Approved]
    ADMIN_SEND_NOTIF --> ADMIN_LOG_APPROVAL[Create Audit Log<br/>Action: admin_approved<br/>Track Changes]
    ADMIN_LOG_APPROVAL --> ADMIN_APPROVAL_DONE[Request Fully Approved<br/>Budget Successfully Allocated]
    
    ADMIN_REVIEW_DECISION -->|Reject| ADMIN_REJECT_REQ[Reject Request<br/>Final Rejection]
    ADMIN_REJECT_REQ --> ADMIN_UPDATE_REJECTED[Update Request Status<br/>Status: admin_rejected]
    ADMIN_UPDATE_REJECTED --> ADMIN_ADD_REASON[Add Rejection Reason<br/>Required Feedback]
    ADMIN_ADD_REASON --> ADMIN_SEND_REJECT[Send Email Notifications<br/>To: Faculty + Department Head<br/>Include Rejection Reason]
    ADMIN_SEND_REJECT --> ADMIN_LOG_REJECT[Create Audit Log<br/>Action: admin_rejected<br/>Track Changes]
    ADMIN_LOG_REJECT --> ADMIN_REJECTION_DONE[Request Rejected by Admin<br/>Workflow Ended]
    
    ADMIN_MAIN_MENU -->|View Reports| ADMIN_VIEW_REPORTS[View All Submissions<br/>& Liquidation Reports]
    ADMIN_MAIN_MENU -->|Analytics| ADMIN_VIEW_ANALYTICS[System-Wide Analytics<br/>Statistics & Reports]
    ADMIN_MAIN_MENU -->|Audit Logs| ADMIN_VIEW_AUDIT[View Complete Audit Trail<br/>System Activity History]
    
    ADMIN_USER_SUCCESS --> ADMIN_CONTINUE_CHECK{Continue<br/>Admin Work?}
    ADMIN_ALLOC_SUCCESS --> ADMIN_CONTINUE_CHECK
    ADMIN_APPROVAL_DONE --> ADMIN_CONTINUE_CHECK
    ADMIN_REJECTION_DONE --> ADMIN_CONTINUE_CHECK
    ADMIN_VIEW_REPORTS --> ADMIN_CONTINUE_CHECK
    ADMIN_VIEW_ANALYTICS --> ADMIN_CONTINUE_CHECK
    ADMIN_VIEW_AUDIT --> ADMIN_CONTINUE_CHECK
    
    ADMIN_CONTINUE_CHECK -->|Yes| ADMIN_MAIN_MENU
    ADMIN_CONTINUE_CHECK -->|No| ADMIN_LOGOUT[Admin Logout<br/>Delete Sanctum Token<br/>Clear Session]
    ADMIN_LOGOUT --> SYSTEM_END
    
    %% ========================================
    %% DEPARTMENT HEAD WORKFLOW
    %% ========================================
    LOAD_DEPT_DASH --> DEPT_MAIN_MENU{DEPARTMENT HEAD<br/>Select Function}
    
    DEPT_MAIN_MENU -->|User Management| DEPT_USER_MGMT[Create Faculty Users<br/>In This Department Only]
    DEPT_USER_MGMT --> DEPT_FACULTY_FORM[Enter Faculty Details<br/>Name, Email, Password]
    DEPT_FACULTY_FORM --> DEPT_AUTO_ASSIGN[Auto-Assign Department<br/>From Dept Head's Department]
    DEPT_AUTO_ASSIGN --> DEPT_VALIDATE_USER{Validate<br/>Faculty Data}
    DEPT_VALIDATE_USER -->|Invalid| DEPT_USER_ERROR[Display Validation Errors]
    DEPT_USER_ERROR --> DEPT_FACULTY_FORM
    DEPT_VALIDATE_USER -->|Valid| DEPT_SAVE_FACULTY[Save Faculty User<br/>Role: faculty<br/>Hash Password]
    DEPT_SAVE_FACULTY --> DEPT_FACULTY_SUCCESS[Faculty User Created<br/>Send Welcome Email]
    
    DEPT_MAIN_MENU -->|Review Requests| DEPT_REVIEW_REQUESTS[Review Budget Requests<br/>From Faculty Members]
    DEPT_REVIEW_REQUESTS --> DEPT_LIST_REQUESTS[List Department Requests<br/>Status: submitted<br/>Filter by Department]
    DEPT_LIST_REQUESTS --> DEPT_SELECT_REQUEST[Select Request to Review]
    DEPT_SELECT_REQUEST --> DEPT_VIEW_DETAILS[View Request Details<br/>Title, Amount, Description<br/>Faculty Name & Info]
    DEPT_VIEW_DETAILS --> DEPT_REVIEW_DECISION{Department<br/>Head Decision}
    
    DEPT_REVIEW_DECISION -->|Approve| DEPT_APPROVE_REQ[Approve Request<br/>Department Level 1]
    DEPT_APPROVE_REQ --> DEPT_UPDATE_APPROVED[Update Request Status<br/>Status: department_approved]
    DEPT_UPDATE_APPROVED --> DEPT_ADD_FEEDBACK[Add Department Feedback<br/>Optional Comments]
    DEPT_ADD_FEEDBACK --> DEPT_RECORD_TIME[Record Review Timestamp<br/>department_reviewed_at]
    DEPT_RECORD_TIME --> DEPT_NOTIFY_FACULTY[Send Email to Faculty<br/>Subject: Dept Approved<br/>Next: Admin Review]
    DEPT_NOTIFY_FACULTY --> DEPT_NOTIFY_ADMIN[Send Email to Admin<br/>Queue for Final Approval]
    DEPT_NOTIFY_ADMIN --> DEPT_LOG_APPROVAL[Create Audit Log<br/>Action: department_approved]
    DEPT_LOG_APPROVAL --> DEPT_QUEUE_ADMIN[Queued for Admin Review<br/>Proceed to Admin Workflow]
    DEPT_QUEUE_ADMIN --> ADMIN_FINAL_APPROVAL
    
    DEPT_REVIEW_DECISION -->|Reject| DEPT_REJECT_REQ[Reject Request<br/>Department Level]
    DEPT_REJECT_REQ --> DEPT_UPDATE_REJECTED[Update Request Status<br/>Status: department_rejected]
    DEPT_UPDATE_REJECTED --> DEPT_ADD_REASON[Add Rejection Reason<br/>Required Feedback]
    DEPT_ADD_REASON --> DEPT_RECORD_REJECT_TIME[Record Review Timestamp<br/>department_reviewed_at]
    DEPT_RECORD_REJECT_TIME --> DEPT_NOTIFY_REJECT[Send Email to Faculty<br/>Include Rejection Reason]
    DEPT_NOTIFY_REJECT --> DEPT_LOG_REJECT[Create Audit Log<br/>Action: department_rejected]
    DEPT_LOG_REJECT --> DEPT_REJECTION_DONE[Request Rejected<br/>Workflow Ended]
    
    DEPT_MAIN_MENU -->|Review Submissions| DEPT_REVIEW_SUBS[Review Budget Submissions<br/>Financial Documents]
    DEPT_REVIEW_SUBS --> DEPT_LIST_SUBS[List Department Submissions<br/>Status: submitted]
    DEPT_LIST_SUBS --> DEPT_SELECT_SUB[Select Submission to Review]
    DEPT_SELECT_SUB --> DEPT_VIEW_SUB[View Submission Details<br/>Download Documents<br/>Review Content]
    DEPT_VIEW_SUB --> DEPT_SUB_DECISION{Review<br/>Documents}
    DEPT_SUB_DECISION -->|Approved| DEPT_SUB_APPROVE[Mark as Reviewed<br/>Status: department_reviewed]
    DEPT_SUB_APPROVE --> DEPT_SUB_FEEDBACK[Add Review Comments]
    DEPT_SUB_FEEDBACK --> DEPT_SUB_NOTIFY[Notify Admin<br/>For Final Review]
    DEPT_SUB_NOTIFY --> DEPT_SUB_DONE[Submission Reviewed<br/>Queued for Admin]
    DEPT_SUB_DECISION -->|Needs Revision| DEPT_SUB_REVISE[Request Revisions]
    DEPT_SUB_REVISE --> DEPT_SUB_NOTES[Add Revision Notes<br/>What Needs to Change]
    DEPT_SUB_NOTES --> DEPT_SUB_NOTIFY_FAC[Notify Faculty<br/>Request Updates]
    DEPT_SUB_NOTIFY_FAC --> DEPT_SUB_REVISION_DONE[Revision Requested<br/>Wait for Faculty]
    
    DEPT_MAIN_MENU -->|Review Liquidations| DEPT_REVIEW_LIQ[Review Liquidation Reports<br/>Closing Documents]
    DEPT_REVIEW_LIQ --> DEPT_LIST_LIQ[List Department Reports<br/>Status: submitted]
    DEPT_LIST_LIQ --> DEPT_SELECT_LIQ[Select Report to Review]
    DEPT_SELECT_LIQ --> DEPT_VIEW_LIQ[View Report Details<br/>Review Supporting Documents]
    DEPT_VIEW_LIQ --> DEPT_LIQ_DECISION{Review<br/>Liquidation}
    DEPT_LIQ_DECISION -->|Approved| DEPT_LIQ_APPROVE[Mark as Reviewed<br/>Status: department_reviewed]
    DEPT_LIQ_APPROVE --> DEPT_LIQ_COMMENT[Add Review Comments]
    DEPT_LIQ_COMMENT --> DEPT_LIQ_NOTIFY[Notify Admin<br/>For Final Review]
    DEPT_LIQ_NOTIFY --> DEPT_LIQ_DONE[Report Reviewed<br/>Queued for Admin]
    DEPT_LIQ_DECISION -->|Issues Found| DEPT_LIQ_ISSUE[Request Clarification]
    DEPT_LIQ_ISSUE --> DEPT_LIQ_NOTES[Add Issue Notes<br/>What Needs Clarification]
    DEPT_LIQ_NOTES --> DEPT_LIQ_NOTIFY_FAC[Notify Faculty<br/>Request Clarification]
    DEPT_LIQ_NOTIFY_FAC --> DEPT_LIQ_ISSUE_DONE[Clarification Requested<br/>Wait for Faculty]
    
    DEPT_MAIN_MENU -->|View Allocation| DEPT_VIEW_ALLOC[View Department Budget<br/>Allocation & Utilization]
    DEPT_MAIN_MENU -->|Analytics| DEPT_VIEW_ANALYTICS[Department Analytics<br/>Statistics & Reports]
    
    DEPT_FACULTY_SUCCESS --> DEPT_CONTINUE_CHECK{Continue<br/>Dept Work?}
    DEPT_REJECTION_DONE --> DEPT_CONTINUE_CHECK
    DEPT_SUB_DONE --> DEPT_CONTINUE_CHECK
    DEPT_SUB_REVISION_DONE --> DEPT_CONTINUE_CHECK
    DEPT_LIQ_DONE --> DEPT_CONTINUE_CHECK
    DEPT_LIQ_ISSUE_DONE --> DEPT_CONTINUE_CHECK
    DEPT_VIEW_ALLOC --> DEPT_CONTINUE_CHECK
    DEPT_VIEW_ANALYTICS --> DEPT_CONTINUE_CHECK
    
    DEPT_CONTINUE_CHECK -->|Yes| DEPT_MAIN_MENU
    DEPT_CONTINUE_CHECK -->|No| DEPT_LOGOUT[Department Logout<br/>Delete Sanctum Token<br/>Clear Session]
    DEPT_LOGOUT --> SYSTEM_END
    
    %% ========================================
    %% FACULTY WORKFLOW
    %% ========================================
    LOAD_FACULTY_DASH --> FACULTY_MAIN_MENU{FACULTY<br/>Select Function}
    
    FACULTY_MAIN_MENU -->|Create Request| FACULTY_CREATE_REQUEST[Create Budget Request<br/>New Submission]
    FACULTY_CREATE_REQUEST --> FACULTY_REQ_FORM[Budget Request Form]
    FACULTY_REQ_FORM --> FACULTY_ENTER_TITLE[Enter Request Title<br/>Brief Description]
    FACULTY_ENTER_TITLE --> FACULTY_ENTER_DESC[Enter Detailed Description<br/>Justification & Purpose]
    FACULTY_ENTER_DESC --> FACULTY_ENTER_AMOUNT[Enter Request Amount<br/>Budget Needed]
    FACULTY_ENTER_AMOUNT --> FACULTY_REVIEW_FORM[Review Form Data]
    FACULTY_REVIEW_FORM --> FACULTY_VALIDATE_REQ{Validate<br/>Request Input}
    FACULTY_VALIDATE_REQ -->|Invalid| FACULTY_REQ_ERROR[Display Validation Errors<br/>- Title Required<br/>- Description Required<br/>- Amount Must be Positive]
    FACULTY_REQ_ERROR --> FACULTY_REQ_FORM
    FACULTY_VALIDATE_REQ -->|Valid| FACULTY_CONFIRM{Confirm<br/>Submission?}
    FACULTY_CONFIRM -->|No| FACULTY_REQ_FORM
    FACULTY_CONFIRM -->|Yes| FACULTY_SUBMIT_REQ[Submit Budget Request<br/>Status: submitted]
    FACULTY_SUBMIT_REQ --> FACULTY_AUTO_DEPT[Auto-Assign Department<br/>From Faculty's Department ID]
    FACULTY_AUTO_DEPT --> FACULTY_TIMESTAMP[Record Submission Time<br/>created_at Timestamp]
    FACULTY_TIMESTAMP --> FACULTY_NOTIFY_DEPT[Send Email Notification<br/>To: Department Head<br/>New Request Submitted]
    FACULTY_NOTIFY_DEPT --> FACULTY_LOG_REQ[Create Audit Log Entry<br/>Action: budget_request_created]
    FACULTY_LOG_REQ --> FACULTY_REQ_SUCCESS[Request Submitted Successfully<br/>Status: Waiting Dept Review]
    FACULTY_REQ_SUCCESS --> DEPT_REVIEW_REQUESTS
    
    FACULTY_MAIN_MENU -->|Submit Documents| FACULTY_CREATE_SUB[Create Budget Submission<br/>Upload Financial Documents]
    FACULTY_CREATE_SUB --> FACULTY_SUB_FORM[Budget Submission Form]
    FACULTY_SUB_FORM --> FACULTY_SUB_TITLE[Enter Submission Title]
    FACULTY_SUB_TITLE --> FACULTY_SUB_DESC[Enter Description<br/>Purpose of Submission]
    FACULTY_SUB_DESC --> FACULTY_SUB_AMOUNT[Enter Amount<br/>Total Amount in Submission]
    FACULTY_SUB_AMOUNT --> FACULTY_UPLOAD_DOC[Upload Financial Document<br/>PDF, Excel, Image Files<br/>Supported Formats]
    FACULTY_UPLOAD_DOC --> FACULTY_VALIDATE_UPLOAD{Validate<br/>File Upload}
    FACULTY_VALIDATE_UPLOAD -->|Invalid| FACULTY_UPLOAD_ERROR[Display Upload Errors<br/>- File Size Too Large<br/>- Invalid File Type<br/>- Missing Required Fields]
    FACULTY_UPLOAD_ERROR --> FACULTY_SUB_FORM
    FACULTY_VALIDATE_UPLOAD -->|Valid| FACULTY_SAVE_FILE[Save File to Storage<br/>Generate File Path<br/>Store Document URL]
    FACULTY_SAVE_FILE --> FACULTY_SUBMIT_SUB[Submit Submission<br/>Status: submitted]
    FACULTY_SUBMIT_SUB --> FACULTY_NOTIFY_DEPT_SUB[Send Email Notification<br/>To: Department Head<br/>New Submission]
    FACULTY_NOTIFY_DEPT_SUB --> FACULTY_LOG_SUB[Create Audit Log Entry<br/>Action: budget_submission_created]
    FACULTY_LOG_SUB --> FACULTY_SUB_SUCCESS[Submission Created<br/>Status: Waiting Dept Review]
    FACULTY_SUB_SUCCESS --> DEPT_REVIEW_SUBS
    
    FACULTY_MAIN_MENU -->|Submit Report| FACULTY_CREATE_LIQ[Create Liquidation Report<br/>Upload Closing Documents]
    FACULTY_CREATE_LIQ --> FACULTY_LIQ_FORM[Liquidation Report Form]
    FACULTY_LIQ_FORM --> FACULTY_LIQ_TITLE[Enter Report Title]
    FACULTY_LIQ_TITLE --> FACULTY_LIQ_DESC[Enter Description<br/>Summary of Expenses]
    FACULTY_LIQ_DESC --> FACULTY_LIQ_AMOUNT[Enter Total Amount<br/>Total Liquidated Amount]
    FACULTY_LIQ_AMOUNT --> FACULTY_UPLOAD_LIQ[Upload Supporting Documents<br/>Receipts, Invoices, etc.]
    FACULTY_UPLOAD_LIQ --> FACULTY_VALIDATE_LIQ{Validate<br/>Report Input}
    FACULTY_VALIDATE_LIQ -->|Invalid| FACULTY_LIQ_ERROR[Display Validation Errors]
    FACULTY_LIQ_ERROR --> FACULTY_LIQ_FORM
    FACULTY_VALIDATE_LIQ -->|Valid| FACULTY_SAVE_LIQ[Save Documents to Storage]
    FACULTY_SAVE_LIQ --> FACULTY_SUBMIT_LIQ[Submit Report<br/>Status: submitted]
    FACULTY_SUBMIT_LIQ --> FACULTY_NOTIFY_DEPT_LIQ[Send Email Notification<br/>To: Department Head<br/>New Report]
    FACULTY_NOTIFY_DEPT_LIQ --> FACULTY_LOG_LIQ[Create Audit Log Entry<br/>Action: liquidation_report_created]
    FACULTY_LOG_LIQ --> FACULTY_LIQ_SUCCESS[Report Submitted<br/>Status: Waiting Dept Review]
    FACULTY_LIQ_SUCCESS --> DEPT_REVIEW_LIQ
    
    FACULTY_MAIN_MENU -->|View Status| FACULTY_VIEW_STATUS[View Request Status<br/>Check Approvals & Feedback]
    FACULTY_VIEW_STATUS --> FACULTY_LIST_REQUESTS[List All Personal Requests<br/>With Current Status]
    FACULTY_LIST_REQUESTS --> FACULTY_SELECT_VIEW[Select Request to View]
    FACULTY_SELECT_VIEW --> FACULTY_SHOW_DETAILS[Show Full Details<br/>- Current Status<br/>- Department Feedback<br/>- Admin Feedback<br/>- All Timestamps]
    FACULTY_SHOW_DETAILS --> FACULTY_STATUS_VIEWED[Status Information Viewed]
    
    FACULTY_MAIN_MENU -->|View History| FACULTY_VIEW_HISTORY[View Historical Data<br/>Past Requests & Submissions]
    FACULTY_VIEW_HISTORY --> FACULTY_SHOW_HISTORY[Display History<br/>- All Requests<br/>- All Submissions<br/>- All Reports<br/>- With Final Statuses]
    FACULTY_SHOW_HISTORY --> FACULTY_HISTORY_VIEWED[History Viewed]
    
    FACULTY_MAIN_MENU -->|View Statistics| FACULTY_VIEW_STATS[Personal Statistics<br/>Analytics & Summary]
    FACULTY_VIEW_STATS --> FACULTY_SHOW_STATS[Display Statistics<br/>- Total Requests Count<br/>- Approved Requests Count<br/>- Pending Requests Count<br/>- Rejected Requests Count<br/>- Total Amount Requested<br/>- Total Amount Approved]
    FACULTY_SHOW_STATS --> FACULTY_STATS_VIEWED[Statistics Viewed]
    
    FACULTY_STATUS_VIEWED --> FACULTY_CONTINUE_CHECK{Continue<br/>Faculty Work?}
    FACULTY_HISTORY_VIEWED --> FACULTY_CONTINUE_CHECK
    FACULTY_STATS_VIEWED --> FACULTY_CONTINUE_CHECK
    
    FACULTY_CONTINUE_CHECK -->|Yes| FACULTY_MAIN_MENU
    FACULTY_CONTINUE_CHECK -->|No| FACULTY_LOGOUT[Faculty Logout<br/>Delete Sanctum Token<br/>Clear Session]
    FACULTY_LOGOUT --> SYSTEM_END
    
    %% ========================================
    %% SYSTEM MONITORING & BACKGROUND PROCESSES
    %% ========================================
    ADMIN_APPROVAL_DONE --> TRIGGER_MONITORING[Trigger Budget Monitoring<br/>Background Process]
    TRIGGER_MONITORING --> FETCH_ALLOCATION[Fetch Department Allocation<br/>Get Allocated & Spent Amounts]
    FETCH_ALLOCATION --> CALC_UTILIZATION[Calculate Budget Utilization<br/>Utilization % = Spent / Allocated × 100<br/>Remaining = Allocated - Spent]
    CALC_UTILIZATION --> CHECK_UTIL_LEVEL{Check<br/>Utilization<br/>Level}
    
    CHECK_UTIL_LEVEL -->|Less than 50%| STATUS_OPTIMAL[Status: OPTIMAL<br/>Color Indicator: Green<br/>Budget Healthy]
    STATUS_OPTIMAL --> LOG_OPTIMAL[Log Status to Database<br/>No Action Required]
    
    CHECK_UTIL_LEVEL -->|50% to 80%| STATUS_WARNING[Status: HIGH USAGE<br/>Color Indicator: Yellow<br/>Budget Usage High]
    STATUS_WARNING --> SEND_WARNING_EMAIL[Send Warning Email<br/>To: Department Head<br/>Subject: Budget Usage Alert<br/>Body: 50-80% Utilized]
    SEND_WARNING_EMAIL --> LOG_WARNING[Log Warning Sent<br/>Create Audit Entry]
    
    CHECK_UTIL_LEVEL -->|80% to 95%| STATUS_CRITICAL[Status: CRITICAL<br/>Color Indicator: Orange<br/>Budget Nearly Exhausted]
    STATUS_CRITICAL --> SEND_CRITICAL_EMAIL[Send Critical Alert<br/>To: Department Head + Admin<br/>Subject: Critical Budget Alert<br/>Body: 80-95% Utilized]
    SEND_CRITICAL_EMAIL --> LOG_CRITICAL[Log Critical Alert<br/>Create Audit Entry]
    
    CHECK_UTIL_LEVEL -->|Greater than 95%| STATUS_DANGER[Status: DANGER<br/>Color Indicator: Red<br/>Budget Limit Reached]
    STATUS_DANGER --> SEND_URGENT_EMAIL[Send Urgent Alert<br/>To: Department Head + Admin<br/>Subject: URGENT Budget Limit<br/>Body: >95% Utilized<br/>Priority: HIGH]
    SEND_URGENT_EMAIL --> LOG_URGENT[Log Urgent Alert<br/>Create Audit Entry]
    LOG_URGENT --> CHECK_DEPLETED{Budget<br/>100%<br/>Depleted?}
    CHECK_DEPLETED -->|Yes| BLOCK_DEPT_REQUESTS[Block New Department Requests<br/>Department Cannot Submit<br/>Until Budget Increased]
    BLOCK_DEPT_REQUESTS --> NOTIFY_BLOCKED[Send Notification<br/>Department Budget Depleted<br/>Contact Admin]
    CHECK_DEPLETED -->|No| UPDATE_DASHBOARD
    
    LOG_OPTIMAL --> UPDATE_DASHBOARD[Update System Dashboard<br/>Display Current Status<br/>Show Utilization Indicators]
    LOG_WARNING --> UPDATE_DASHBOARD
    LOG_CRITICAL --> UPDATE_DASHBOARD
    NOTIFY_BLOCKED --> UPDATE_DASHBOARD
    
    UPDATE_DASHBOARD --> MONITORING_COMPLETE[Budget Monitoring Complete<br/>Continue System Operations]
    
    %% ========================================
    %% SYSTEM END & SESSION TERMINATION
    %% ========================================
    MONITORING_COMPLETE --> SYSTEM_END([SYSTEM WORKFLOW COMPLETE<br/>All Processes Finished])
    
    %% ========================================
    %% STYLING
    %% ========================================
    style START fill:#e3f2fd,stroke:#1976d2,stroke-width:4px,color:#000
    style VALIDATE_AUTH fill:#fff3e0,stroke:#f57c00,stroke-width:3px
    style ROLE_DECISION fill:#f3e5f5,stroke:#7b1fa2,stroke-width:3px
    
    style LOAD_ADMIN_DASH fill:#ffebee,stroke:#c62828,stroke-width:3px
    style LOAD_DEPT_DASH fill:#e0f7fa,stroke:#00838f,stroke-width:3px
    style LOAD_FACULTY_DASH fill:#e8f5e9,stroke:#2e7d32,stroke-width:3px
    
    style ADMIN_MAIN_MENU fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    style DEPT_MAIN_MENU fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    style FACULTY_MAIN_MENU fill:#f3e5f5,stroke:#7b1fa2,stroke-width:2px
    
    style ADMIN_REVIEW_DECISION fill:#fff3e0,stroke:#f57c00,stroke-width:2px
    style DEPT_REVIEW_DECISION fill:#fff3e0,stroke:#f57c00,stroke-width:2px
    
    style ADMIN_APPROVE_REQ fill:#c8e6c9,stroke:#43a047,stroke-width:2px
    style DEPT_APPROVE_REQ fill:#c8e6c9,stroke:#43a047,stroke-width:2px
    style ADMIN_APPROVAL_DONE fill:#81c784,stroke:#2e7d32,stroke-width:3px
    
    style ADMIN_REJECT_REQ fill:#ffcdd2,stroke:#e53935,stroke-width:2px
    style DEPT_REJECT_REQ fill:#ffcdd2,stroke:#e53935,stroke-width:2px
    style ADMIN_REJECTION_DONE fill:#e57373,stroke:#c62828,stroke-width:3px
    style DEPT_REJECTION_DONE fill:#e57373,stroke:#c62828,stroke-width:3px
    
    style STATUS_OPTIMAL fill:#c8e6c9,stroke:#43a047,stroke-width:2px
    style STATUS_WARNING fill:#fff9c4,stroke:#f9a825,stroke-width:2px
    style STATUS_CRITICAL fill:#ffe0b2,stroke:#ef6c00,stroke-width:2px
    style STATUS_DANGER fill:#ffcdd2,stroke:#e53935,stroke-width:2px
    style BLOCK_DEPT_REQUESTS fill:#ef9a9a,stroke:#c62828,stroke-width:2px
    
    style END_DENIED fill:#e57373,stroke:#c62828,stroke-width:3px
    style SYSTEM_END fill:#81c784,stroke:#2e7d32,stroke-width:4px
```

---

## System Architecture Diagram
### Complete Vertical Architecture - Optimized for Bond Paper

```mermaid
flowchart TD
    %% ========================================
    %% CLIENT/PRESENTATION LAYER
    %% ========================================
    START_ARCH([BUDGET TRACKING SYSTEM<br/>System Architecture]) --> CLIENT_LAYER[CLIENT/PRESENTATION LAYER]
    
    CLIENT_LAYER --> WEB_BROWSER[Web Browser Interface<br/>Chrome, Firefox, Safari, Edge]
    WEB_BROWSER --> FRONTEND_TECH[Frontend Technologies]
    FRONTEND_TECH --> HTML5[HTML5<br/>Page Structure & Content]
    HTML5 --> TAILWIND[Tailwind CSS<br/>Styling & Responsive Design]
    TAILWIND --> JAVASCRIPT[JavaScript<br/>Client-side Interactivity]
    JAVASCRIPT --> ALPINE[Alpine.js<br/>Reactive Components]
    ALPINE --> BLADE[Blade Templates<br/>Server-side Rendering<br/>Laravel Template Engine]
    
    CLIENT_LAYER --> API_CLIENT[API Client<br/>Mobile Apps / External Systems<br/>Third-party Integration]
    
    %% ========================================
    %% API GATEWAY LAYER
    %% ========================================
    BLADE --> GATEWAY_LAYER[API GATEWAY LAYER]
    API_CLIENT --> GATEWAY_LAYER
    
    GATEWAY_LAYER --> ROUTES[Laravel Routes<br/>api.php & web.php]
    ROUTES --> ROUTE_DETAIL[Route Configuration<br/>- RESTful API Endpoints<br/>- Resource Routes<br/>- Custom Routes<br/>- Route Groups]
    
    ROUTE_DETAIL --> MIDDLEWARE_LAYER[Middleware Layer<br/>Request Processing Pipeline]
    MIDDLEWARE_LAYER --> AUTH_MIDDLEWARE[Authentication Middleware<br/>Laravel Sanctum<br/>Token Validation]
    AUTH_MIDDLEWARE --> SANCTUM_DETAIL[Sanctum Features<br/>- API Token Generation<br/>- Token Storage<br/>- Token Validation<br/>- Token Revocation<br/>- SPA Authentication]
    
    SANCTUM_DETAIL --> OTHER_MIDDLEWARE[Other Middleware<br/>- CORS Handler<br/>- Rate Limiting<br/>- Request Logging<br/>- Input Sanitization<br/>- Exception Handler]
    
    %% ========================================
    %% APPLICATION/BUSINESS LOGIC LAYER
    %% ========================================
    OTHER_MIDDLEWARE --> APP_LAYER[APPLICATION/BUSINESS LOGIC LAYER]
    
    APP_LAYER --> CONTROLLERS[Controllers Layer<br/>Request Handlers]
    
    CONTROLLERS --> USER_CONTROLLER[UserController<br/>User Management<br/>CRUD Operations]
    USER_CONTROLLER --> USER_FUNCS[Functions:<br/>- index: List users<br/>- store: Create user<br/>- show: View user<br/>- update: Edit user<br/>- destroy: Delete user]
    
    CONTROLLERS --> DEPT_CONTROLLER[DepartmentController<br/>Department Management<br/>CRUD Operations]
    DEPT_CONTROLLER --> DEPT_FUNCS[Functions:<br/>- index: List departments<br/>- store: Create department<br/>- show: View department<br/>- update: Edit department<br/>- destroy: Delete department]
    
    CONTROLLERS --> BUDGET_REQ_CONTROLLER[BudgetRequestController<br/>Request Workflow Management]
    BUDGET_REQ_CONTROLLER --> BR_FUNCS[Functions:<br/>- index: List requests<br/>- store: Create request<br/>- show: View request<br/>- update: Edit request<br/>- destroy: Delete request<br/>- approveDepartment<br/>- rejectDepartment<br/>- approveAdmin<br/>- rejectAdmin]
    
    CONTROLLERS --> BUDGET_SUB_CONTROLLER[BudgetSubmissionController<br/>Document Management]
    BUDGET_SUB_CONTROLLER --> BS_FUNCS[Functions:<br/>- index: List submissions<br/>- store: Create submission<br/>- show: View submission<br/>- update: Edit submission<br/>- destroy: Delete submission<br/>- reviewDepartment<br/>- reviewAdmin]
    
    CONTROLLERS --> LIQ_REP_CONTROLLER[LiquidationReportController<br/>Report Processing]
    LIQ_REP_CONTROLLER --> LR_FUNCS[Functions:<br/>- index: List reports<br/>- store: Create report<br/>- show: View report<br/>- update: Edit report<br/>- destroy: Delete report<br/>- reviewDepartment<br/>- reviewAdmin]
    
    CONTROLLERS --> BUDGET_ALLOC_CONTROLLER[BudgetAllocationController<br/>Allocation Management]
    BUDGET_ALLOC_CONTROLLER --> BA_FUNCS[Functions:<br/>- index: List allocations<br/>- store: Create allocation<br/>- show: View allocation<br/>- update: Edit allocation<br/>- destroy: Delete allocation<br/>- statistics: Get stats]
    
    CONTROLLERS --> APPROVAL_CONTROLLER[ApprovalController<br/>Dashboard & Statistics]
    APPROVAL_CONTROLLER --> AC_FUNCS[Functions:<br/>- getPendingApprovals<br/>- getStatistics<br/>- getDashboardData]
    
    CONTROLLERS --> AUDIT_CONTROLLER[AuditLogController<br/>Activity Tracking]
    AUDIT_CONTROLLER --> AL_FUNCS[Functions:<br/>- index: List logs<br/>- show: View log details<br/>- summary: Get summary]
    
    CONTROLLERS --> ANALYTICS_CONTROLLER[AnalyticsController<br/>Reporting & Insights]
    ANALYTICS_CONTROLLER --> AN_FUNCS[Functions:<br/>- getOverview<br/>- getBudgetByDepartment<br/>- getRequestStatus<br/>- getSpendingTrends<br/>- getBudgetAllocationVsSpending<br/>- getDepartmentSpendingPercentage<br/>- getForecast<br/>- getTopDepartments<br/>- getApprovalRate<br/>- getFinancialReport]
    
    %% ========================================
    %% AUTHORIZATION LAYER
    %% ========================================
    USER_FUNCS --> POLICIES[Authorization Policies Layer<br/>Access Control]
    DEPT_FUNCS --> POLICIES
    BR_FUNCS --> POLICIES
    BS_FUNCS --> POLICIES
    LR_FUNCS --> POLICIES
    BA_FUNCS --> POLICIES
    AC_FUNCS --> POLICIES
    AL_FUNCS --> POLICIES
    AN_FUNCS --> POLICIES
    
    POLICIES --> USER_POLICY[UserPolicy<br/>User Authorization]
    USER_POLICY --> UP_RULES[Authorization Rules:<br/>- viewAny: List users<br/>- view: View specific user<br/>- create: Create user<br/>- update: Edit user<br/>- delete: Delete user<br/>Role-based: Admin & Dept Head]
    
    POLICIES --> BUDGET_REQ_POLICY[BudgetRequestPolicy<br/>Request Authorization]
    BUDGET_REQ_POLICY --> BRP_RULES[Authorization Rules:<br/>- viewAny: List requests<br/>- view: View request<br/>- create: Create request<br/>- update: Edit request<br/>- delete: Delete request<br/>- approveDepartment<br/>- approveAdmin<br/>Role & Department checks]
    
    POLICIES --> BUDGET_SUB_POLICY[BudgetSubmissionPolicy<br/>Submission Authorization]
    BUDGET_SUB_POLICY --> BSP_RULES[Authorization Rules:<br/>- viewAny: List submissions<br/>- view: View submission<br/>- create: Create submission<br/>- update: Edit submission<br/>- delete: Delete submission<br/>- reviewDepartment<br/>- reviewAdmin]
    
    POLICIES --> LIQ_REP_POLICY[LiquidationReportPolicy<br/>Report Authorization]
    LIQ_REP_POLICY --> LRP_RULES[Authorization Rules:<br/>- viewAny: List reports<br/>- view: View report<br/>- create: Create report<br/>- update: Edit report<br/>- delete: Delete report<br/>- reviewDepartment<br/>- reviewAdmin]
    
    %% ========================================
    %% MODELS/DOMAIN LAYER
    %% ========================================
    UP_RULES --> MODELS[Models/Domain Layer<br/>Business Entities]
    BRP_RULES --> MODELS
    BSP_RULES --> MODELS
    LRP_RULES --> MODELS
    
    MODELS --> USER_MODEL[User Model<br/>users table]
    USER_MODEL --> USER_ATTRS[Attributes:<br/>- id: Primary Key<br/>- name: Full Name<br/>- email: Unique Email<br/>- password: Hashed<br/>- role: enum<br/>- department_id: FK<br/>Relationships:<br/>- belongsTo: Department<br/>- hasMany: BudgetRequests<br/>- hasMany: BudgetSubmissions<br/>- hasMany: LiquidationReports<br/>- hasMany: AuditLogs]
    
    MODELS --> DEPT_MODEL[Department Model<br/>departments table]
    DEPT_MODEL --> DEPT_ATTRS[Attributes:<br/>- id: Primary Key<br/>- name: Unique Name<br/>- description: Text<br/>Relationships:<br/>- hasMany: Users<br/>- hasMany: BudgetRequests<br/>- hasMany: BudgetSubmissions<br/>- hasMany: LiquidationReports<br/>- hasMany: BudgetAllocations]
    
    MODELS --> BUDGET_REQ_MODEL[BudgetRequest Model<br/>budget_requests table]
    BUDGET_REQ_MODEL --> BR_ATTRS[Attributes:<br/>- id: Primary Key<br/>- title: String<br/>- description: Text<br/>- amount: Decimal<br/>- status: Enum<br/>- user_id: FK<br/>- department_id: FK<br/>- department_feedback: Text<br/>- department_reviewed_at<br/>- admin_feedback: Text<br/>- admin_reviewed_at<br/>Relationships:<br/>- belongsTo: User<br/>- belongsTo: Department<br/>Traits:<br/>- Filterable]
    
    MODELS --> BUDGET_SUB_MODEL[BudgetSubmission Model<br/>budget_submissions table]
    BUDGET_SUB_MODEL --> BS_ATTRS[Attributes:<br/>- id: Primary Key<br/>- title: String<br/>- description: Text<br/>- amount: Decimal<br/>- document_url: String<br/>- status: Enum<br/>- user_id: FK<br/>- department_id: FK<br/>- department_feedback: Text<br/>- admin_feedback: Text<br/>Relationships:<br/>- belongsTo: User<br/>- belongsTo: Department<br/>Traits:<br/>- Filterable]
    
    MODELS --> LIQ_REP_MODEL[LiquidationReport Model<br/>liquidation_reports table]
    LIQ_REP_MODEL --> LR_ATTRS[Attributes:<br/>- id: Primary Key<br/>- title: String<br/>- description: Text<br/>- amount: Decimal<br/>- document_url: String<br/>- status: Enum<br/>- user_id: FK<br/>- department_id: FK<br/>- department_feedback: Text<br/>- admin_feedback: Text<br/>Relationships:<br/>- belongsTo: User<br/>- belongsTo: Department<br/>Traits:<br/>- Filterable]
    
    MODELS --> BUDGET_ALLOC_MODEL[BudgetAllocation Model<br/>budget_allocations table]
    BUDGET_ALLOC_MODEL --> BA_ATTRS[Attributes:<br/>- id: Primary Key<br/>- department_id: FK<br/>- fiscal_year: String<br/>- allocated_amount: Decimal<br/>- spent_amount: Decimal<br/>- status: Enum<br/>- notes: Text<br/>Relationships:<br/>- belongsTo: Department<br/>Methods:<br/>- calculateUtilization<br/>- getRemainingBudget]
    
    MODELS --> AUDIT_LOG_MODEL[AuditLog Model<br/>audit_logs table]
    AUDIT_LOG_MODEL --> AL_ATTRS[Attributes:<br/>- id: Primary Key<br/>- user_id: FK<br/>- action: String<br/>- model_type: String<br/>- model_id: BigInt<br/>- old_values: JSON<br/>- new_values: JSON<br/>- ip_address: String<br/>- user_agent: String<br/>Relationships:<br/>- belongsTo: User<br/>- morphTo: Auditable]
    
    %% ========================================
    %% SERVICES/UTILITIES LAYER
    %% ========================================
    USER_ATTRS --> SERVICES[Services & Utilities Layer<br/>Business Logic Support]
    DEPT_ATTRS --> SERVICES
    BR_ATTRS --> SERVICES
    BS_ATTRS --> SERVICES
    LR_ATTRS --> SERVICES
    BA_ATTRS --> SERVICES
    AL_ATTRS --> SERVICES
    
    SERVICES --> MAIL_SERVICE[Mail Service<br/>Email Notifications]
    MAIL_SERVICE --> MAIL_CLASSES[Mailable Classes:<br/>- BudgetRequestApprovedDepartment<br/>- BudgetRequestRejectedDepartment<br/>- BudgetRequestApprovedAdmin<br/>- BudgetRequestRejectedAdmin<br/>Features:<br/>- Queue Support<br/>- Email Templates<br/>- Attachments<br/>- CC/BCC]
    
    SERVICES --> FILTER_TRAIT[Filterable Trait<br/>Advanced Search & Filtering]
    FILTER_TRAIT --> FILTER_FEATURES[Features:<br/>- Status Filtering<br/>- Date Range Filtering<br/>- Amount Range Filtering<br/>- Department Filtering<br/>- Full-text Search<br/>- Dynamic Query Building<br/>- Pagination Support]
    
    SERVICES --> QUEUE_SERVICE[Queue Service<br/>Background Job Processing]
    QUEUE_SERVICE --> QUEUE_FEATURES[Features:<br/>- Email Queue<br/>- Job Retries<br/>- Failed Job Handling<br/>- Job Priorities<br/>- Delayed Jobs<br/>Database Driver]
    
    SERVICES --> CACHE_SERVICE[Cache Service<br/>Performance Optimization]
    CACHE_SERVICE --> CACHE_FEATURES[Features:<br/>- Query Result Caching<br/>- Analytics Caching<br/>- Session Storage<br/>- Cache Tags<br/>- Cache Duration: 5-15 min<br/>Driver: Redis/Memcached]
    
    SERVICES --> STORAGE_SERVICE[Storage Service<br/>File Management]
    STORAGE_SERVICE --> STORAGE_FEATURES[Features:<br/>- File Upload<br/>- File Download<br/>- File Deletion<br/>- Disk: public/local<br/>- Cloud: AWS S3 Support<br/>- File Validation<br/>- Size Limits]
    
    %% ========================================
    %% DATA PERSISTENCE LAYER
    %% ========================================
    MAIL_CLASSES --> DATA_LAYER[DATA PERSISTENCE LAYER]
    FILTER_FEATURES --> DATA_LAYER
    QUEUE_FEATURES --> DATA_LAYER
    CACHE_FEATURES --> DATA_LAYER
    STORAGE_FEATURES --> DATA_LAYER
    
    DATA_LAYER --> PRIMARY_DB[(MySQL Database<br/>Primary Data Store<br/>Port: 3307)]
    PRIMARY_DB --> DB_TABLES[Database Tables:<br/>- users<br/>- departments<br/>- budget_requests<br/>- budget_submissions<br/>- liquidation_reports<br/>- budget_allocations<br/>- audit_logs<br/>- personal_access_tokens<br/>- cache<br/>- jobs<br/>- failed_jobs]
    
    DB_TABLES --> DB_FEATURES[Database Features:<br/>- Foreign Key Constraints<br/>- Indexes for Performance<br/>- Timestamps<br/>- Soft Deletes<br/>- Transactions<br/>- Migration Version Control<br/>- Database Seeding]
    
    DATA_LAYER --> FILE_STORAGE[(File Storage<br/>Document Repository)]
    FILE_STORAGE --> FILE_STRUCTURE[Storage Structure:<br/>- storage/app/public<br/>- Submissions Documents<br/>- Liquidation Documents<br/>- User Uploads<br/>- Generated Reports]
    
    DATA_LAYER --> CACHE_STORE[(Cache Store<br/>Redis/Memcached)]
    CACHE_STORE --> CACHE_DATA[Cached Data:<br/>- Session Data<br/>- API Tokens<br/>- Query Results<br/>- Analytics Data<br/>- User Preferences]
    
    %% ========================================
    %% EXTERNAL SERVICES LAYER
    %% ========================================
    DB_FEATURES --> EXTERNAL[EXTERNAL SERVICES LAYER]
    FILE_STRUCTURE --> EXTERNAL
    CACHE_DATA --> EXTERNAL
    
    EXTERNAL --> SMTP_SERVER[SMTP Server<br/>Email Delivery]
    SMTP_SERVER --> SMTP_DETAIL[Configuration:<br/>- Mailtrap for Development<br/>- Gmail/SendGrid for Production<br/>- Port: 587 TLS<br/>- Authentication Required<br/>- Rate Limiting<br/>- Delivery Tracking]
    
    EXTERNAL --> CLOUD_STORAGE[Cloud Storage<br/>File Backup & CDN]
    CLOUD_STORAGE --> CLOUD_DETAIL[Services:<br/>- AWS S3<br/>- Local Storage<br/>- File Backup<br/>- CDN Integration<br/>- Automatic Sync<br/>- Version Control]
    
    %% ========================================
    %% INFRASTRUCTURE LAYER
    %% ========================================
    SMTP_DETAIL --> INFRASTRUCTURE[INFRASTRUCTURE LAYER<br/>Development & Deployment]
    CLOUD_DETAIL --> INFRASTRUCTURE
    
    INFRASTRUCTURE --> LARAVEL_CORE[Laravel Framework 11.x<br/>PHP 8.2+]
    LARAVEL_CORE --> LARAVEL_FEATURES[Core Features:<br/>- Eloquent ORM<br/>- Routing System<br/>- Middleware Pipeline<br/>- Service Container<br/>- Dependency Injection<br/>- Event System<br/>- Artisan CLI<br/>- Blade Engine]
    
    INFRASTRUCTURE --> COMPOSER[Composer<br/>Dependency Manager]
    COMPOSER --> COMPOSER_PACKAGES[Key Packages:<br/>- laravel/sanctum<br/>- laravel/tinker<br/>- phpunit/phpunit<br/>- fakerphp/faker<br/>- guzzlehttp/guzzle<br/>- monolog/monolog]
    
    INFRASTRUCTURE --> VITE[Vite<br/>Asset Bundler & Build Tool]
    VITE --> VITE_FEATURES[Features:<br/>- Fast Hot Reload<br/>- CSS Processing<br/>- JS Bundling<br/>- Asset Optimization<br/>- Production Build<br/>- Development Server]
    
    INFRASTRUCTURE --> VERSION_CONTROL[Git<br/>Version Control System]
    VERSION_CONTROL --> GIT_FEATURES[Features:<br/>- Source Code Management<br/>- Branch Management<br/>- Commit History<br/>- Collaboration<br/>- Deployment Hooks]
    
    INFRASTRUCTURE --> TESTING[Testing Framework<br/>PHPUnit & Feature Tests]
    TESTING --> TEST_TYPES[Test Types:<br/>- Unit Tests<br/>- Feature Tests<br/>- Integration Tests<br/>- API Tests<br/>- Browser Tests<br/>- Code Coverage]
    
    %% ========================================
    %% SECURITY LAYER
    %% ========================================
    LARAVEL_FEATURES --> SECURITY[SECURITY LAYER<br/>System Protection]
    
    SECURITY --> AUTH_SECURITY[Authentication Security]
    AUTH_SECURITY --> AUTH_SEC_FEATURES[Features:<br/>- Bcrypt Password Hashing<br/>- Token-based Auth<br/>- CSRF Protection<br/>- XSS Prevention<br/>- SQL Injection Prevention<br/>- Rate Limiting<br/>- Session Management]
    
    SECURITY --> AUTHORIZATION_SECURITY[Authorization Security]
    AUTHORIZATION_SECURITY --> AUTHZ_SEC_FEATURES[Features:<br/>- Role-based Access Control<br/>- Policy-based Authorization<br/>- Department-level Isolation<br/>- Resource Ownership<br/>- Permission Checking<br/>- Middleware Guards]
    
    SECURITY --> DATA_SECURITY[Data Security]
    DATA_SECURITY --> DATA_SEC_FEATURES[Features:<br/>- Database Encryption<br/>- HTTPS Enforcement<br/>- Input Validation<br/>- Output Sanitization<br/>- File Upload Validation<br/>- Audit Trail Logging<br/>- Sensitive Data Masking]
    
    %% ========================================
    %% MONITORING & LOGGING LAYER
    %% ========================================
    AUTH_SEC_FEATURES --> MONITORING[MONITORING & LOGGING LAYER<br/>System Observability]
    AUTHZ_SEC_FEATURES --> MONITORING
    DATA_SEC_FEATURES --> MONITORING
    
    MONITORING --> APP_LOGGING[Application Logging<br/>Monolog]
    APP_LOGGING --> LOG_CHANNELS[Log Channels:<br/>- stack: Multiple channels<br/>- single: Single file<br/>- daily: Daily rotation<br/>- slack: Slack notifications<br/>- syslog: System log<br/>- errorlog: PHP error log<br/>Log Levels:<br/>- DEBUG, INFO, WARNING<br/>- ERROR, CRITICAL]
    
    MONITORING --> AUDIT_LOGGING[Audit Logging<br/>Activity Tracking]
    AUDIT_LOGGING --> AUDIT_FEATURES[Tracked Events:<br/>- User Actions<br/>- CRUD Operations<br/>- Status Changes<br/>- Approvals/Rejections<br/>- Login/Logout<br/>- Failed Attempts<br/>Storage: Database<br/>Retention: Permanent]
    
    MONITORING --> PERFORMANCE_MONITORING[Performance Monitoring]
    PERFORMANCE_MONITORING --> PERF_METRICS[Metrics:<br/>- Query Performance<br/>- Response Times<br/>- Memory Usage<br/>- Cache Hit Rates<br/>- API Endpoints Stats<br/>- Error Rates<br/>Tools: Laravel Telescope]
    
    MONITORING --> BUDGET_MONITORING[Budget Utilization Monitoring]
    BUDGET_MONITORING --> BUDGET_ALERTS[Alert System:<br/>- < 50%: Optimal Green<br/>- 50-80%: Warning Yellow<br/>- 80-95%: Critical Orange<br/>- > 95%: Danger Red<br/>Notifications:<br/>- Email Alerts<br/>- Dashboard Indicators<br/>- Real-time Updates]
    
    %% ========================================
    %% SYSTEM END
    %% ========================================
    LOG_CHANNELS --> ARCH_END([COMPLETE SYSTEM ARCHITECTURE<br/>All Layers Integrated])
    AUDIT_FEATURES --> ARCH_END
    PERF_METRICS --> ARCH_END
    BUDGET_ALERTS --> ARCH_END
    
    %% ========================================
    %% STYLING
    %% ========================================
    style START_ARCH fill:#e3f2fd,stroke:#1976d2,stroke-width:4px
    style CLIENT_LAYER fill:#f3e5f5,stroke:#7b1fa2,stroke-width:3px
    style GATEWAY_LAYER fill:#fff3e0,stroke:#f57c00,stroke-width:3px
    style APP_LAYER fill:#ffebee,stroke:#c62828,stroke-width:3px
    style POLICIES fill:#e1f5fe,stroke:#0277bd,stroke-width:3px
    style MODELS fill:#e8f5e9,stroke:#2e7d32,stroke-width:3px
    style SERVICES fill:#fff9c4,stroke:#f9a825,stroke-width:3px
    style DATA_LAYER fill:#c8e6c9,stroke:#2e7d32,stroke-width:3px
    style EXTERNAL fill:#ffe0b2,stroke:#ef6c00,stroke-width:3px
    style INFRASTRUCTURE fill:#f3e5f5,stroke:#7b1fa2,stroke-width:3px
    style SECURITY fill:#ffcdd2,stroke:#c62828,stroke-width:3px
    style MONITORING fill:#b2ebf2,stroke:#00838f,stroke-width:3px
    style ARCH_END fill:#81c784,stroke:#2e7d32,stroke-width:4px
    
    style PRIMARY_DB fill:#4479a1,color:#fff,stroke:#1565c0,stroke-width:3px
    style FILE_STORAGE fill:#4caf50,color:#fff,stroke:#2e7d32,stroke-width:3px
    style CACHE_STORE fill:#d32f2f,color:#fff,stroke:#c62828,stroke-width:3px
```

---

## Flowchart Legend

### Status Colors
- 🔵 **Blue** - System Start/Entry Points
- 🟣 **Purple** - Decision Points/User Choices
- 🔴 **Red** - Admin Functions/Highest Authority
- 🟡 **Cyan** - Department Head Functions
- 🟢 **Green** - Faculty Functions/Success States
- 🟠 **Orange** - Validation/Error States
- ⚫ **Dark Red** - Rejection/End States

### Process Flow
1. **Authentication** → User logs in with credentials
2. **Role-Based Routing** → System determines user role and loads appropriate dashboard
3. **Admin Workflow** → Full system access, budget allocation, final approval
4. **Department Workflow** → Department-level reviews and approvals
5. **Faculty Workflow** → Submit requests, documents, and reports
6. **Monitoring** → Real-time budget utilization monitoring with alerts
7. **Session End** → User logout and session termination

### Key Decision Points
- **Authentication Check** - Valid/Invalid credentials
- **Role Decision** - Admin/Department Head/Faculty
- **Approval Decisions** - Approve/Reject at each level
- **Budget Utilization** - 4 alert levels (Optimal/Warning/Critical/Danger)

---

## Architecture Layers Explanation

### 1. Client/Presentation Layer
- **Web Browser Interface** - User-facing frontend
- **Frontend Technologies** - HTML5, Tailwind CSS, JavaScript, Alpine.js, Blade
- **API Client** - Mobile/external system integration

### 2. API Gateway Layer
- **Laravel Routes** - RESTful API endpoint definitions
- **Middleware** - Request processing pipeline
- **Authentication** - Sanctum token-based auth
- **Request Filtering** - CORS, rate limiting, logging

### 3. Application/Business Logic Layer
- **Controllers** - 9 controllers handling all operations
- **Policies** - 4 authorization policies for access control
- **Models** - 7 Eloquent models representing business entities
- **Services** - Mail, Queue, Cache, Storage, Filtering

### 4. Data Persistence Layer
- **MySQL Database** - Primary relational data store (Port 3307)
- **File Storage** - Document repository for uploads
- **Cache Store** - Redis/Memcached for performance

### 5. External Services Layer
- **SMTP Server** - Email delivery (Mailtrap/Gmail)
- **Cloud Storage** - AWS S3 / Local storage for file backup

### 6. Infrastructure Layer
- **Laravel Framework** - PHP 8.2+ framework
- **Composer** - PHP dependency management
- **Vite** - Frontend asset bundling
- **Git** - Version control
- **PHPUnit** - Testing framework

### 7. Security Layer
- **Authentication Security** - Bcrypt hashing, token auth, CSRF protection
- **Authorization Security** - RBAC, policies, department isolation
- **Data Security** - Encryption, validation, audit logging

### 8. Monitoring & Logging Layer
- **Application Logging** - Monolog with multiple channels
- **Audit Logging** - Complete activity tracking
- **Performance Monitoring** - Query optimization, metrics
- **Budget Monitoring** - Real-time utilization alerts

---

## Notes for Capstone/Research Paper

This unified flowchart represents the complete end-to-end workflow of the Budget Tracking System in a single, continuous vertical flow. The diagram is optimized for bond paper printing in portrait orientation and shows:

1. **Complete User Journey** - From login to logout
2. **All Three User Roles** - Admin, Department Head, and Faculty workflows
3. **Full Approval Process** - Two-level approval (Department → Admin)
4. **Budget Monitoring** - Real-time utilization tracking and alerts
5. **All System Functions** - User management, budget allocation, document submissions, liquidation reports, and analytics

The flowchart demonstrates the hierarchical nature of the approval workflow and shows how data flows through the system from faculty submission to final admin approval, with automatic budget monitoring and email notifications at each stage.

The architecture diagram demonstrates a modern, secure, and scalable Laravel-based system with comprehensive features including authentication, authorization, audit logging, email notifications, file management, and real-time monitoring across 8 distinct architectural layers.
