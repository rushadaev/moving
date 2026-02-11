# Deployment Guide - MOOWEE Moving Application

## Quick Start

On your AWS server, navigate to the project directory and run:

```bash
cd /home/ubuntu/www/moving
make deploy
```

That's it! This single command will:
1. ✅ Pull latest code from Git
2. ✅ Rebuild Docker containers
3. ✅ Run database migrations
4. ✅ Clear caches
5. ✅ Optimize for production
6. ✅ Restart all services

---

## Available Deployment Commands

### 🚀 Main Deployment Commands

| Command | Description | When to Use |
|---------|-------------|-------------|
| `make deploy` | **Full deployment** (recommended) | After code changes, new features, or updates |
| `make deploy-quick` | Quick deployment (no rebuild) | For minor config changes only |
| `make deploy-force` | Force rebuild from scratch | When Docker cache issues occur |
| `make update-all` | Alias for `make deploy` | Same as deploy |

### 📦 Individual Update Steps

If you need to run steps manually:

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker-compose -f docker-compose.prod.yml build

# Start containers
docker-compose -f docker-compose.prod.yml up -d

# Run migrations
docker-compose -f docker-compose.prod.yml exec -T backend php artisan migrate --force

# Clear caches
make moving-cache-clear

# Optimize for production
docker-compose -f docker-compose.prod.yml exec -T backend php artisan config:cache
docker-compose -f docker-compose.prod.yml exec -T backend php artisan route:cache
docker-compose -f docker-compose.prod.yml exec -T backend php artisan view:cache
```

### 🔄 Rollback

If something goes wrong after deployment:

```bash
make deploy-rollback
```

This will revert to the previous Git commit and redeploy.

---

## Database Management

### Backup Database

```bash
make backup-db
```

This creates a backup in `backups/db_backup_YYYYMMDD_HHMMSS.sql`

### Restore Database

```bash
make restore-db FILE=backups/db_backup_20250211_120000.sql
```

### Manual Database Backup

```bash
docker-compose -f docker-compose.prod.yml exec mysql mysqldump \
  -u root -psecret laravel > backup.sql
```

---

## Monitoring

### Check Service Status

```bash
make status
```

### View Logs

```bash
# All services
make logs

# Specific service
make moving-logs
make nginx-logs

# Watch logs in real-time
make watch-logs
```

### Health Check

```bash
make health-check
```

This checks if your site is responding correctly.

### Check Disk Usage

```bash
make disk-usage
```

---

## Common Deployment Scenarios

### 1. Deploy New Feature (Most Common)

```bash
cd /home/ubuntu/www/moving
make deploy
```

### 2. Deploy Frontend Changes Only

```bash
cd /home/ubuntu/www/moving
git pull origin main
docker-compose -f docker-compose.prod.yml restart frontend
```

### 3. Deploy Backend Changes with New Migration

```bash
cd /home/ubuntu/www/moving
make deploy
# This automatically runs migrations
```

### 4. Deploy Configuration Changes Only

```bash
cd /home/ubuntu/www/moving
git pull origin main
make deploy-quick
```

### 5. Emergency Rollback

```bash
cd /home/ubuntu/www/moving
make deploy-rollback
```

Or manually:

```bash
git reset --hard HEAD~1  # Go back 1 commit
# Or
git reset --hard abc1234  # Go back to specific commit
make deploy
```

---

## Troubleshooting Deployments

### Problem: Deployment fails with "port already in use"

```bash
# Check what's using the port
sudo lsof -i :80
sudo lsof -i :443

# Stop all containers and restart
make down
make up
```

### Problem: Containers fail to start

```bash
# Check logs for errors
make logs

# Check specific service
docker-compose -f docker-compose.prod.yml logs backend

# Restart specific service
docker-compose -f docker-compose.prod.yml restart backend
```

### Problem: Database migration fails

```bash
# Check database is running
docker-compose -f docker-compose.prod.yml ps mysql

# Access database shell
make db-moving

# Run migration with verbose output
docker-compose -f docker-compose.prod.yml exec backend php artisan migrate --force -vvv
```

### Problem: Out of disk space

```bash
# Check disk usage
df -h
make disk-usage

# Clean up Docker
docker system prune -af --volumes

# Clean old backups (keep last 7 days)
find backups/ -name "*.sql" -mtime +7 -delete
```

### Problem: Changes not appearing after deployment

```bash
# Force clear all caches
make moving-cache-clear

# Or do a force rebuild
make deploy-force
```

---

## Pre-Deployment Checklist

Before running `make deploy` on production:

- [ ] Changes tested locally
- [ ] Database backup created (`make backup-db`)
- [ ] All tests passing
- [ ] No breaking changes to API
- [ ] Environment variables updated if needed
- [ ] Migrations are safe and reversible
- [ ] Dependencies updated in composer.json/package.json

---

## Post-Deployment Verification

After running `make deploy`:

1. **Check service status:**
   ```bash
   make status
   ```

2. **Check health:**
   ```bash
   make health-check
   ```

3. **Test in browser:**
   - Visit https://mooweemoving.com
   - Test login
   - Test creating a request
   - Test Stripe payment flow

4. **Check logs for errors:**
   ```bash
   make watch-logs
   # Press Ctrl+C to exit
   ```

5. **Verify database migrations:**
   ```bash
   docker-compose -f docker-compose.prod.yml exec backend php artisan migrate:status
   ```

---

## Automated Deployment (Optional)

### Setup GitHub Actions for Auto-Deploy

Create `.github/workflows/deploy-prod.yml`:

```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - name: Deploy via SSH
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.AWS_HOST }}
          username: ubuntu
          key: ${{ secrets.AWS_SSH_KEY }}
          script: |
            cd /home/ubuntu/www/moving
            make deploy
```

Then set secrets in GitHub:
- `AWS_HOST` - Your EC2 instance IP or domain
- `AWS_SSH_KEY` - Your private SSH key

---

## Quick Reference Commands

```bash
# Most common commands
make deploy              # Full deployment
make status              # Check services
make logs                # View logs
make backup-db           # Backup database
make health-check        # Test site health

# Service management
make up                  # Start all services
make down                # Stop all services
make restart             # Restart all services

# Debugging
make moving-logs         # Moving app logs
make nginx-logs          # Nginx logs
make watch-logs          # Live log feed

# Cleanup
make clean               # Remove everything (CAREFUL!)
```

---

## Support

If you encounter issues:

1. Check logs: `make logs`
2. Check disk space: `df -h`
3. Check container status: `make status`
4. Check AWS_SETUP_GUIDE.md for detailed AWS configuration

---

**Last Updated**: February 11, 2026
**For**: MOOWEE Moving Application
