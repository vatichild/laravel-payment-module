<?php
return [
    'gateway' => 'datatrans',
    'datatrans' => [
        'url' => env('DATATRANS_URL', 'https://api.sandbox.datatrans.com/v1'),
        'merchant_id' => env('DATATRANS_MERCHANT_ID', ''),
        'password' => env('DATATRANS_MERCHANT_PASSWORD', ''),
        'headers' => [
            'Content-Type' => 'application/json',
            'Authorization' => 'Basic '. env('DATATRANS_AUTH_KEY', '') // Basic base64_encode(<merchant_id>:<password>)
        ],
        'init' => [
            //VIS,ECA,AMX,CUP,DIN,JCB,MAU,DNK... (https://docs.datatrans.ch/docs/payment-methods)
            'paymentMethods' => [
                "VIS",
                "ECA"
            ],
            'autoSettle' => true,
            'option' => [
                'createAlias' => true,
            ],
            'redirect' => [
                "successUrl"=> "https://7620-188-169-130-150.ngrok-free.app/datatrans/success",
                "cancelUrl"=> "https://7620-188-169-130-150.ngrok-free.app/datatrans/cancel",
                "errorUrl" => "https://7620-188-169-130-150.ngrok-free.app/datatrans/error"
            ],
            "theme" => [
                "name" => "DT2015", 
                "configuration" => [
                   "brandColor" => "#FFFFFF", 
                   "logoBorderColor" => "#A1A1A1", 
                   "brandButton" => "#A1A1A1", 
                   "payButtonTextColor" => "#FFFFFF", 
                   "logoSrc" => "https://development.paybetter.redberry.work/_next/static/media/new_logo.478d8a5d.svg", 
                   "logoType" => "circle", 
                   "initialView" => "list" 
                ] 
             ] 
        ]
    ]
];