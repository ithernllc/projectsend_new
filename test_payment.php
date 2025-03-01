<?php


require_once 'stripe-php/vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51QkX2yKgzD75154hk2tofNecxjKeZ6p6rpJ0CCLQ1AhhoYAMkoNh0xgDbvybBwnCGxA3OQ9Vh6aUDgbRz3JpV5I900mdEn5KGI');

$products = \Stripe\Product::all(['limit' => 100]);

foreach ($products->data as $product) {
    echo "Product ID: " . $product->id . " | Name: " . $product->name . "\n";
}


?>