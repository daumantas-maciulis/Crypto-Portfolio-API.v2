# Crypto Portfolio API

## Endpoints:

### Registration

``
POST /api/v1/create-account
``

Into this endpoint user must provide JSON with email and password:

````json
{
  "email": "adminas@admin.com",
  "password": "labas"
}
````

If registration ins successful User wil get API token which will identity user:

````json
"Your X-AUTH-TOKEN is: 54136cfe193523985bace1818c25ce6f57f6fa99282ca75f1d774b5d1669"
````

Later with each request User in Header must provide this token

```json
"X-AUTH-TOKEN: 54136cfe193523985bace1818c25ce6f57f6fa99282ca75f1d774b5d1669"
```

### Login

User can retrieve his token sending request to: </br>

``
POST /api/v1/login
``

```json
{
  "email": "admin@admin.csom",
  "password": "labas"
}
```

### Post new asset

When user is logged in, he can add new assets into his wallet.

Validation is applied for currency type, only BTC, ETH, IOTA are allowed, and currency values must be positive ir zero.

``
POST /api/v1/asset
`` </br>

Request JSON:

```json
{
  "label": "Etherium Vallet",
  "currency": "ETH",
  "value": 10
}
```

Response to this request:

```json
{
  "id": 40,
  "label": "s",
  "currency": "ETH",
  "value": 10,
  "priceInUsd": 28307.68
}
```

### Get all assets:

When User send request to this endpoint, he will get all of his assets

``
GET /api/v1/asset
``

Response to this request:

```json
  [
      {
        "id": 41,
        "label": "Etherium vallet",
        "currency": "ETH",
        "value": 10,
        "priceInUsd": 28307.68
      },
      {
        "id": 42,
        "label": "Bitcoin vallet",
        "currency": "BTC",
        "value": 10,
        "priceInUsd": 387798.04
      }
]
```

### Get one asset

User can preview one of his assets

``
    GET /api/v1/asset/{id}
``

Response to this request:
````json
{
    "id": 43,
    "label": "Bitcoin vallet",
    "currency": "BTC",
    "value": 10,
    "priceInUsd": 387798.04
}
````

### Delete one asset

User can delete asset which belongs to him

``
    DELETE /api/v1/asset/{id}
``

### Update asset

When user is logged in, he can add update his asset.

Validation is applied for currency type, only BTC, ETH, IOTA are allowed, and currency values must be positive ir zero.

``
PATCH /api/v1/asset/{id}
`` </br>

Request JSON:

```json
{
  "label": "Etherium Vallet",
  "currency": "ETH",
  "value": 10
}
```

Response to this request:

```json
{
  "id": 40,
  "label": "s",
  "currency": "ETH",
  "value": 10,
  "priceInUsd": 28307.68
}
```