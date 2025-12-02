#!/bin/bash

# Colors for output
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}========================================${NC}"
echo -e "${BLUE}  Reloading Environment Variables${NC}"
echo -e "${BLUE}========================================${NC}"
echo ""

# Navigate to project directory
cd /home/ubuntu/www/moving

# Stop frontend and nginx
echo -e "${YELLOW}Stopping frontend and nginx...${NC}"
docker compose -f docker-compose.prod.yml stop frontend nginx

# Rebuild frontend with new env variables
echo -e "${YELLOW}Rebuilding frontend with updated .env...${NC}"
docker compose -f docker-compose.prod.yml build --no-cache frontend

# Start all services
echo -e "${YELLOW}Starting all services...${NC}"
docker compose -f docker-compose.prod.yml up -d

# Wait for frontend to build
echo -e "${YELLOW}Waiting for frontend to build (20 seconds)...${NC}"
sleep 20

# Show status
echo ""
echo -e "${GREEN}========================================${NC}"
echo -e "${GREEN}  Services Status${NC}"
echo -e "${GREEN}========================================${NC}"
docker compose -f docker-compose.prod.yml ps

echo ""
echo -e "${GREEN}✓ Environment variables reloaded!${NC}"
echo -e "${GREEN}✓ Frontend rebuilt with new .env${NC}"
echo -e "${GREEN}✓ All services restarted${NC}"
echo ""
echo -e "${BLUE}Your site: https://mooweemoving.com${NC}"
echo ""
