#!/bin/bash
echo "Testing Registration..."
curl -s -X POST http://localhost:8000/api/register \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "name": "Test User",
    "email": "test@example.com",
    "password": "password",
    "password_confirmation": "password"
  }' | jq

echo -e "\n\nTesting Login..."
LOGIN_RESPONSE=$(curl -s -X POST http://localhost:8000/api/login \
  -H "Accept: application/json" \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "password": "password"
  }')
echo $LOGIN_RESPONSE | jq

TOKEN=$(echo $LOGIN_RESPONSE | jq -r .access_token)

echo -e "\n\nTesting Protected Route (/api/user)..."
curl -s -X GET http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" | jq

echo -e "\n\nTesting Logout..."
curl -s -X POST http://localhost:8000/api/logout \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" | jq

echo -e "\n\nTesting Protected Route AGALIN (Should Fail)..."
curl -s -X GET http://localhost:8000/api/user \
  -H "Accept: application/json" \
  -H "Authorization: Bearer $TOKEN" | jq
