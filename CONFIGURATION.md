# Budget Tracking System - Configuration Guide

## Environment Configuration

The system is pre-configured with the following `.env` settings:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:[YOUR_KEY]
APP_DEBUG=true
APP_URL=http://localhost

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3307
DB_DATABASE=budget_tracking_system
DB_USERNAME=root
DB_PASSWORD=

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database
```

## System Requirements

### Hardware
- Processor: 1.5 GHz or higher
- RAM: 512 MB minimum (1 GB recommended)
- Disk Space: 500 MB minimum

### Software
- PHP: 8.1 or higher
- MySQL: 5.7 or higher (tested on 8.0+)
- Composer: Latest stable version

### PHP Extensions Required
- BCMath
- Ctype
- JSON
- Mbstring
- OpenSSL
- PDO (MySQL)
- Tokenizer

## Installation Verification

### Step 1: Verify PHP Version
```bash
php -v
```

### Step 2: Verify MySQL Connection
```bash
mysql -u root -P 3307 -e "SELECT 1;"
```

### Step 3: Verify Database
```bash
mysql -u root -P 3307 -e "USE budget_tracking_system; SHOW TABLES;"
```

### Step 4: Check Laravel Installation
```bash
cd bunget_tracking_system
php artisan --version
```

## Starting the System

### Method 1: Built-in Server (Development)
```bash
cd bunget_tracking_system
php artisan serve --host=0.0.0.0 --port=8000
```

Server available at: `http://localhost:8000`

### Method 2: Apache/Nginx (Production)
Configure your web server to point to the `public` directory.

## First Time Setup

If you need to reset the system:

```bash
# Clear configuration cache
php artisan config:clear

# Fresh migrations and seed
php artisan migrate:refresh --seed

# Clear caches
php artisan cache:clear
php artisan view:clear
```

## User Accounts

### Admin Account
```
Email: admin@budgettracking.com
Password: admin@123456
Role: Admin
```

### Creating Additional Users

**Via API:**
```bash
POST /api/users
Authorization: Bearer {admin_token}
{
  "name": "Department Head",
  "email": "dept@example.com",
  "password": "password123",
  "password_confirmation": "password123",
  "role": "department",
  "department_id": 1
}
```

**Roles Available:**
- `admin` - System administrator (Admin only)
- `department` - Department head
- `faculty` - Faculty/Staff member

## Departments Reference

| ID | Department |
|----|-----------|
| 1 | College of Graduate Studies |
| 2 | College of Nursing and Health Sciences |
| 3 | College of Engineering |
| 4 | College of Education |
| 5 | College of Arts and Sciences |
| 6 | College of Industrial Technology |

## Database Tables

### users
```sql
CREATE TABLE users (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255),
  email VARCHAR(255) UNIQUE,
  password VARCHAR(255),
  role ENUM('admin', 'department', 'faculty') DEFAULT 'faculty',
  department_id BIGINT NULLABLE,
  email_verified_at TIMESTAMP NULL,
  remember_token VARCHAR(100),
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (department_id) REFERENCES departments(id)
);
```

### departments
```sql
CREATE TABLE departments (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  name VARCHAR(255) UNIQUE,
  description TEXT,
  created_at TIMESTAMP,
  updated_at TIMESTAMP
);
```

### budget_requests
```sql
CREATE TABLE budget_requests (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT,
  department_id BIGINT,
  title VARCHAR(255),
  description TEXT,
  amount DECIMAL(15, 2),
  status ENUM(...) DEFAULT 'pending',
  department_feedback TEXT,
  admin_feedback TEXT,
  submitted_at TIMESTAMP,
  department_reviewed_at TIMESTAMP NULL,
  admin_reviewed_at TIMESTAMP NULL,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (department_id) REFERENCES departments(id)
);
```

### budget_submissions
```sql
CREATE TABLE budget_submissions (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT,
  budget_request_id BIGINT,
  submission_content TEXT,
  document_path VARCHAR(255) NULL,
  status ENUM('pending', 'department_reviewed', 'admin_reviewed') DEFAULT 'pending',
  department_feedback TEXT,
  admin_feedback TEXT,
  submitted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (budget_request_id) REFERENCES budget_requests(id)
);
```

### liquidation_reports
```sql
CREATE TABLE liquidation_reports (
  id BIGINT PRIMARY KEY AUTO_INCREMENT,
  user_id BIGINT,
  budget_request_id BIGINT,
  report_content TEXT,
  document_path VARCHAR(255) NULL,
  status ENUM('pending', 'department_reviewed', 'admin_reviewed') DEFAULT 'pending',
  department_feedback TEXT,
  admin_feedback TEXT,
  submitted_at TIMESTAMP,
  created_at TIMESTAMP,
  updated_at TIMESTAMP,
  FOREIGN KEY (user_id) REFERENCES users(id),
  FOREIGN KEY (budget_request_id) REFERENCES budget_requests(id)
);
```

## API Authentication

### Getting a Token

```bash
POST /api/login
Content-Type: application/json

{
  "email": "admin@budgettracking.com",
  "password": "admin@123456"
}
```

Response:
```json
{
  "message": "Login successful",
  "user": {
    "id": 1,
    "name": "System Administrator",
    "email": "admin@budgettracking.com",
    "role": "admin",
    "department": null
  },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### Using the Token

All authenticated requests must include:
```
Authorization: Bearer {token}
```

Example:
```bash
curl -X GET http://localhost:8000/api/users \
  -H "Authorization: Bearer 1|xxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

## File Permissions

```bash
chmod -R 755 bunget_tracking_system
chmod -R 777 bunget_tracking_system/storage
chmod -R 777 bunget_tracking_system/bootstrap/cache
```

## Common Commands

### Laravel Artisan Commands

```bash
# Show all available commands
php artisan list

# Clear all caches
php artisan cache:clear

# Clear configuration cache
php artisan config:clear

# Clear compiled views
php artisan view:clear

# Show application routes
php artisan route:list

# Check database connection
php artisan db

# Run migrations
php artisan migrate

# Rollback migrations
php artisan migrate:rollback

# Seed database
php artisan db:seed

# View application logs
tail -f storage/logs/laravel.log
```

## Troubleshooting

### Port Already in Use
```bash
# Use a different port
php artisan serve --host=0.0.0.0 --port=8001
```

### Database Connection Failed
```bash
# Verify MySQL is running
mysql -u root -P 3307 -e "SELECT 1;"

# Check .env configuration
cat .env | grep DB_

# Test connection
php artisan db
```

### Migrations Fail
```bash
# Clear config cache
php artisan config:clear

# Run migrations step by step
php artisan migrate --step
```

### Permission Denied
```bash
# Fix file permissions
chmod -R 755 .
chmod -R 777 storage
chmod -R 777 bootstrap/cache
```

## Performance Optimization

### For Production

1. **Clear Development Cache**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

2. **Optimize Autoloader**
```bash
composer install --optimize-autoloader --no-dev
php artisan optimize
```

3. **Use .env.production**
```bash
APP_ENV=production
APP_DEBUG=false
```

## Backup & Restore

### Backup Database
```bash
mysqldump -u root -P 3307 budget_tracking_system > backup.sql
```

### Restore Database
```bash
mysql -u root -P 3307 budget_tracking_system < backup.sql
```

## Monitoring

### View Application Logs
```bash
# Real-time log viewing
tail -f storage/logs/laravel.log

# Last 50 lines
tail -50 storage/logs/laravel.log

# Search logs
grep "error" storage/logs/laravel.log
```

### Check System Health
```bash
# Database connection
php artisan db

# Configuration
php artisan config:show

# Routes
php artisan route:list
```

## Development Tips

### Enable Query Logging
Add to AppServiceProvider:
```php
if (config('app.debug')) {
    DB::listen(function ($query) {
        \Log::info($query->sql, $query->bindings);
    });
}
```

### Using Tinker REPL
```bash
php artisan tinker

>>> \App\Models\User::count()
>>> \App\Models\Department::all()
>>> \App\Models\BudgetRequest::latest()->first()
```

## Additional Resources

- [Laravel Documentation](https://laravel.com/docs)
- [MySQL Documentation](https://dev.mysql.com/doc/)
- [Composer Documentation](https://getcomposer.org/doc/)
- [REST API Best Practices](https://restfulapi.net/)

---

For more information, see README.md and QUICK_START.md
