<?php

return [

    'default' => env('BROADCAST_DRIVER', 'log'),

    'connections' => [

        'log' => [
            'driver' => 'log',
        ],

        'pusher' => [
            'driver' => 'pusher',
            'key' => env('PUSHER_APP_KEY'),
            'secret' => env('PUSHER_APP_SECRET'),
            'app_id' => env('PUSHER_APP_ID'),
            'options' => [
                'cluster' => env('PUSHER_APP_CLUSTER'),
                'useTLS' => true,
            ],
        ],

        'null' => [
            'driver' => 'null',
        ],

    ],
];
