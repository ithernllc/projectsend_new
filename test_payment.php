<?php


require_once 'stripe-php/vendor/autoload.php';

// \Stripe\Stripe::setApiKey('sk_test_51QkX2yKgzD75154hk2tofNecxjKeZ6p6rpJ0CCLQ1AhhoYAMkoNh0xgDbvybBwnCGxA3OQ9Vh6aUDgbRz3JpV5I900mdEn5KGI'); // Replace with your test secret key

// try {
//     // Create a new customer
//     $customer = \Stripe\Customer::create([
//         'email' => 'testuser@example.com',
//         'name' => 'Test User',
//         'description' => 'This is a test customer.',
//     ]);

//     echo 'Customer created successfully: ' . $customer->id;
// } catch (\Stripe\Exception\ApiErrorException $e) {
//     echo 'Error: ' . $e->getMessage();
// }



\Stripe\Stripe::setApiKey('sk_test_51QkX2yKgzD75154hk2tofNecxjKeZ6p6rpJ0CCLQ1AhhoYAMkoNh0xgDbvybBwnCGxA3OQ9Vh6aUDgbRz3JpV5I900mdEn5KGI');



try {
    $session = \Stripe\BillingPortal\Session::create([
        'customer' => 'cus_RhXJvgNVOJ5idE',
        'return_url' => 'https://yourwebsite.com/account',
    ]);

    echo "Billing Portal URL: <a href='" . htmlspecialchars($session->url) . "' target='_blank'>Click here</a>";
} catch (\Stripe\Exception\ApiErrorException $e) {
    echo "Error: " . $e->getMessage();
}


?>