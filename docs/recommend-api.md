# Recommendation API

GET /api/v1/book/{book_id}/recommendations
Authorization: Bearer {token}

## 200

````json
{
    "id": 1,
    "ts": 121212,
    "items": [
      {
        "id":  1
      }
    ]
}
````
## 403
````json
{
  "error": "access denied"
}
````
