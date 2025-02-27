<?php
require_once 'bootstrap.php';
$users = getUsersWithTrialEndingTomorrow();

// Send email to each user
foreach ($users as $user) {
    $to = $user['email'];
    $subject = 'Your Trial Period is Ending Soon!';
    $message = "Dear User,

Your 30-day trial period will end on " . date('Y-m-d', strtotime('+1 day')) . ". 

Don't miss out on uninterrupted access! Upgrade now to continue enjoying our services.

Best regards,
Techassures";


    $email = new \ProjectSend\Classes\Emails;
    $email->send([
        'type' => 'trail_check',
        'to' => $to,
        'message' => $message,
    ]);


}

echo "Emails sent successfully.";
?>
