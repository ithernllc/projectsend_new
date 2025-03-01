<?php
// cancel_subscription.php

// Include the functions.php file to use the functions defined in it
$allowed_levels = array(9, 8);
require_once 'bootstrap.php';
log_in_required($allowed_levels);
 // Include your functions file where cancelSub() is defined
 // Include your DB connection setup

// Main logic to handle the request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    if (isset($_POST['user_id']) && isset($_POST['subscription_status'])) {
        $client_id = $_POST['user_id']; // Retrieving the user ID from POST
        $subscription_status = $_POST['subscription_status'];
        $edit_client = new \ProjectSend\Classes\Users($client_id);

        $client_arguments = $edit_client->cancelSub();
        // Call the cancelSub function to cancel the subscription
        // $result = cancelSub($user_id);
        
        // Return the result (success or error)
        echo $result;
    }
}
?>
