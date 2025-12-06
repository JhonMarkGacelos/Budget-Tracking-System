# Budget Tracking System - Process Flowcharts

## 1. Budget Request Approval Workflow

```mermaid
flowchart TD
    START([Faculty User Logs In]) --> CREATE[Faculty Creates<br/>Budget Request]
    CREATE --> SUBMIT[Submit Request<br/>Status: submitted]
    
    SUBMIT --> DEPT_NOTIFY{Email Notification<br/>to Department Head}
    DEPT_NOTIFY --> DEPT_REVIEW[Department Head<br/>Reviews Request]
    
    DEPT_REVIEW --> DEPT_DECISION{Department<br/>Decision}
    DEPT_DECISION -->|Approve| DEPT_APPROVE[Status: department_approved<br/>Add Feedback]
    DEPT_DECISION -->|Reject| DEPT_REJECT[Status: department_rejected<br/>Add Feedback + Reason]
    
    DEPT_REJECT --> EMAIL_DEPT_REJECT[Email Notification<br/>to Faculty]
    EMAIL_DEPT_REJECT --> END_REJECTED([End: Request Rejected<br/>at Department Level])
    
    DEPT_APPROVE --> EMAIL_DEPT_APPROVE[Email Notification<br/>to Faculty & Admin]
    EMAIL_DEPT_APPROVE --> ADMIN_REVIEW[Admin Reviews<br/>Approved Request]
    
    ADMIN_REVIEW --> ADMIN_DECISION{Admin<br/>Decision}
    ADMIN_DECISION -->|Approve| ADMIN_APPROVE[Status: admin_approved<br/>Add Feedback]
    ADMIN_DECISION -->|Reject| ADMIN_REJECT[Status: admin_rejected<br/>Add Feedback + Reason]
    
    ADMIN_REJECT --> EMAIL_ADMIN_REJECT[Email Notification<br/>to Faculty & Department]
    EMAIL_ADMIN_REJECT --> END_ADMIN_REJECT([End: Request Rejected<br/>by Admin])
    
    ADMIN_APPROVE --> EMAIL_ADMIN_APPROVE[Email Notification<br/>to Faculty & Department]
    EMAIL_ADMIN_APPROVE --> UPDATE_ALLOCATION[Update Budget Allocation<br/>Spent Amount += Request Amount]
    UPDATE_ALLOCATION --> AUDIT_LOG[Create Audit Log Entry]
    AUDIT_LOG --> END_APPROVED([End: Request Fully Approved<br/>Budget Allocated])
    
    style START fill:#e3f2fd
    style SUBMIT fill:#fff3e0
    style DEPT_APPROVE fill:#c8e6c9
    style DEPT_REJECT fill:#ffcdd2
    style ADMIN_APPROVE fill:#a5d6a7
    style ADMIN_REJECT fill:#ef9a9a
    style END_APPROVED fill:#81c784
    style END_REJECTED fill:#e57373
    style END_ADMIN_REJECT fill:#e57373
```

## 2. Budget Submission Review Workflow

```mermaid
flowchart TD
    START([Faculty User Logs In]) --> CREATE[Faculty Creates<br/>Budget Submission]
    CREATE --> UPLOAD[Upload Financial<br/>Documents]
    UPLOAD --> SUBMIT[Submit Submission<br/>Status: submitted]
    
    SUBMIT --> NOTIFY_DEPT[Notify Department Head]
    NOTIFY_DEPT --> DEPT_REVIEW[Department Head<br/>Reviews Submission]
    
    DEPT_REVIEW --> DEPT_CHECK{Review<br/>Documents}
    DEPT_CHECK -->|Approved| DEPT_APPROVE[Status: department_reviewed<br/>Add Feedback]
    DEPT_CHECK -->|Needs Revision| DEPT_REVISION[Request Revisions<br/>Add Feedback]
    
    DEPT_REVISION --> NOTIFY_FACULTY_REV[Notify Faculty]
    NOTIFY_FACULTY_REV --> UPDATE_DOC[Faculty Updates<br/>Documents]
    UPDATE_DOC --> DEPT_REVIEW
    
    DEPT_APPROVE --> NOTIFY_ADMIN[Notify Admin]
    NOTIFY_ADMIN --> ADMIN_REVIEW[Admin Reviews<br/>Submission]
    
    ADMIN_REVIEW --> ADMIN_CHECK{Final<br/>Review}
    ADMIN_CHECK -->|Approved| ADMIN_APPROVE[Status: admin_reviewed<br/>Mark as Complete]
    ADMIN_CHECK -->|Needs Revision| ADMIN_REVISION[Request Revisions<br/>Send Back to Faculty]
    
    ADMIN_REVISION --> UPDATE_DOC
    
    ADMIN_APPROVE --> AUDIT[Create Audit Log]
    AUDIT --> END([End: Submission<br/>Approved])
    
    style START fill:#e3f2fd
    style SUBMIT fill:#fff3e0
    style DEPT_APPROVE fill:#c8e6c9
    style ADMIN_APPROVE fill:#a5d6a7
    style END fill:#81c784
    style DEPT_REVISION fill:#fff9c4
    style ADMIN_REVISION fill:#fff9c4
```

## 3. Liquidation Report Review Workflow

```mermaid
flowchart TD
    START([Faculty User Logs In]) --> CREATE[Faculty Creates<br/>Liquidation Report]
    CREATE --> DETAILS[Enter Report Details<br/>Amount, Description]
    DETAILS --> UPLOAD[Upload Supporting<br/>Documents]
    UPLOAD --> SUBMIT[Submit Report<br/>Status: submitted]
    
    SUBMIT --> NOTIFY_DEPT[Notify Department Head]
    NOTIFY_DEPT --> DEPT_REVIEW[Department Head<br/>Reviews Report]
    
    DEPT_REVIEW --> DEPT_VERIFY{Verify<br/>Liquidation}
    DEPT_VERIFY -->|Approved| DEPT_APPROVE[Status: department_reviewed<br/>Add Comments]
    DEPT_VERIFY -->|Issues Found| DEPT_ISSUE[Request Clarification<br/>Add Comments]
    
    DEPT_ISSUE --> NOTIFY_FACULTY[Notify Faculty]
    NOTIFY_FACULTY --> CLARIFY[Faculty Provides<br/>Clarification]
    CLARIFY --> DEPT_REVIEW
    
    DEPT_APPROVE --> NOTIFY_ADMIN[Notify Admin]
    NOTIFY_ADMIN --> ADMIN_REVIEW[Admin Final Review]
    
    ADMIN_REVIEW --> ADMIN_VERIFY{Admin<br/>Verification}
    ADMIN_VERIFY -->|Approved| ADMIN_APPROVE[Status: admin_reviewed<br/>Mark Complete]
    ADMIN_VERIFY -->|Issues Found| ADMIN_ISSUE[Request Additional Info<br/>Send Back]
    
    ADMIN_ISSUE --> CLARIFY
    
    ADMIN_APPROVE --> UPDATE_RECORDS[Update Financial<br/>Records]
    UPDATE_RECORDS --> AUDIT[Create Audit Log]
    AUDIT --> CLOSE[Close Report]
    CLOSE --> END([End: Liquidation<br/>Complete])
    
    style START fill:#e3f2fd
    style SUBMIT fill:#fff3e0
    style DEPT_APPROVE fill:#c8e6c9
    style ADMIN_APPROVE fill:#a5d6a7
    style END fill:#81c784
    style DEPT_ISSUE fill:#ffcdd2
    style ADMIN_ISSUE fill:#ffcdd2
```

## 4. User Authentication Flow

```mermaid
flowchart TD
    START([User Visits System]) --> LOGIN_PAGE[Navigate to<br/>Login Page]
    LOGIN_PAGE --> ENTER_CREDS[Enter Email<br/>& Password]
    
    ENTER_CREDS --> SUBMIT_LOGIN[Submit Login Form<br/>POST /api/login]
    SUBMIT_LOGIN --> VALIDATE{Validate<br/>Credentials}
    
    VALIDATE -->|Invalid| LOGIN_ERROR[Display Error Message]
    LOGIN_ERROR --> LOGIN_PAGE
    
    VALIDATE -->|Valid| CHECK_ROLE[Verify User Role]
    CHECK_ROLE --> CREATE_TOKEN[Create Sanctum<br/>API Token]
    CREATE_TOKEN --> STORE_TOKEN[Store Token<br/>in Session]
    
    STORE_TOKEN --> ROLE_CHECK{User Role?}
    ROLE_CHECK -->|Admin| ADMIN_DASH[Redirect to<br/>Admin Dashboard]
    ROLE_CHECK -->|Department Head| DEPT_DASH[Redirect to<br/>Department Dashboard]
    ROLE_CHECK -->|Faculty| FACULTY_DASH[Redirect to<br/>Faculty Dashboard]
    
    ADMIN_DASH --> ADMIN_FEATURES[Access Admin Features<br/>- All Users<br/>- All Departments<br/>- All Requests<br/>- Budget Allocations]
    
    DEPT_DASH --> DEPT_FEATURES[Access Department Features<br/>- Department Users<br/>- Department Requests<br/>- Approvals<br/>- Reviews]
    
    FACULTY_DASH --> FACULTY_FEATURES[Access Faculty Features<br/>- Own Requests<br/>- Own Submissions<br/>- Own Reports]
    
    ADMIN_FEATURES --> ACTIVITY[Perform Actions]
    DEPT_FEATURES --> ACTIVITY
    FACULTY_FEATURES --> ACTIVITY
    
    ACTIVITY --> LOGOUT_CHECK{Logout?}
    LOGOUT_CHECK -->|No| ACTIVITY
    LOGOUT_CHECK -->|Yes| LOGOUT[POST /api/logout<br/>Delete Token]
    LOGOUT --> END([End Session])
    
    style START fill:#e3f2fd
    style CREATE_TOKEN fill:#c8e6c9
    style ADMIN_DASH fill:#ff6b6b,color:#fff
    style DEPT_DASH fill:#4ecdc4
    style FACULTY_DASH fill:#95e1d3
    style LOGOUT fill:#ffcdd2
    style END fill:#e57373
```

## 5. Budget Allocation Creation Flow

```mermaid
flowchart TD
    START([Admin Logs In]) --> DASHBOARD[Admin Dashboard]
    DASHBOARD --> NAV[Navigate to<br/>Budget Allocations]
    
    NAV --> LIST[View Existing<br/>Allocations]
    LIST --> CREATE_BTN[Click Create<br/>Allocation Button]
    
    CREATE_BTN --> FORM[Fill Allocation Form]
    FORM --> SELECT_DEPT[Select Department]
    SELECT_DEPT --> FISCAL[Enter Fiscal Year<br/>e.g., 2024-2025]
    FISCAL --> AMOUNT[Enter Allocated<br/>Amount]
    AMOUNT --> STATUS[Select Status<br/>Active/Inactive/Pending]
    STATUS --> NOTES[Add Optional Notes]
    
    NOTES --> VALIDATE{Validate<br/>Input}
    VALIDATE -->|Invalid| ERROR[Show Validation<br/>Errors]
    ERROR --> FORM
    
    VALIDATE -->|Valid| CHECK_EXIST{Allocation<br/>Exists?}
    CHECK_EXIST -->|Yes| DUPLICATE[Show Duplicate<br/>Error]
    DUPLICATE --> FORM
    
    CHECK_EXIST -->|No| SAVE[Save to Database]
    SAVE --> INIT_SPENT[Initialize Spent<br/>Amount = 0.00]
    INIT_SPENT --> AUDIT[Create Audit Log]
    AUDIT --> NOTIFY[Notify Department Head]
    NOTIFY --> SUCCESS[Show Success Message]
    SUCCESS --> VIEW[View Allocation<br/>Details]
    
    VIEW --> MONITOR[Monitor Budget<br/>Utilization]
    MONITOR --> TRACK{Check<br/>Utilization}
    TRACK -->|< 50%| OPTIMAL[Status: Optimal<br/>Green]
    TRACK -->|50-80%| HIGH[Status: High<br/>Yellow]
    TRACK -->|> 80%| CRITICAL[Status: Critical<br/>Red]
    
    OPTIMAL --> END([Continue Monitoring])
    HIGH --> ALERT[Send Warning<br/>to Department]
    CRITICAL --> URGENT[Send Urgent Alert<br/>to Admin & Department]
    
    ALERT --> END
    URGENT --> END
    
    style START fill:#e3f2fd
    style SAVE fill:#c8e6c9
    style OPTIMAL fill:#a5d6a7
    style HIGH fill:#fff9c4
    style CRITICAL fill:#ffcdd2
    style END fill:#81c784
```

## 6. User Creation Flow (by Admin/Department Head)

```mermaid
flowchart TD
    START([Authorized User Logs In]) --> CHECK_ROLE{User Role?}
    
    CHECK_ROLE -->|Admin| ADMIN_PATH[Admin Can Create<br/>Any User Type]
    CHECK_ROLE -->|Department Head| DEPT_PATH[Dept Head Can Create<br/>Faculty in Their Dept]
    
    ADMIN_PATH --> USER_MENU[Navigate to<br/>User Management]
    DEPT_PATH --> USER_MENU
    
    USER_MENU --> CREATE_BTN[Click Create<br/>User Button]
    CREATE_BTN --> FORM[User Creation Form]
    
    FORM --> NAME[Enter Name]
    NAME --> EMAIL[Enter Email]
    EMAIL --> PASSWORD[Generate/Enter<br/>Password]
    PASSWORD --> ROLE_SELECT{Select<br/>User Role}
    
    ROLE_SELECT -->|Admin Role| ADMIN_CREATE[Create Admin User<br/>No Department Required]
    ROLE_SELECT -->|Dept Head| DEPT_SELECT[Select Department]
    ROLE_SELECT -->|Faculty| DEPT_SELECT
    
    DEPT_SELECT --> AUTH_CHECK{Authorized<br/>for Dept?}
    AUTH_CHECK -->|No| ERROR[Show Authorization<br/>Error]
    AUTH_CHECK -->|Yes| VALIDATE
    
    ADMIN_CREATE --> VALIDATE{Validate<br/>Input}
    
    VALIDATE -->|Invalid| FORM_ERROR[Show Validation<br/>Errors]
    FORM_ERROR --> FORM
    
    VALIDATE -->|Valid| CHECK_EMAIL{Email<br/>Exists?}
    CHECK_EMAIL -->|Yes| DUPLICATE[Show Duplicate<br/>Error]
    DUPLICATE --> FORM
    
    CHECK_EMAIL -->|No| HASH_PASS[Hash Password<br/>with Bcrypt]
    HASH_PASS --> SAVE[Save User to<br/>Database]
    SAVE --> CREATE_TOKEN[Generate Initial<br/>Token (Optional)]
    CREATE_TOKEN --> AUDIT[Create Audit Log]
    AUDIT --> EMAIL_WELCOME[Send Welcome Email<br/>with Credentials]
    EMAIL_WELCOME --> SUCCESS[Show Success Message]
    SUCCESS --> END([End: User Created])
    
    style START fill:#e3f2fd
    style ADMIN_PATH fill:#ff6b6b,color:#fff
    style DEPT_PATH fill:#4ecdc4
    style SAVE fill:#c8e6c9
    style END fill:#81c784
    style ERROR fill:#ffcdd2
    style DUPLICATE fill:#ffcdd2
```

## 7. Data Filtering & Search Flow

```mermaid
flowchart TD
    START([User Views List Page]) --> DISPLAY[Display Records<br/>Default View]
    
    DISPLAY --> FILTER_OPTIONS[Show Filter Options<br/>- Status<br/>- Date Range<br/>- Department<br/>- Amount Range<br/>- Search Text]
    
    FILTER_OPTIONS --> USER_INPUT{User<br/>Applies Filter?}
    USER_INPUT -->|No| DISPLAY
    
    USER_INPUT -->|Yes| SELECT_FILTERS[User Selects<br/>Filter Criteria]
    SELECT_FILTERS --> BUILD_QUERY[Build Query with<br/>Filterable Trait]
    
    BUILD_QUERY --> APPLY_STATUS{Status<br/>Filter?}
    APPLY_STATUS -->|Yes| ADD_STATUS[Add Status<br/>Condition]
    APPLY_STATUS -->|No| APPLY_DATE
    
    ADD_STATUS --> APPLY_DATE{Date Range<br/>Filter?}
    APPLY_DATE -->|Yes| ADD_DATE[Add Date Between<br/>Condition]
    APPLY_DATE -->|No| APPLY_DEPT
    
    ADD_DATE --> APPLY_DEPT{Department<br/>Filter?}
    APPLY_DEPT -->|Yes| ADD_DEPT[Add Department<br/>Condition]
    APPLY_DEPT -->|No| APPLY_AMOUNT
    
    ADD_DEPT --> APPLY_AMOUNT{Amount Range<br/>Filter?}
    APPLY_AMOUNT -->|Yes| ADD_AMOUNT[Add Amount Between<br/>Condition]
    APPLY_AMOUNT -->|No| APPLY_SEARCH
    
    ADD_AMOUNT --> APPLY_SEARCH{Search Text<br/>Provided?}
    APPLY_SEARCH -->|Yes| ADD_SEARCH[Add LIKE Conditions<br/>on Multiple Fields]
    APPLY_SEARCH -->|No| EXECUTE
    
    ADD_SEARCH --> EXECUTE[Execute Query]
    EXECUTE --> RESULTS{Results<br/>Found?}
    
    RESULTS -->|Yes| DISPLAY_RESULTS[Display Filtered<br/>Results]
    RESULTS -->|No| EMPTY[Show No Results<br/>Message]
    
    DISPLAY_RESULTS --> EXPORT_OPTION{Export<br/>Option?}
    EXPORT_OPTION -->|Yes| EXPORT[Export to CSV/PDF]
    EXPORT_OPTION -->|No| END([End])
    
    EMPTY --> CLEAR_FILTER[Option to Clear<br/>Filters]
    CLEAR_FILTER --> DISPLAY
    
    EXPORT --> END
    
    style START fill:#e3f2fd
    style EXECUTE fill:#fff3e0
    style DISPLAY_RESULTS fill:#c8e6c9
    style END fill:#81c784
    style EMPTY fill:#ffcdd2
```

## 8. Audit Log Creation Flow

```mermaid
flowchart TD
    START([User Performs Action]) --> ACTION{Action Type?}
    
    ACTION -->|Create| CREATE_ACTION[Create New Record]
    ACTION -->|Update| UPDATE_ACTION[Update Existing Record]
    ACTION -->|Delete| DELETE_ACTION[Delete Record]
    ACTION -->|Approve| APPROVE_ACTION[Approve Request]
    ACTION -->|Reject| REJECT_ACTION[Reject Request]
    
    CREATE_ACTION --> CAPTURE_NEW[Capture New Values]
    UPDATE_ACTION --> CAPTURE_CHANGES[Capture Old & New Values]
    DELETE_ACTION --> CAPTURE_OLD[Capture Old Values]
    APPROVE_ACTION --> CAPTURE_STATUS[Capture Status Change]
    REJECT_ACTION --> CAPTURE_STATUS
    
    CAPTURE_NEW --> BUILD_LOG[Build Audit Log Entry]
    CAPTURE_CHANGES --> BUILD_LOG
    CAPTURE_OLD --> BUILD_LOG
    CAPTURE_STATUS --> BUILD_LOG
    
    BUILD_LOG --> GET_USER[Get Current User ID]
    GET_USER --> GET_IP[Get IP Address]
    GET_IP --> GET_AGENT[Get User Agent]
    GET_AGENT --> GET_MODEL[Get Model Type & ID]
    GET_MODEL --> GET_ACTION_TYPE[Get Action Type String]
    
    GET_ACTION_TYPE --> PREPARE_VALUES[Prepare JSON Values<br/>old_values, new_values]
    PREPARE_VALUES --> SAVE_LOG[Save Audit Log<br/>to Database]
    
    SAVE_LOG --> CHECK_SENSITIVE{Contains<br/>Sensitive Data?}
    CHECK_SENSITIVE -->|Yes| MASK_DATA[Mask Sensitive Fields<br/>e.g., passwords]
    CHECK_SENSITIVE -->|No| COMPLETE
    
    MASK_DATA --> COMPLETE[Audit Log Created]
    COMPLETE --> VIEWABLE[Log Viewable by<br/>Admin Only]
    
    VIEWABLE --> END([End: Action Tracked])
    
    style START fill:#e3f2fd
    style BUILD_LOG fill:#fff3e0
    style SAVE_LOG fill:#c8e6c9
    style END fill:#81c784
    style MASK_DATA fill:#ffcdd2
```

## 9. Email Notification Flow

```mermaid
flowchart TD
    START([Trigger Event Occurs]) --> EVENT{Event Type?}
    
    EVENT -->|Budget Request<br/>Dept Approved| DEPT_APPROVE_MAIL
    EVENT -->|Budget Request<br/>Dept Rejected| DEPT_REJECT_MAIL
    EVENT -->|Budget Request<br/>Admin Approved| ADMIN_APPROVE_MAIL
    EVENT -->|Budget Request<br/>Admin Rejected| ADMIN_REJECT_MAIL
    
    DEPT_APPROVE_MAIL[Create BudgetRequestApprovedDepartment<br/>Mailable] --> BUILD_DEPT_APP
    DEPT_REJECT_MAIL[Create BudgetRequestRejectedDepartment<br/>Mailable] --> BUILD_DEPT_REJ
    ADMIN_APPROVE_MAIL[Create BudgetRequestApprovedAdmin<br/>Mailable] --> BUILD_ADMIN_APP
    ADMIN_REJECT_MAIL[Create BudgetRequestRejectedAdmin<br/>Mailable] --> BUILD_ADMIN_REJ
    
    BUILD_DEPT_APP[Build Email Content<br/>- Request Details<br/>- Dept Feedback<br/>- Next Steps] --> GET_RECIPIENTS_DEPT_APP
    
    BUILD_DEPT_REJ[Build Email Content<br/>- Request Details<br/>- Rejection Reason<br/>- Dept Feedback] --> GET_RECIPIENTS_DEPT_REJ
    
    BUILD_ADMIN_APP[Build Email Content<br/>- Request Details<br/>- Admin Feedback<br/>- Approval Confirmation] --> GET_RECIPIENTS_ADMIN_APP
    
    BUILD_ADMIN_REJ[Build Email Content<br/>- Request Details<br/>- Rejection Reason<br/>- Admin Feedback] --> GET_RECIPIENTS_ADMIN_REJ
    
    GET_RECIPIENTS_DEPT_APP[Get Recipients<br/>- Faculty User<br/>- Admin] --> QUEUE_DEPT_APP
    
    GET_RECIPIENTS_DEPT_REJ[Get Recipients<br/>- Faculty User] --> QUEUE_DEPT_REJ
    
    GET_RECIPIENTS_ADMIN_APP[Get Recipients<br/>- Faculty User<br/>- Department Head] --> QUEUE_ADMIN_APP
    
    GET_RECIPIENTS_ADMIN_REJ[Get Recipients<br/>- Faculty User<br/>- Department Head] --> QUEUE_ADMIN_REJ
    
    QUEUE_DEPT_APP[Queue Email Job] --> SEND
    QUEUE_DEPT_REJ[Queue Email Job] --> SEND
    QUEUE_ADMIN_APP[Queue Email Job] --> SEND
    QUEUE_ADMIN_REJ[Queue Email Job] --> SEND
    
    SEND[Process Email Queue] --> MAIL_CHECK{Mail<br/>Configuration OK?}
    
    MAIL_CHECK -->|Yes| SEND_EMAIL[Send Email via<br/>SMTP/Mailtrap]
    MAIL_CHECK -->|No| LOG_ERROR[Log Email Error]
    
    SEND_EMAIL --> DELIVERY{Email<br/>Delivered?}
    DELIVERY -->|Yes| SUCCESS[Mark as Sent]
    DELIVERY -->|No| RETRY[Retry (3 attempts)]
    
    RETRY --> RETRY_CHECK{Max<br/>Retries?}
    RETRY_CHECK -->|No| SEND_EMAIL
    RETRY_CHECK -->|Yes| FAIL[Mark as Failed<br/>Log Error]
    
    SUCCESS --> END([End: Notification Sent])
    FAIL --> END
    LOG_ERROR --> END
    
    style START fill:#e3f2fd
    style SEND_EMAIL fill:#fff3e0
    style SUCCESS fill:#c8e6c9
    style END fill:#81c784
    style FAIL fill:#ffcdd2
    style LOG_ERROR fill:#ffcdd2
```

## 10. Analytics & Reporting Flow

```mermaid
flowchart TD
    START([User Accesses Analytics]) --> AUTH_CHECK{User<br/>Authorized?}
    
    AUTH_CHECK -->|No| DENY[Show 403 Error]
    AUTH_CHECK -->|Yes| ROLE_CHECK{User Role?}
    
    ROLE_CHECK -->|Admin| FULL_ACCESS[Access All Analytics]
    ROLE_CHECK -->|Department Head| DEPT_ACCESS[Access Department<br/>Analytics Only]
    ROLE_CHECK -->|Faculty| LIMITED[Access Personal<br/>Statistics Only]
    
    FULL_ACCESS --> ANALYTICS_MENU[Analytics Dashboard]
    DEPT_ACCESS --> ANALYTICS_MENU
    LIMITED --> ANALYTICS_MENU
    
    ANALYTICS_MENU --> SELECT{Select<br/>Report Type}
    
    SELECT -->|Overview| OVERVIEW[GET /api/analytics/overview<br/>- Total Requests<br/>- Total Amount<br/>- Approval Rate]
    
    SELECT -->|By Department| BY_DEPT[GET /api/analytics/budget-by-department<br/>- Dept Breakdown<br/>- Spending per Dept]
    
    SELECT -->|Status| BY_STATUS[GET /api/analytics/request-status<br/>- Status Distribution<br/>- Approval Pipeline]
    
    SELECT -->|Trends| TRENDS[GET /api/analytics/spending-trends<br/>- Monthly Trends<br/>- Year-over-Year]
    
    SELECT -->|Allocation vs Spending| ALLOC_SPEND[GET /api/analytics/budget-allocation-vs-spending<br/>- Allocated vs Spent<br/>- Utilization %]
    
    SELECT -->|Forecast| FORECAST[GET /api/analytics/forecast<br/>- Projected Spending<br/>- Budget Predictions]
    
    OVERVIEW --> PROCESS[Process Data]
    BY_DEPT --> PROCESS
    BY_STATUS --> PROCESS
    TRENDS --> PROCESS
    ALLOC_SPEND --> PROCESS
    FORECAST --> PROCESS
    
    PROCESS --> FILTER_ROLE{Filter by<br/>User Role}
    
    FILTER_ROLE -->|Admin| ALL_DATA[Query All Data]
    FILTER_ROLE -->|Department| DEPT_DATA[Query Department Data]
    FILTER_ROLE -->|Faculty| USER_DATA[Query User Data]
    
    ALL_DATA --> AGGREGATE[Aggregate & Calculate]
    DEPT_DATA --> AGGREGATE
    USER_DATA --> AGGREGATE
    
    AGGREGATE --> FORMAT[Format Response<br/>JSON/Charts Data]
    FORMAT --> CACHE{Cache<br/>Results?}
    
    CACHE -->|Yes| STORE_CACHE[Store in Cache<br/>5-15 minutes]
    CACHE -->|No| RETURN
    
    STORE_CACHE --> RETURN[Return Analytics Data]
    RETURN --> DISPLAY[Display Charts<br/>& Visualizations]
    
    DISPLAY --> EXPORT_OPT{Export<br/>Option?}
    EXPORT_OPT -->|Yes| EXPORT[Export to<br/>PDF/Excel/CSV]
    EXPORT_OPT -->|No| END([End])
    
    EXPORT --> END
    DENY --> END
    
    style START fill:#e3f2fd
    style AGGREGATE fill:#fff3e0
    style DISPLAY fill:#c8e6c9
    style END fill:#81c784
    style DENY fill:#ffcdd2
```
