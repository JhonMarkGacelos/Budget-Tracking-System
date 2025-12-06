#!/bin/bash

# Budget Tracking System - API Testing Guide
# This script provides curl examples for testing the Budget Tracking System API

BASE_URL="http://localhost:8000/api"
ADMIN_EMAIL="admin@budgettracking.com"
ADMIN_PASSWORD="admin@123456"

echo "================================"
echo "Budget Tracking System API Tests"
echo "================================"

# 1. Login
echo -e "\n1. LOGIN - Getting Admin Token..."
LOGIN_RESPONSE=$(curl -s -X POST "$BASE_URL/login" \
  -H "Content-Type: application/json" \
  -d "{
    \"email\": \"$ADMIN_EMAIL\",
    \"password\": \"$ADMIN_PASSWORD\"
  }")

echo "Login Response:"
echo "$LOGIN_RESPONSE" | jq '.'

# Extract token from response
TOKEN=$(echo "$LOGIN_RESPONSE" | jq -r '.token')
echo "Token: $TOKEN"

# 2. Get Current User
echo -e "\n2. GET CURRENT USER..."
curl -s -X GET "$BASE_URL/user" \
  -H "Authorization: Bearer $TOKEN" | jq '.'

# 3. List Users
echo -e "\n3. LIST USERS..."
curl -s -X GET "$BASE_URL/users" \
  -H "Authorization: Bearer $TOKEN" | jq '.'

# 4. List Departments
echo -e "\n4. LIST BUDGET REQUESTS..."
curl -s -X GET "$BASE_URL/budget-requests" \
  -H "Authorization: Bearer $TOKEN" | jq '.'

# 5. Get Pending Approvals
echo -e "\n5. GET PENDING APPROVALS..."
curl -s -X GET "$BASE_URL/approvals/pending" \
  -H "Authorization: Bearer $TOKEN" | jq '.'

# 6. Get Approval Statistics
echo -e "\n6. GET APPROVAL STATISTICS..."
curl -s -X GET "$BASE_URL/approvals/statistics" \
  -H "Authorization: Bearer $TOKEN" | jq '.'

echo -e "\n================================"
echo "API Testing Complete"
echo "================================"
