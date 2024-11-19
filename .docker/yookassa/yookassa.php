<?php

foreach ($_SERVER as $key => $value) {
    if (preg_match('/^(HTTP|REQUEST).+$/', $key)) {
        error_log("$key = $value");
    }
}

header('Content-Type: application/json');
switch ($_SERVER['REQUEST_METHOD']) {
    case 'POST':
        echo created();
        break;
    default:
        echo status();
        break;
}

function created(): string
{
    $payment = json_decode(<<<JSON
    {
        "id": "22e12f66-000f-5000-8000-18db351245c7",
        "status": "pending",
        "paid": false,
        "amount": {
            "value": "2.00",
            "currency": "RUB"
        },
        "confirmation": {
            "type": "redirect",
            "return_url": "https://www.example.com/return_url",
            "confirmation_url": "https://yoomoney.ru/payments/external/confirmation?orderId=22e12f66-000f-5000-8000-18db351245c7"
        },
        "created_at": "2018-07-18T10:51:18.139Z",
        "description": "Заказ №72",
        "metadata": {},
        "payment_method": {
            "type": "bank_card",
            "id": "22e12f66-000f-5000-8000-18db351245c7",
            "saved": false
        },
        "recipient": {
            "account_id": "100500",
            "gateway_id": "100700"
        },
        "refundable": false,
        "test": false
    }
    JSON);

    $payment->id = uuid_create();

    return json_encode($payment);
}

function status(): string
{
    preg_match('#payments/([a-z0-9-]+)#', $_SERVER['REQUEST_URI'], $matches);
    $paymentId = $matches[1] ?? uuid_create();

    $payment = json_decode(<<<JSON
        {
            "id": "23d93cac-000f-5000-8000-126628f15141",
            "status": "pending",
            "paid": false,
            "amount": {
                "value": "100.00",
                "currency": "RUB"
            },
            "confirmation": {
                "type": "redirect",
                "confirmation_url": "https://asinosoft.ru/vpn.html"
            },
            "created_at": "2019-01-22T14:30:45.129Z",
            "description": "Заказ №1",
            "metadata": {},
            "recipient": {
                "account_id": "100500",
                "gateway_id": "100700"
            },
            "refundable": false,
            "test": false
        }
    JSON);

    $payment->id = $paymentId;
    $payment->status = ['waiting_for_capture', 'succeeded', 'canceled'][random_int(0, 2)];

    return json_encode($payment);
}

function uuid_create()
{
    $data = random_bytes(16);

    $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
    $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

    return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
}
