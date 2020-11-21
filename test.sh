curl --location --request POST 'http://localhost:3000/webhook_handler.php' \
--header 'secure-token: eogrBiWqaq' \
--header 'Content-Type: application/json' \
--data-raw '{
    "error": 0,
    "data": [
        {
            "when": "2020-11-02",
            "amount": -15200000,
            "description": "DH35",
            "cusum_balance": -15200000,
            "tid": "TF80307914",
            "subAccId": "123456789",
            "order": "2020110200001"
        }
    ]
}'
