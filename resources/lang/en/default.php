<?php

return [
    'plans' => [
        'title' => 'Plans',
        'subscribed' => 'Subscribed',
        'subscribe' => 'Subscribe',
        'cancel' => 'Cancel',
        'notify' => [
            'already_subscribed' => [
                'title' => 'Already Subscribed',
                'body' => "You're already subscribed to another plan. Cancel it before you subscribe to another plan.",
            ],
            'not_subscribed' => [
                'title' => 'Not Subscribed',
                'body' => "You're not subscribed to this plan. Subscribe before you cancel",
            ],
            'subscription_cant_canceled' => [
                'title' => 'Subscription Cant be Canceled',
                'body' => 'You cannot cancel this subscription.',
            ],
            'subscription_canceled' => [
                'title' => 'ubscription Canceled',
                'body' => 'Your subscription has been canceled.',
            ],
        ],
    ],

    'billing-portal' => [
        'title' => 'Billing Portal',
    ],
];
