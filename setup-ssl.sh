#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting SSL certificate setup for mooweemoving.com${NC}"

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run this script as root (use sudo)${NC}"
    exit 1
fi

# Update system
echo -e "${YELLOW}Updating system packages...${NC}"
yum update -y

# Install certbot
echo -e "${YELLOW}Installing certbot...${NC}"
yum install -y certbot python3-certbot-nginx

# Stop nginx container temporarily
echo -e "${YELLOW}Stopping nginx container...${NC}"
cd /var/www/moving-full-app
docker-compose -f docker-compose.prod.yml stop nginx

# Start nginx on host for certbot
echo -e "${YELLOW}Starting nginx on host for certificate verification...${NC}"
systemctl start nginx
systemctl enable nginx

# Create temporary nginx config for certbot
echo -e "${YELLOW}Creating temporary nginx configuration...${NC}"
cat > /etc/nginx/conf.d/mooweemoving.conf << 'EOF'
server {
    listen 80;
    server_name mooweemoving.com www.mooweemoving.com;
    
    location /.well-known/acme-challenge/ {
        root /var/www/html;
    }
    
    location / {
        return 301 http://34.201.58.212$request_uri;
    }
}
EOF

# Create webroot directory
mkdir -p /var/www/html/.well-known/acme-challenge

# Test nginx configuration
nginx -t

# Reload nginx
systemctl reload nginx

# Get SSL certificate
echo -e "${YELLOW}Requesting SSL certificate from Let's Encrypt...${NC}"
certbot certonly --webroot \
    --webroot-path=/var/www/html \
    --email admin@mooweemoving.com \
    --agree-tos \
    --no-eff-email \
    -d mooweemoving.com \
    -d www.mooweemoving.com

# Check if certificate was obtained successfully
if [ $? -eq 0 ]; then
    echo -e "${GREEN}SSL certificate obtained successfully!${NC}"
    
    # Stop host nginx
    systemctl stop nginx
    systemctl disable nginx
    
    # Copy certificates to project directory
    echo -e "${YELLOW}Copying certificates to project directory...${NC}"
    mkdir -p /var/www/moving-full-app/ssl
    cp /etc/letsencrypt/live/mooweemoving.com/fullchain.pem /var/www/moving-full-app/ssl/
    cp /etc/letsencrypt/live/mooweemoving.com/privkey.pem /var/www/moving-full-app/ssl/
    chown -R ec2-user:ec2-user /var/www/moving-full-app/ssl/
    chmod 600 /var/www/moving-full-app/ssl/privkey.pem
    chmod 644 /var/www/moving-full-app/ssl/fullchain.pem
    
    # Update nginx configuration for HTTPS
    echo -e "${YELLOW}Updating nginx configuration for HTTPS...${NC}"
    cat > /var/www/moving-full-app/nginx/conf.d/prod.conf << 'EOF'
# HTTP server - redirect to HTTPS
server {
    listen 80;
    server_name mooweemoving.com www.mooweemoving.com;
    return 301 https://$server_name$request_uri;
}

# HTTPS server
server {
    listen 443 ssl http2;
    server_name mooweemoving.com www.mooweemoving.com;
    root /var/www/frontend;
    index index.html;

    # SSL configuration
    ssl_certificate /etc/ssl/fullchain.pem;
    ssl_certificate_key /etc/ssl/privkey.pem;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;

    # Security headers
    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-XSS-Protection "1; mode=block";
    add_header X-Content-Type-Options "nosniff";
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;
    add_header Content-Security-Policy "default-src 'self' https: data: blob:; script-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; style-src 'self' 'unsafe-inline' https: data: blob:; img-src 'self' https: data: blob:; font-src 'self' https: data:; connect-src 'self' https: wss:;" always;

    # Gzip compression
    gzip on;
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml application/xml+rss text/javascript;
    gzip_comp_level 6;
    gzip_min_length 1000;

    # Frontend static files
    location / {
        try_files $uri $uri/ /index.html;
        add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0";
        expires -1;
    }

    # Prevent caching of index.html
    location = /index.html {
        add_header Cache-Control "no-store, no-cache, must-revalidate, proxy-revalidate, max-age=0";
        expires -1;
    }

    # Handle static assets
    location /moving/assets/ {
        alias /var/www/frontend/assets/;
        try_files $uri =404;
        add_header Cache-Control "public, no-transform";
        expires 7d;
    }

    # Handle favicon
    location = /moving/favicon.ico {
        alias /var/www/frontend/favicon.ico;
        access_log off;
        log_not_found off;
        expires 7d;
        add_header Cache-Control "public, no-transform";
    }

    # Backend API
    location /api {
        proxy_pass http://backend:9000;
        proxy_http_version 1.1;
        proxy_set_header Upgrade $http_upgrade;
        proxy_set_header Connection 'upgrade';
        proxy_set_header Host $host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_set_header X-Forwarded-For $proxy_add_x_forwarded_for;
        proxy_set_header X-Forwarded-Proto $scheme;
        proxy_cache_bypass $http_upgrade;
    }

    # Error pages
    error_page 404 /index.html;
    error_page 500 502 503 504 /50x.html;
    location = /50x.html {
        root /var/www/frontend;
    }
}
EOF

    # Update docker-compose to include port 443 and SSL volume
    echo -e "${YELLOW}Updating docker-compose configuration...${NC}"
    sed -i 's/ports:/ports:\n      - "443:443"/' /var/www/moving-full-app/docker-compose.prod.yml
    sed -i 's|volumes:|volumes:\n      - ./ssl:/etc/ssl|' /var/www/moving-full-app/docker-compose.prod.yml
    
    # Restart nginx container
    echo -e "${YELLOW}Restarting nginx container...${NC}"
    cd /var/www/moving-full-app
    docker-compose -f docker-compose.prod.yml up -d nginx
    
    # Set up auto-renewal
    echo -e "${YELLOW}Setting up automatic certificate renewal...${NC}"
    (crontab -l 2>/dev/null; echo "0 12 * * * /usr/bin/certbot renew --quiet") | crontab -
    
    echo -e "${GREEN}SSL setup completed successfully!${NC}"
    echo -e "${GREEN}Your site should now be accessible at https://mooweemoving.com${NC}"
    
else
    echo -e "${RED}Failed to obtain SSL certificate${NC}"
    echo -e "${YELLOW}Please check the error messages above and try again${NC}"
    
    # Cleanup
    systemctl stop nginx
    systemctl disable nginx
    rm -f /etc/nginx/conf.d/mooweemoving.conf
    
    # Restart nginx container
    cd /var/www/moving-full-app
    docker-compose -f docker-compose.prod.yml up -d nginx
    
    exit 1
fi

echo -e "${GREEN}Setup complete!${NC}" 