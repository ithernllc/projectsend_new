<?php
require_once 'stripe-php/vendor/autoload.php';

\Stripe\Stripe::setApiKey('sk_test_51QkX2yKgzD75154hk2tofNecxjKeZ6p6rpJ0CCLQ1AhhoYAMkoNh0xgDbvybBwnCGxA3OQ9Vh6aUDgbRz3JpV5I900mdEn5KGI'); // Your Secret Key

$customerId = 'cus_RhXJvgNVOJ5idE'; // Replace with your user's Stripe Customer ID

try {
    // Get the latest invoice for the customer
    $invoices = \Stripe\Invoice::all([
        'customer' => $customerId,
        'limit' => 1,
    ]);

    if (!empty($invoices->data)) {
        $latestInvoice = $invoices->data[0];
        
        // Check if the latest invoice payment failed
        if ($latestInvoice->status === 'open' && $latestInvoice->attempted && $latestInvoice->paid === false) {
            echo "Payment failed for subscription: " . $latestInvoice->subscription . "\n";

            // // TODO: Update the user's subscription status in the database
            // $dbh = new PDO("mysql:host=your_host;dbname=your_db", "username", "password");
            // $stmt = $dbh->prepare("UPDATE users SET subscription_status = 'past_due' WHERE stripe_customer_id = :customerId");
            // $stmt->bindParam(':customerId', $customerId);
            // $stmt->execute();
        } else {
            echo "Payment is successful or pending.\n";
        }
    } else {
        echo "No invoices found for this customer.\n";
    }
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
