# AWS Production Environment Setup Guide

## Project Status
✅ **Application is currently running on:**
- HTTP: port 80 (redirects to HTTPS)
- HTTPS: port 443 (with self-signed certificate)
- Database: MySQL on port 3307
- All containers: Up and healthy

## Current Setup Summary
- **OS**: Ubuntu 24.04 (Noble)
- **Docker**: v28.5.1
- **Docker Compose**: v2.40.1
- **Domain**: mooweemoving.com
- **Project Path**: /home/ubuntu/www/moving

---

## AWS Infrastructure Requirements

### 1. EC2 Instance Configuration

#### Instance Type
- **Recommended**: t3.medium or larger
  - 2 vCPUs
  - 4 GB RAM
  - For production with moderate traffic
- **Minimum**: t3.small (2 vCPUs, 2 GB RAM) for low traffic

#### Storage
- **Root Volume**: 30 GB GP3 SSD minimum
- **Recommended**: 50 GB GP3 for logs and database growth

#### AMI
- Ubuntu Server 24.04 LTS (currently installed)

### 2. Security Groups Configuration

Create a security group with the following inbound rules:

```
┌──────────────┬─────────┬────────────┬─────────────────────────┐
│ Type         │ Port    │ Protocol   │ Source                  │
├──────────────┼─────────┼────────────┼─────────────────────────┤
│ HTTP         │ 80      │ TCP        │ 0.0.0.0/0, ::/0        │
│ HTTPS        │ 443     │ TCP        │ 0.0.0.0/0, ::/0        │
│ SSH          │ 22      │ TCP        │ Your IP/VPN only       │
│ MySQL*       │ 3307    │ TCP        │ Security group ID**    │
└──────────────┴─────────┴────────────┴─────────────────────────┘
```

\* Only if you need external database access (not recommended for production)
\*\* Reference the same security group for internal communication

**Outbound rules**: Allow all traffic (default)

### 3. Elastic IP (Recommended)
- Allocate an Elastic IP and associate it with your EC2 instance
- This ensures your IP doesn't change on instance restart
- Required for consistent DNS configuration

### 4. DNS Configuration (Route 53 or External DNS)

#### Using Route 53:
1. Create a hosted zone for `mooweemoving.com`
2. Create an A record pointing to your Elastic IP:
   ```
   Name: mooweemoving.com
   Type: A
   Value: <Your Elastic IP>
   TTL: 300
   ```
3. Create a CNAME record for www:
   ```
   Name: www.mooweemoving.com
   Type: CNAME
   Value: mooweemoving.com
   TTL: 300
   ```
4. Update your domain registrar's nameservers to point to Route 53

#### Using External DNS Provider:
- Create an A record pointing `mooweemoving.com` to your Elastic IP
- Create a CNAME record for `www` pointing to `mooweemoving.com`

---

## SSL Certificate Setup (Let's Encrypt)

Currently using **self-signed certificates** for testing. For production:

### Option 1: Using Certbot (Recommended)

1. **Stop the nginx container temporarily:**
```bash
cd /home/ubuntu/www/moving
docker compose -f docker-compose.prod.yml stop nginx
```

2. **Install Certbot:**
```bash
sudo apt-get update
sudo apt-get install -y certbot
```

3. **Get SSL certificate:**
```bash
sudo certbot certonly --standalone \
  -d mooweemoving.com \
  -d www.mooweemoving.com \
  --non-interactive \
  --agree-tos \
  --email admin@mooweemoving.com
```

4. **Copy certificates to project directory:**
```bash
sudo cp /etc/letsencrypt/live/mooweemoving.com/fullchain.pem /home/ubuntu/www/moving/ssl/
sudo cp /etc/letsencrypt/live/mooweemoving.com/privkey.pem /home/ubuntu/www/moving/ssl/
sudo chown ubuntu:ubuntu /home/ubuntu/www/moving/ssl/*.pem
sudo chmod 644 /home/ubuntu/www/moving/ssl/fullchain.pem
sudo chmod 600 /home/ubuntu/www/moving/ssl/privkey.pem
```

5. **Restart nginx:**
```bash
docker compose -f docker-compose.prod.yml start nginx
```

6. **Setup auto-renewal:**
```bash
sudo crontab -e
# Add this line:
0 3 * * * certbot renew --quiet --deploy-hook "cp /etc/letsencrypt/live/mooweemoving.com/*.pem /home/ubuntu/www/moving/ssl/ && chown ubuntu:ubuntu /home/ubuntu/www/moving/ssl/*.pem && cd /home/ubuntu/www/moving && docker compose -f docker-compose.prod.yml restart nginx"
```

### Option 2: Using AWS Certificate Manager (ACM) with Application Load Balancer

1. **Create an Application Load Balancer**
2. **Request a certificate in ACM for your domain**
3. **Configure ALB to:**
   - Listen on port 443 (HTTPS) with ACM certificate
   - Forward to target group on port 80
   - Redirect HTTP (80) to HTTPS (443)
4. **Update Security Group:**
   - Remove port 443 from EC2 instance
   - Allow port 80 from ALB security group only

---

## Database Configuration

### Current Setup
- MySQL 8.0 running in Docker container
- Port 3307 exposed on host
- Data persisted in Docker volume `moving_mysql_data`

### Production Recommendations

#### Option 1: RDS MySQL (Recommended)
**Advantages:**
- Automated backups
- High availability with Multi-AZ
- Automatic patching
- Better performance at scale

**Setup:**
1. Create RDS MySQL 8.0 instance
2. Choose instance type (db.t3.medium or larger)
3. Enable automated backups (7-30 days retention)
4. Place in private subnet
5. Update `.env` file:
```env
DB_HOST=<rds-endpoint>
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=<rds-username>
DB_PASSWORD=<secure-password>
```
6. Migrate data from Docker MySQL to RDS
7. Remove MySQL container from docker-compose.prod.yml

#### Option 2: Docker MySQL (Current)
**For development/small production only**

**To backup:**
```bash
docker exec moving-mysql-1 mysqldump -u root -p<password> laravel > backup.sql
```

**To restore:**
```bash
docker exec -i moving-mysql-1 mysql -u root -p<password> laravel < backup.sql
```

---

## Monitoring and Logging

### CloudWatch Integration

1. **Install CloudWatch Agent:**
```bash
wget https://s3.amazonaws.com/amazoncloudwatch-agent/ubuntu/amd64/latest/amazon-cloudwatch-agent.deb
sudo dpkg -i amazon-cloudwatch-agent.deb
```

2. **Configure CloudWatch Agent** to monitor:
   - CPU utilization
   - Memory usage
   - Disk space
   - Docker container logs

3. **Docker Logs:**
```bash
# View all container logs
docker compose -f docker-compose.prod.yml logs -f

# View specific service logs
docker compose -f docker-compose.prod.yml logs -f nginx
docker compose -f docker-compose.prod.yml logs -f backend
```

### Log Rotation
Add to `/etc/logrotate.d/docker`:
```
/var/lib/docker/containers/*/*.log {
    rotate 7
    daily
    compress
    missingok
    delaycompress
    copytruncate
}
```

---

## Backup Strategy

### 1. Database Backups
```bash
# Create backup script: /home/ubuntu/scripts/backup-db.sh
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/home/ubuntu/backups"
mkdir -p $BACKUP_DIR

docker exec moving-mysql-1 mysqldump -u root -p$DOCKER_MYSQL_ROOT_PASSWORD laravel \
  > $BACKUP_DIR/db_backup_$DATE.sql

# Upload to S3
aws s3 cp $BACKUP_DIR/db_backup_$DATE.sql s3://your-backup-bucket/database/

# Keep only last 7 days locally
find $BACKUP_DIR -name "db_backup_*.sql" -mtime +7 -delete
```

Run daily via cron:
```bash
0 2 * * * /home/ubuntu/scripts/backup-db.sh
```

### 2. Application Files Backup
- Code is in Git repository (already backed up)
- User uploads (if any) should be stored in S3

### 3. EBS Snapshots
- Enable automated EBS snapshots for root volume
- Retention: 7 days minimum

---

## Application Deployment

### Current Deployment Process

```bash
# Navigate to project directory
cd /home/ubuntu/www/moving

# Pull latest code
git pull origin main

# Rebuild and restart containers
docker compose -f docker-compose.prod.yml build
docker compose -f docker-compose.prod.yml up -d

# Run migrations (if needed)
docker compose -f docker-compose.prod.yml exec backend php artisan migrate --force

# Clear caches
docker compose -f docker-compose.prod.yml exec backend php artisan config:clear
docker compose -f docker-compose.prod.yml exec backend php artisan cache:clear
```

### Automated Deployment (CI/CD)

#### Using GitHub Actions:
Create `.github/workflows/deploy.yml`:
```yaml
name: Deploy to Production

on:
  push:
    branches: [ main ]

jobs:
  deploy:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v2

      - name: Deploy to EC2
        uses: appleboy/ssh-action@master
        with:
          host: ${{ secrets.EC2_HOST }}
          username: ubuntu
          key: ${{ secrets.EC2_SSH_KEY }}
          script: |
            cd /home/ubuntu/www/moving
            git pull origin main
            docker compose -f docker-compose.prod.yml build
            docker compose -f docker-compose.prod.yml up -d
            docker compose -f docker-compose.prod.yml exec -T backend php artisan migrate --force
```

---

## Performance Optimization

### 1. Enable HTTP/2
Already configured in nginx (nginx/conf.d/production.conf:10)

### 2. Enable Gzip Compression
Add to nginx configuration:
```nginx
gzip on;
gzip_vary on;
gzip_min_length 1024;
gzip_types text/plain text/css text/xml text/javascript application/x-javascript application/xml+rss application/json;
```

### 3. Laravel Optimization
```bash
docker compose -f docker-compose.prod.yml exec backend php artisan config:cache
docker compose -f docker-compose.prod.yml exec backend php artisan route:cache
docker compose -f docker-compose.prod.yml exec backend php artisan view:cache
```

### 4. CDN (CloudFront)
- Create CloudFront distribution
- Point to your domain
- Cache static assets (/assets/, /moving/assets/)
- Reduce load on origin server

---

## Security Best Practices

### 1. IAM Role for EC2
Create an IAM role with policies for:
- CloudWatch Logs (write)
- S3 (read/write for backups)
- Systems Manager (for secure access)

### 2. Systems Manager Session Manager
- Use instead of SSH for access
- No need to expose port 22
- All sessions logged

### 3. Security Updates
```bash
# Enable automatic security updates
sudo apt-get install -y unattended-upgrades
sudo dpkg-reconfigure -plow unattended-upgrades
```

### 4. Environment Variables
- Never commit `.env` to Git
- Use AWS Secrets Manager for sensitive data
- Rotate credentials regularly

### 5. Regular Security Audits
```bash
# Update all packages
sudo apt-get update && sudo apt-get upgrade -y

# Update Docker images
docker compose -f docker-compose.prod.yml pull
docker compose -f docker-compose.prod.yml up -d
```

---

## Scaling Considerations

### Horizontal Scaling (Multiple Instances)
1. **Use Application Load Balancer**
2. **Shared file storage**: EFS for uploads
3. **External cache**: ElastiCache Redis
4. **External database**: RDS MySQL
5. **Session management**: Database or Redis sessions

### Vertical Scaling (Bigger Instance)
- Upgrade to larger instance type (t3.large, t3.xlarge)
- More suitable for initial growth

---

## Cost Optimization

### Estimated Monthly Costs (us-east-1)

```
EC2 Instance (t3.medium):           ~$30/month
Elastic IP (associated):            $0/month
EBS Volume (50 GB GP3):            ~$5/month
Data Transfer (100 GB):            ~$9/month
RDS MySQL (db.t3.medium, optional): ~$60/month
Route 53 Hosted Zone:              ~$0.50/month
---------------------------------------------
Total (without RDS):                ~$44/month
Total (with RDS):                   ~$104/month
```

### Savings:
1. **Reserved Instances**: Save up to 75% on EC2 costs
2. **Savings Plans**: Flexible commitment-based discounts
3. **Turn off dev/staging during off-hours**

---

## Troubleshooting

### Container not starting
```bash
# Check logs
docker compose -f docker-compose.prod.yml logs <service-name>

# Check container status
docker compose -f docker-compose.prod.yml ps

# Restart specific service
docker compose -f docker-compose.prod.yml restart <service-name>
```

### Database connection issues
```bash
# Check MySQL is running
docker compose -f docker-compose.prod.yml ps mysql

# Check MySQL logs
docker compose -f docker-compose.prod.yml logs mysql

# Test connection
docker compose -f docker-compose.prod.yml exec backend php artisan tinker
# Then run: DB::connection()->getPdo();
```

### Port already in use
```bash
# Find process using port
sudo lsof -i :80
sudo lsof -i :443

# Stop process or restart Docker
sudo systemctl restart docker
```

### Out of disk space
```bash
# Check disk usage
df -h

# Clean up Docker
docker system prune -a --volumes

# Clean up old logs
sudo journalctl --vacuum-time=7d
```

---

## Quick Commands Reference

```bash
# Start all services
docker compose -f docker-compose.prod.yml up -d

# Stop all services
docker compose -f docker-compose.prod.yml down

# View logs
docker compose -f docker-compose.prod.yml logs -f

# Restart a service
docker compose -f docker-compose.prod.yml restart nginx

# Execute command in container
docker compose -f docker-compose.prod.yml exec backend php artisan <command>

# Access MySQL
docker compose -f docker-compose.prod.yml exec mysql mysql -u root -p

# Check disk usage
docker system df
```

---

## Support Contacts

- **Application Issues**: Check logs in `/home/ubuntu/www/moving`
- **AWS Issues**: AWS Support (if on support plan)
- **Domain Issues**: Contact domain registrar
- **SSL Issues**: Let's Encrypt Community Forums

---

**Last Updated**: October 21, 2025
**Document Version**: 1.0
