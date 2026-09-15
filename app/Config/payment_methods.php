<?php

declare(strict_types=1);

return [
    'bkash' => [
        'id' => 'bkash',
        'name' => 'bKash',
        'code' => 'BKASH',
        'brand_color' => '#E2136E',
        'badge_bg' => 'bg-pink-50 text-pink-700 border-pink-200',
        'default_number' => '01700000001',
        'default_type' => 'Merchant',
        'instructions' => "1. Dial *247# or open bKash App\n2. Select 'Make Payment' or 'Send Money'\n3. Enter the Number: {NUMBER} ({TYPE})\n4. Enter the exact Amount: {AMOUNT}\n5. Enter Reference: {REF}\n6. Enter your PIN to confirm\n7. Copy the TrxID and enter below",
    ],
    'nagad' => [
        'id' => 'nagad',
        'name' => 'Nagad',
        'code' => 'NAGAD',
        'brand_color' => '#F7941D',
        'badge_bg' => 'bg-orange-50 text-orange-700 border-orange-200',
        'default_number' => '01800000002',
        'default_type' => 'Merchant',
        'instructions' => "1. Dial *167# or open Nagad App\n2. Select 'Merchant Pay' or 'Send Money'\n3. Enter the Number: {NUMBER} ({TYPE})\n4. Enter the exact Amount: {AMOUNT}\n5. Enter Reference: {REF}\n6. Enter your PIN to confirm\n7. Copy the TrxID and enter below",
    ],
    'rocket' => [
        'id' => 'rocket',
        'name' => 'Rocket',
        'code' => 'ROCKET',
        'brand_color' => '#8C3494',
        'badge_bg' => 'bg-purple-50 text-purple-700 border-purple-200',
        'default_number' => '01900000003-8',
        'default_type' => 'Merchant',
        'instructions' => "1. Dial *322# or open Rocket App\n2. Select 'Merchant Pay' or 'Send Money'\n3. Enter the Number: {NUMBER} ({TYPE})\n4. Enter the exact Amount: {AMOUNT}\n5. Enter Reference: {REF}\n6. Enter your PIN to confirm\n7. Copy the TrxID and enter below",
    ],
];
