<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Application Name & Branding
    |--------------------------------------------------------------------------
    */
    'name' => env('APP_NAME', 'GoMad'),
    'tagline' => 'Mobilitas orèng Madhurâ',
    'description' => 'Platform booking travel antar kota di Madura. Door-to-door service.',

    /*
    |--------------------------------------------------------------------------
    | URLs
    |--------------------------------------------------------------------------
    */
    'api_url' => env('API_URL', 'http://api.gomad.test'),
    'web_url' => env('APP_URL', 'http://web.gomad.test'),
    'landing_url' => env('LANDING_URL', 'http://gomad.test'),

    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    */
    'booking_code_prefix' => 'GM',
    'payment_timeout_minutes' => 30,
    'schedule_min_days_before' => 30,

    /*
    |--------------------------------------------------------------------------
    | Overload Rules
    |--------------------------------------------------------------------------
    */
    'overload_rules' => [
        'economy' => [
            'max_overload' => 2,
            'max_total' => 10,
        ],
        'premium' => [
            'max_overload' => 0,
        ],
        'charter' => [
            'max_overload' => 0,
        ],
        'rental' => [
            'max_overload' => 0,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Baggage Limits (kg per person)
    |--------------------------------------------------------------------------
    */
    'baggage_limits' => [
        'economy' => 15.00,
        'premium' => 20.00,
        'charter' => 25.00,
        'rental' => 0,
    ],

    /*
    |--------------------------------------------------------------------------
    | Commission Configuration
    |--------------------------------------------------------------------------
    */
    'commission_rate' => env('COMMISSION_RATE', 5),
    'warung_commission_rate' => env('WARUNG_COMMISSION_RATE', 2),

    /*
    |--------------------------------------------------------------------------
    | Service & Platform Fee
    |--------------------------------------------------------------------------
    */
    'service_fee' => env('SERVICE_FEE', 5000),
    'platform_fee_percent' => env('PLATFORM_FEE_PERCENT', 3),

    /*
    |--------------------------------------------------------------------------
    | Tour Configuration
    |--------------------------------------------------------------------------
    */
    'tour' => [
        'service_fee' => env('TOUR_SERVICE_FEE', 5000),
        'platform_fee_percent' => env('TOUR_PLATFORM_FEE_PERCENT', 3),
        'min_participants' => env('TOUR_MIN_PARTICIPANTS', 5),
        'max_participants' => env('TOUR_MAX_PARTICIPANTS', 20),
        'cancellation' => [
            'early_percent' => env('TOUR_CANCEL_EARLY_PCT', 15),   // > 7 hari
            'early_hours' => env('TOUR_CANCEL_EARLY_HOURS', 168),
            'mid_percent' => env('TOUR_CANCEL_MID_PCT', 30),       // 3-7 hari
            'mid_hours' => env('TOUR_CANCEL_MID_HOURS', 72),
            'late_percent' => env('TOUR_CANCEL_LATE_PCT', 50),     // 1-3 hari
            'late_hours' => env('TOUR_CANCEL_LATE_HOURS', 24),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Travel Cancellation Configuration
    |--------------------------------------------------------------------------
    */
    'travel_cancellation' => [
        'percent' => env('TRAVEL_CANCEL_PERCENT', 25),
        'hours_before' => env('TRAVEL_CANCEL_HOURS', 24),
        'refund_approval_limit' => env('TRAVEL_REFUND_APPROVAL_LIMIT', 100000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Rental Configuration
    |--------------------------------------------------------------------------
    */
    'rental' => [
        'service_fee' => env('RENTAL_SERVICE_FEE', 5000),
        'platform_fee_percent' => env('RENTAL_PLATFORM_FEE_PERCENT', 3),
        'cancellation' => [
            'early_percent' => env('RENTAL_CANCEL_EARLY_PCT', 10),   // > 7 hari
            'early_hours' => env('RENTAL_CANCEL_EARLY_HOURS', 168),
            'mid_percent' => env('RENTAL_CANCEL_MID_PCT', 25),       // 3-7 hari
            'mid_hours' => env('RENTAL_CANCEL_MID_HOURS', 72),
            'late_percent' => env('RENTAL_CANCEL_LATE_PCT', 50),     // 1-3 hari
            'late_hours' => env('RENTAL_CANCEL_LATE_HOURS', 24),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Referral Configuration
    |--------------------------------------------------------------------------
    */
    'referral' => [
        'discount_percent' => env('REFERRAL_DISCOUNT_PERCENT', 20),
        'max_discount' => env('REFERRAL_MAX_DISCOUNT', 30000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Withdrawal Configuration
    |--------------------------------------------------------------------------
    */
    'minimal_withdrawal' => env('MINIMAL_WITHDRAWAL', 100000),
    'withdrawal_admin_fee' => env('WITHDRAWAL_ADMIN_FEE', 5000),
    'auto_approve_limit' => env('AUTO_APPROVE_LIMIT', 5000000),

    /*
    |--------------------------------------------------------------------------
    | Payment Code Configuration (Warung GoMad)
    |--------------------------------------------------------------------------
    */
    'payment_code_prefix' => 'WM',
    'payment_code_expiry_hours' => 24,

    /*
    |--------------------------------------------------------------------------
    | Top Up Configuration
    |--------------------------------------------------------------------------
    */
    'topup_admin_fee' => env('TOPUP_ADMIN_FEE', 3500),

    /*
    |--------------------------------------------------------------------------
    | COD Configuration
    |--------------------------------------------------------------------------
    */
    'cod_min_deposit_default' => env('COD_MIN_DEPOSIT', 500000),

    /*
    |--------------------------------------------------------------------------
    | Settlement Configuration
    |--------------------------------------------------------------------------
    */
    'settlement_day' => 'monday',

    /*
    |--------------------------------------------------------------------------
    | Driver Configuration
    |--------------------------------------------------------------------------
    */
    'driver_min_rating' => 3.0,

    /*
    |--------------------------------------------------------------------------
    | Support
    |--------------------------------------------------------------------------
    */
    'support_phone' => env('SUPPORT_PHONE', '081234567890'),
    'support_email' => env('SUPPORT_EMAIL', 'support@gomad.id'),

    /*
    |--------------------------------------------------------------------------
    | Midtrans Configuration
    |--------------------------------------------------------------------------
    */
    'midtrans' => [
        'server_key' => env('MIDTRANS_SERVER_KEY'),
        'client_key' => env('MIDTRANS_CLIENT_KEY'),
        'merchant_id' => env('MIDTRANS_MERCHANT_ID'),
        'is_production' => env('MIDTRANS_IS_PRODUCTION', false),
        'snap_url' => env('MIDTRANS_SNAP_URL', 'https://app.sandbox.midtrans.com/snap/snap.js'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Twilio Configuration (WhatsApp)
    |--------------------------------------------------------------------------
    */
    'twilio' => [
        'sid' => env('TWILIO_SID'),
        'auth_token' => env('TWILIO_AUTH_TOKEN'),
        'whatsapp_from' => env('TWILIO_WHATSAPP_FROM'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Firebase Cloud Messaging (FCM)
    |--------------------------------------------------------------------------
    */
    'fcm' => [
        'server_key' => env('FCM_SERVER_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Google Maps
    |--------------------------------------------------------------------------
    */
    'google_maps' => [
        'api_key' => env('GOOGLE_MAPS_API_KEY'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Mobile App Configuration
    |--------------------------------------------------------------------------
    */
    'mobile_app' => [
        'play_store_url' => env('PLAY_STORE_URL', 'https://play.google.com/store/apps/details?id=id.gomad.app'),
        'app_store_url' => env('APP_STORE_URL', 'https://apps.apple.com/id/app/gomad/id123456789'),
        'deep_link_scheme' => 'gomad://',
        'min_android_version' => '6.0',
        'min_ios_version' => '14.0',
    ],

    /*
    |--------------------------------------------------------------------------
    | Gallery Limits
    |--------------------------------------------------------------------------
    */
    'gallery' => [
        'max_photos' => 10,
        'max_size_kb' => 2048,
    ],

    /*
    |--------------------------------------------------------------------------
    | Review Configuration
    |--------------------------------------------------------------------------
    */
    'review' => [
        'min_rating' => 1,
        'max_rating' => 5,
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
    'pagination' => [
        'default_per_page' => 15,
        'max_per_page' => 100,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache TTL (in minutes)
    |--------------------------------------------------------------------------
    */
    'cache_ttl' => [
        'schedule_search' => 5,
        'agency_profile' => 60,
        'platform_settings' => 60,
        'route_list' => 120,
        'city_list' => 1440,
    ],
];