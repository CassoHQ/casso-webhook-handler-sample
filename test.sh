curl --location --request POST 'http://localhost:3000/webhook_handler.php' \
--header 'secure-token: eogrBiWqaq' \
--header 'Content-Type: application/json' \
--data-raw '{
    "error": 0,
    "data": [
        {
            "id" : 1, 
            "tid": "TF80307914",
            "amount": 200500,
            "description": "DH35",
            "cusum_balance": 15900500,
            "when": "2020-11-02",
            "subAccId": "123456789",
            "bank_sub_acc_id": "123456789"
        }
    ]
}'
