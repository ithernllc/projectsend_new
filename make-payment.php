<?php
require_once 'bootstrap.php'; // Load your dependencies, including database and email classes.
require_once 'stripe-php/vendor/autoload.php'; // Load Stripe library.

use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\Exception\ApiErrorException;

/**
 * Fetch users whose payment is due today.
 * Replace this with your actual implementation.
 */
$usersDueForPayment = getUsersWithPaymentDueToday();

// Set your Stripe secret key
Stripe::setApiKey('sk_test_51QkX2yKgzD75154hk2tofNecxjKeZ6p6rpJ0CCLQ1AhhoYAMkoNh0xgDbvybBwnCGxA3OQ9Vh6aUDgbRz3JpV5I900mdEn5KGI');

// Process payments for each user
foreach ($usersDueForPayment as $user) {
    $email = $user['email'];
    switch ($user['plan']) {
        case 'monthly':
            $amount = 499; // $4.99 in cents
            break;
        case 'six_months':
            $amount = 2499; // $24.99 in cents
            break;
        case 'yearly':
            $amount = 4599; // $45.99 in cents
            break;
        default:
            throw new Exception("Unknown subscription type for user ID {$user['id']}.");
    }
    $currency = 'usd'; 

    // try {
        // Create a PaymentIntent for the user
        
        $paymentMethod = \Stripe\PaymentMethod::create([
            'type' => 'card',
            'card' => [
                'number' => $user['card_number'],   // The card number (never store CVV)
                'exp_month' => substr($user['expiry_date'], 0, 2), // Expiry month (e.g., 12)
                'exp_year' => substr($user['expiry_date'], 3, 2),  // Expiry year (e.g., 24)
                'cvc' => $user['cvv']  // CVV (which is highly discouraged to store in the DB)
            ],
        ]);
        print_r("Payment method created successfully: " . print_r($paymentMethod, true));
        // Create a PaymentIntent to handle the payment
        $paymentIntent = PaymentIntent::create([
            'amount' => $amount, 
            'currency' => 'usd',
            'payment_method' => $paymentMethod->id, // Use the PaymentMethod created
            'confirm' => true, // Automatically confirm the payment
        ]);

        // Check if the payment succeeded
        if ($paymentIntent->status === 'succeeded') {
            // Update the user's subscription status in the database
            markSubscriptionAsPaid($user['id']); // Replace with your database update logic

            // Send successful payment email
            sendEmail(
                $email,
                'Subscription Renewal Successful',
                "Dear {$user['name']},\n\nYour subscription has been successfully renewed. Thank you for staying with us!\n\nBest regards,\nTechassures"
            );
        } else {
            // If payment fails for any reason
            echo'Payment failed.';
        }
    // } catch (\Exception $e) {
        // Log the error
        // error_log("Payment failed for user {$user['id']}: " . $e->getMessage());

        // Pause the user's subscription in the database
        pauseSubscription($user['id']); // Replace with your database update logic

        // Send payment failure email
        sendEmail(
            $email,
            'Subscription Renewal Failed',
            "Dear {$user['name']},\n\nUnfortunately, your subscription renewal payment failed. Your subscription has been paused. Please update your payment details to continue enjoying our services.\n\nBest regards,\nTechassures"
        );
    // }
}

echo "Payment processing completed.";

/**
 * Send an email (helper function).
 */
function sendEmail($to, $subject, $message)
{
    $email = new \ProjectSend\Classes\Emails;
    $email->send([
        'type' => 'subscription_payment',
        'to' => $to,
        'message' => $message,
    ]);
}
?>
