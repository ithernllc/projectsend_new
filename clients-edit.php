<?php
/**
 * Show the form to edit an existing client.
 */
$allowed_levels = array(9, 8, 0);
require_once 'bootstrap.php';
log_in_required($allowed_levels);

$active_nav = 'clients';

// Check if the id parameter is on the URI.
if (!isset($_GET['id'])) {
    exit_with_error_code(403);
}

$client_id = (int)$_GET['id'];
if (!client_exists_id($client_id)) {
    exit_with_error_code(403);
}

// Create the object
$edit_client = new \ProjectSend\Classes\Users($client_id);

$client_arguments = $edit_client->getProperties();

// Get groups where this client is member
$get_groups = new \ProjectSend\Classes\GroupsMemberships;
$get_arguments = [
    'client_id' => $client_id,
];
$found_groups = $get_groups->getGroupsByClient($get_arguments);

// Get current membership requests
$get_arguments['denied'] = 0;
$found_requests = $get_groups->getMembershipRequests($get_arguments);

// Form type
if (CURRENT_USER_LEVEL != 0) {
    $clients_form_type = 'edit_client';
    $ignore_size = false;
} else {
    $clients_form_type = 'edit_client_self';
    define('EDITING_SELF_ACCOUNT', true);
    $ignore_size = true;
}

// Compare the client editing this account to the on the db.
if (CURRENT_USER_LEVEL == 0) {
    if (isset($client_arguments) && CURRENT_USER_USERNAME != $client_arguments['username']) {
        exit_with_error_code(403);
    }
}

if (isset($_GET['action']) && $_GET['action'] === 'cancel_subscription') {
    $user_id = intval($_GET['id']); // Sanitize the user_id input

    // Create an instance of the Users class and call cancelSub
    try {
        $edit_client = new \ProjectSend\Classes\Users($user_id);
        $result = $edit_client->cancelSub();

        if ($result) {
            // Redirect or display a success message
            $flash->success(__('Subscription Cancelled successfully'));
            header("Location:clients-edit.php?id=$user_id");
            exit();
        } else {
            // Redirect or display an error message
            header("Location:process.php?do=logout");
            exit();
        }
    } catch (Exception $e) {
        // Redirect or display an error message in case of an exception
        header("Location: subscription_success.php?status=exception&message=" . urlencode($e->getMessage()));
        exit();
    }
} 
if (isset($_GET['action']) && $_GET['action'] === 'update_billing') {
    $user_id = intval($_GET['id']); // Sanitize the user_id input

        // Create an instance of the Users class and call cancelSub
        try {
            $edit_client = new \ProjectSend\Classes\Users($user_id);
            $result = $edit_client->updatebilling();
            if ($result && filter_var($result, FILTER_VALIDATE_URL)) {
                // Redirect if $result is a valid URL
                header("Location: " . $result);
            } else {
                // Redirect to clients-edit.php if updatebilling() fails or does not return a URL
                header("Location: clients-edit.php?id=$user_id");
            }
        } catch (Exception $e) {
            // Redirect or display an error message in case of an exception
            header("Location: subscription_success.php?status=exception&message=" . urlencode($e->getMessage()));
            exit();
        }
} 
if ($_POST) {

    function updatePaymentMethod() {
    // Here, you would add your logic to interact with Stripe API
    // For example, to update payment methods, you might need to use the Stripe API
    // Here's just a placeholder for testing:
    return "Payment method updated successfully!";
}

// If the request is made via AJAX, call the function
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update_payment_method'])) {
    echo updatePaymentMethod();  // Call your function and return the response
    exit;  // Stop further script execution to prevent any other unwanted output
}
    /**
     * If the user is not an admin, check if the id of the client
     * that's being edited is the same as the current logged in one.
     */
    if (CURRENT_USER_LEVEL == 0 || CURRENT_USER_LEVEL == 7) {
        if ($client_id != CURRENT_USER_ID) {
            exit_with_error_code(403);
        }
    }

    /**
     * Clean the posted form values to be used on the user actions,
     * and again on the form if validation failed.
     * Also, overwrites the values gotten from the database so if
     * validation failed, the new unsaved values are shown to avoid
     * having to type them again.
     */
    $client_arguments = array(
        'id' => $client_id,
        'username' => $_POST['username'],
        'role' => 0,
        'first_name' => $_POST['first_name'],
        'last_name' => $_POST['last_name'],
        'email' => $_POST['email'],
        'address' => (isset($_POST["address"])) ? $_POST['address'] : null,
        'phone' => (isset($_POST["phone"])) ? $_POST['phone'] : null,
        'contact' => (isset($_POST["contact"])) ? $_POST['contact'] : null,
        'notify_upload' => (isset($_POST["notify_upload"])) ? 1 : 0,
        'max_file_size' => $client_arguments['max_file_size'],
        'can_upload_public' => $client_arguments['can_upload_public'],
        'active' => $client_arguments['active'],
        'type' => 'edit_client',
    );

    if ($ignore_size == false) {
        $client_arguments['max_file_size'] = (isset($_POST["max_file_size"])) ? $_POST["max_file_size"] : null;
    }

    if (CURRENT_USER_LEVEL != 0) {
        $client_arguments['can_upload_public'] = (isset($_POST["can_upload_public"])) ? 1 : 0;
        $client_arguments['active'] = (isset($_POST["active"])) ? 1 : 0;
    }

    /**
     * If the password field, or the verification are not completed,
     * send an empty value to prevent notices.
     */
    $client_arguments['password'] = (isset($_POST['password'])) ? $_POST['password'] : null;

    /** Validate the information from the posted form. */
    $edit_client->set($client_arguments);
    $edit_client->setType("existing_client");
    $edit_response = $edit_client->edit();

    $edit_groups = (!empty($_POST['groups_request'])) ? $_POST['groups_request'] : array();
    $memberships = new \ProjectSend\Classes\GroupsMemberships;
    $arguments = [
        'client_id' => $client_id,
        'group_ids' => $edit_groups,
        'request_by' => CURRENT_USER_USERNAME,
    ];

    if (in_array(CURRENT_USER_LEVEL, [8, 9])) {
        $memberships->clientEditGroups($arguments);
    } else {
        $memberships->updateMembershipRequests($arguments);
    }

    if ($edit_response['query'] == 1) {
        if ($client_id == CURRENT_USER_ID) {
            $flash->success(__('Profile edited successfully'));
        } else {
            $flash->success(__('Client saved successfully'));
        }
    } else {
        $flash->error(__('There was an error saving to the database'));
    }

    ps_redirect(BASE_URI . 'clients-edit.php?id=' . $client_id);
}

$page_title = __('Edit client', 'cftp_admin');
if (isset($client_arguments['username']) && CURRENT_USER_USERNAME == $client_arguments['username']) {
    $page_title = __('My account', 'cftp_admin');
}

$page_id = 'client_form';

include_once ADMIN_VIEWS_DIR . DS . 'header.php';

?>
<div class="row">
    <div class="col-12 col-sm-12 col-lg-6">
        <div class="white-box">
            <div class="white-box-interior text-center">
                <?php
                // If the form was submitted with errors, show them here.
                echo $edit_client->getValidationErrors();

                include_once FORMS_DIR . DS . 'clients.php';
                
                
                ?>
            </div>
            <div class="row">
                <?php
                    if ($client_arguments['subscription_status'] != 'pending_cancellation') {
                ?>
                    <div class="col-md-6 mb-3">
                        <?php include_once 'cancelsubscription.php'; ?>
                    </div>
                    <div class="col-md-6 mb-3">
                        <?php include_once 'updatebilling.php'; ?>
                    </div>
                <?php
                    }
                ?>
            </div>
        </div>
    </div>
</div>
<?php
include_once ADMIN_VIEWS_DIR . DS . 'footer.php';
