curl --location --request POST 'http://localhost:3000/webhook_handler.php' \
--header 'secure-token: eogrBiWqaq' \
--header 'Content-Type: application/json' \
--data-raw '{
    "error": 0,
    "data": [
        {
            "id" : 1, 
            "when": "2020-11-02",
            "amount": 200500,
            "description": "DH35",
            "cusum_balance": 15900500,
            "tid": "TF80307914",
            "subAccId": "123456789",
            "order": "2020110200001"
        }
    ]
}'
