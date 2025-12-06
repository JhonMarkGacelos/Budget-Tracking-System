# Railway Deployment Guide

## Budget Tracking System - Railway Hosting

This guide will help you deploy your Budget Tracking System to Railway.

---

## Prerequisites

1. **Railway Account**: Sign up at [railway.app](https://railway.app)
2. **GitHub Repository**: Push your code to GitHub
3. **Railway CLI** (optional): Install with `npm i -g @railway/cli`

---

## Quick Deploy Steps

### 1. Create New Project on Railway

1. Go to [railway.app](https://railway.app)
2. Click **"New Project"**
3. Select **"Deploy from GitHub repo"**
4. Authorize Railway to access your repository
5. Select your Budget Tracking System repository

### 2. Add MySQL Database

1. In your Railway project, click **"+ New"**
2. Select **"Database"** → **"Add MySQL"**
3. Railway will automatically provision a MySQL database
4. The database credentials will be available as environment variables

### 3. Configure Environment Variables

Railway will automatically inject MySQL variables. Add these additional variables:

**Required Variables:**
```env
APP_NAME=Budget Tracking System
APP_ENV=production
APP_DEBUG=false
APP_KEY=base64:YOUR_KEY_HERE
APP_URL=https://your-app-name.railway.app

# Session & Cache
SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

# Mail Configuration (use your SMTP provider)
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=your-email@gmail.com
MAIL_PASSWORD=your-app-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@yourdomain.com
MAIL_FROM_NAME=Budget Tracking System
```

**How to add variables:**
1. Go to your project in Railway
2. Select your service
3. Go to **"Variables"** tab
4. Click **"+ New Variable"**
5. Add each variable above

### 4. Generate APP_KEY

Run this command locally:
```bash
php artisan key:generate --show
```

Copy the output and add it as `APP_KEY` in Railway variables.

### 5. Deploy

Railway will automatically deploy when you:
- Push to your GitHub repository (if connected)
- Or click **"Deploy"** in Railway dashboard

---

## Deployment Files

Your project includes these Railway configuration files:

### **Procfile**
Defines the web process command.

### **nixpacks.toml**
Configures the build process (PHP 8.2, Composer, Node.js).

### **railway.json**
Railway-specific deployment configuration.

---

## Post-Deployment Setup

### 1. Run Database Migrations

The migrations run automatically during deployment. To manually run them:

```bash
# Using Railway CLI
railway run php artisan migrate --force

# Or use Railway's web terminal
```

### 2. Seed Initial Data (Optional)

```bash
railway run php artisan db:seed --force
```

### 3. Create Admin User

Access your app and register the first user, then manually set their role to `admin` in the database.

Or run a tinker command:
```bash
railway run php artisan tinker
# Then run:
User::where('email', 'admin@example.com')->update(['role' => 'admin']);
```

---

## Environment Variables Reference

### Automatic (Provided by Railway MySQL)

Railway automatically provides these when you add MySQL:
- `MYSQLHOST` - Database host
- `MYSQLPORT` - Database port (usually 3306)
- `MYSQLDATABASE` - Database name
- `MYSQLUSER` - Database username
- `MYSQLPASSWORD` - Database password
- `DATABASE_URL` - Full connection string

### Manual Configuration

**Application:**
- `APP_NAME` - Your application name
- `APP_ENV` - Set to `production`
- `APP_KEY` - Generate with `php artisan key:generate --show`
- `APP_DEBUG` - Set to `false` for production
- `APP_URL` - Your Railway app URL (e.g., `https://budget-tracking.railway.app`)

**Mail (use Gmail, SendGrid, or Mailgun):**
- `MAIL_MAILER` - `smtp`
- `MAIL_HOST` - Your SMTP host
- `MAIL_PORT` - `587` (TLS) or `465` (SSL)
- `MAIL_USERNAME` - Your email username
- `MAIL_PASSWORD` - Your email password/app password
- `MAIL_ENCRYPTION` - `tls` or `ssl`
- `MAIL_FROM_ADDRESS` - Sender email
- `MAIL_FROM_NAME` - Sender name

---

## Common Issues & Solutions

### Issue: "No application encryption key"
**Solution:** Generate and set `APP_KEY`:
```bash
php artisan key:generate --show
```
Add the output to Railway environment variables.

### Issue: Database connection failed
**Solution:** Verify MySQL service is running and variables are set:
- Check that `MYSQLHOST`, `MYSQLPORT`, etc. are available
- Ensure your `.env.example` uses these variable names

### Issue: Mail not sending
**Solution:** 
1. For Gmail, create an "App Password" (not your regular password)
2. Enable "Less secure app access" or use OAuth2
3. Consider using SendGrid or Mailgun for production

### Issue: Storage/File upload errors
**Solution:** Railway's filesystem is ephemeral. For persistent storage:
1. Use Railway's persistent volumes
2. Or integrate with cloud storage (AWS S3, Cloudinary)

### Issue: Build fails
**Solution:** 
1. Check `composer.json` requires PHP 8.2+
2. Ensure `package.json` and `vite.config.js` are present
3. Review build logs in Railway dashboard

---

## Monitoring & Logs

### View Logs
1. Go to your Railway project
2. Select your service
3. Click **"Deployments"** tab
4. Click on a deployment to view logs

### Using Railway CLI
```bash
# Install CLI
npm i -g @railway/cli

# Login
railway login

# Link to project
railway link

# View logs
railway logs

# Run commands
railway run php artisan tinker
```

---

## Scaling & Performance

### Database
- Railway MySQL is production-ready
- Consider upgrading plan for more resources
- Enable connection pooling if needed

### Application
- Railway auto-scales based on your plan
- Monitor CPU and memory usage
- Use caching (Redis) for better performance

### CDN & Assets
- Use Railway's CDN for static assets
- Consider Cloudflare for additional caching
- Optimize images before upload

---

## Custom Domain

### Add Your Domain

1. Go to **"Settings"** → **"Domains"**
2. Click **"+ Custom Domain"**
3. Enter your domain (e.g., `budget.yourdomain.com`)
4. Add DNS records provided by Railway to your domain registrar:
   - **CNAME**: Point to Railway's domain
   - Or **A Record**: Point to Railway's IP

### Update APP_URL
After adding domain, update:
```env
APP_URL=https://budget.yourdomain.com
```

---

## Backup Strategy

### Database Backups

**Automated:**
Railway Pro plan includes automatic backups.

**Manual:**
```bash
# Export database
railway run mysqldump -h $MYSQLHOST -P $MYSQLPORT -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE > backup.sql

# Import database
railway run mysql -h $MYSQLHOST -P $MYSQLPORT -u $MYSQLUSER -p$MYSQLPASSWORD $MYSQLDATABASE < backup.sql
```

### Code Backups
- Use Git for version control
- Push regularly to GitHub
- Tag releases for rollback capability

---

## Security Checklist

- ✅ `APP_DEBUG=false` in production
- ✅ `APP_ENV=production`
- ✅ Strong `APP_KEY` generated
- ✅ Database credentials secured (auto by Railway)
- ✅ HTTPS enabled (automatic on Railway)
- ✅ CORS configured properly
- ✅ Rate limiting enabled
- ✅ Input validation implemented
- ✅ SQL injection protection (Eloquent)
- ✅ XSS protection (Blade escaping)

---

## Cost Estimation

**Free Tier:**
- $5 credit per month
- Good for testing and small projects
- Limited resources

**Hobby Plan ($5/month):**
- $5 credit included
- Suitable for small production apps
- Pay-as-you-go beyond credit

**Pro Plan ($20/month):**
- $20 credit included
- Better performance
- Automatic backups
- Priority support

---

## Support & Resources

- **Railway Docs**: [docs.railway.app](https://docs.railway.app)
- **Laravel Docs**: [laravel.com/docs](https://laravel.com/docs)
- **Community**: Railway Discord, Laravel Discord
- **GitHub Issues**: Report bugs in your repository

---

## Deployment Checklist

Before deploying to production:

- [ ] All tests passing
- [ ] Environment variables configured
- [ ] Database migrations ready
- [ ] Seeders tested (if using)
- [ ] Mail configuration working
- [ ] File upload paths configured
- [ ] Error pages customized
- [ ] Logging configured
- [ ] Security headers set
- [ ] Rate limiting enabled
- [ ] Backup strategy in place
- [ ] Monitoring set up
- [ ] Custom domain configured
- [ ] SSL certificate active
- [ ] Performance optimized

---

## Quick Commands Reference

```bash
# Generate APP_KEY
php artisan key:generate --show

# Run migrations
railway run php artisan migrate --force

# Seed database
railway run php artisan db:seed --force

# Clear cache
railway run php artisan cache:clear
railway run php artisan config:clear
railway run php artisan route:clear
railway run php artisan view:clear

# View logs
railway logs

# Open app
railway open
```

---

## Next Steps

1. ✅ Push code to GitHub
2. ✅ Create Railway project
3. ✅ Add MySQL database
4. ✅ Configure environment variables
5. ✅ Deploy application
6. ✅ Run migrations
7. ✅ Test the application
8. ✅ Add custom domain (optional)
9. ✅ Set up monitoring
10. ✅ Configure backups

---

**Your Budget Tracking System is now live on Railway! 🚀**

For questions or issues, check the Railway documentation or Laravel community resources.
