#!/bin/bash

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

echo -e "${GREEN}Starting SSL certificate setup for alexbuildservice.net${NC}"

# Check if running as root
if [ "$EUID" -ne 0 ]; then
    echo -e "${RED}Please run this script as root (use sudo)${NC}"
    exit 1
fi

# Stop nginx container temporarily
echo -e "${YELLOW}Stopping nginx container...${NC}"
cd /home/ubuntu/www/moving
docker-compose -f docker-compose.prod.yml stop nginx

# Get SSL certificate using standalone mode (port 80 is free)
echo -e "${YELLOW}Requesting SSL certificate from Let's Encrypt...${NC}"
certbot certonly --standalone \
    --email admin@alexbuildservice.net \
    --agree-tos \
    --no-eff-email \
    -d alexbuildservice.net \
    -d www.alexbuildservice.net

# Check if certificate was obtained successfully
if [ $? -eq 0 ]; then
    echo -e "${GREEN}SSL certificate obtained successfully!${NC}"

    # Copy certificates to project directory
    echo -e "${YELLOW}Copying certificates to project directory...${NC}"
    mkdir -p /home/ubuntu/www/moving/ssl
    cp /etc/letsencrypt/live/alexbuildservice.net/fullchain.pem /home/ubuntu/www/moving/ssl/alexbuild-fullchain.pem
    cp /etc/letsencrypt/live/alexbuildservice.net/privkey.pem /home/ubuntu/www/moving/ssl/alexbuild-privkey.pem
    chown -R ubuntu:ubuntu /home/ubuntu/www/moving/ssl/
    chmod 600 /home/ubuntu/www/moving/ssl/alexbuild-privkey.pem
    chmod 644 /home/ubuntu/www/moving/ssl/alexbuild-fullchain.pem

    # Restart nginx container
    echo -e "${YELLOW}Starting nginx container...${NC}"
    cd /home/ubuntu/www/moving
    docker-compose -f docker-compose.prod.yml start nginx

    # Set up auto-renewal with hook to copy certificates and restart nginx
    echo -e "${YELLOW}Setting up automatic certificate renewal...${NC}"
    (crontab -l 2>/dev/null; echo "0 4 * * * /usr/bin/certbot renew --quiet --deploy-hook 'cp /etc/letsencrypt/live/alexbuildservice.net/*.pem /home/ubuntu/www/moving/ssl/ && mv /home/ubuntu/www/moving/ssl/fullchain.pem /home/ubuntu/www/moving/ssl/alexbuild-fullchain.pem && mv /home/ubuntu/www/moving/ssl/privkey.pem /home/ubuntu/www/moving/ssl/alexbuild-privkey.pem && chown ubuntu:ubuntu /home/ubuntu/www/moving/ssl/*.pem && cd /home/ubuntu/www/moving && docker-compose -f docker-compose.prod.yml restart nginx'") | crontab -

    echo -e "${GREEN}SSL setup completed successfully!${NC}"
    echo -e "${GREEN}Your site should now be accessible at https://alexbuildservice.net${NC}"

else
    echo -e "${RED}Failed to obtain SSL certificate${NC}"
    echo -e "${YELLOW}Please check the error messages above and try again${NC}"

    # Restart nginx container
    cd /home/ubuntu/www/moving
    docker-compose -f docker-compose.prod.yml start nginx

    exit 1
fi

echo -e "${GREEN}Setup complete!${NC}"
