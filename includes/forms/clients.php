<?php
/**
 * Contains the form that is used when adding or editing clients.
 */
$name_placeholder = __("Will be visible on the client's file list", 'cftp_admin');

$clients_can_select_group = get_option('clients_can_select_group');

switch ($clients_form_type) {
        /** User is creating a new client */
    case 'new_client':
        $submit_value = __('Add client', 'cftp_admin');
        $disable_user = false;
        $require_pass = true;
        $form_action = 'clients-add.php';
        $info_box = true;
        $extra_fields = true;
        $group_field = true;
        $group_label = __('Groups', 'cftp_admin');
        $ignore_size = false;
        break;
        /** User is editing an existing client */
    case 'edit_client':
        $submit_value = __('Save client', 'cftp_admin');
        $disable_user = true;
        $require_pass = false;
        $form_action = 'clients-edit.php?id=' . $client_id;
        $info_box = false;
        $extra_fields = true;
        $group_field = true;
        $group_label = __('Groups', 'cftp_admin');
        $ignore_size = false;
        break;
        /** A client is creating a new account for himself */
    case 'new_client_self':
        $submit_value = (get_option('clients_auto_approve') == 1) ? __('Create account', 'cftp_admin') : __('Submit', 'cftp_admin');
        $disable_user = false;
        $require_pass = true;
        $form_action = 'register.php';
        $info_box = true;
        $extra_fields = false;
        $first_name_placeholder = __("Your first name", 'cftp_admin');
        $last_name_placeholder = __("Your last name", 'cftp_admin');
        $group_field = false;
        if ($clients_can_select_group == 'public' || $clients_can_select_group == 'all') {
            $group_field = true;
            $group_label = __('Request access to groups', 'cftp_admin');
        }
        break;
        /** A client is editing their profile */
    case 'edit_client_self':
        $submit_value = __('Update account', 'cftp_admin');
        $disable_user = true;
        $require_pass = false;
        $form_action = 'clients-edit.php?id=' . $client_id;
        $info_box = false;
        $extra_fields = false;
        $group_field = false;
        if ($clients_can_select_group == 'public' || $clients_can_select_group == 'all') {
            $group_field = true;
            $group_label = __('Request access to groups', 'cftp_admin');
            $override_groups_list = (!empty($found_requests[$client_id]['group_ids'])) ? $found_requests[$client_id]['group_ids'] : null;
        }
        $ignore_size = true;
        break;
}
?>
<style>
        .card {
            min-height: 100%;
        }
        .card-header {
            min-height: 80px; /* Set uniform height for card headers */
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .badge-custom {
        position: absolute;
        top: 6px;
        right: 10px;
        font-size: 12px;
        padding: 5px 6px;
        border-radius: 8px;
        }
        .payment-container {
            width: 100%;
            max-width: 400px;
            margin: auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.1);
            font-family: "Arial", sans-serif;
        }

        #card-element {
            padding: 12px;
            border-radius: 6px;
            background: #fff;
            box-shadow: 0px 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #d1d5db;
        }

        #card-errors {
            color: red;
            margin-top: 10px;
            font-size: 14px;
        }
</style>
<form action="<?php echo html_output($form_action); ?>" name="client_form" id="client_form" method="post" class="form-horizontal" data-form-type="<?php echo $clients_form_type; ?>">
    <?php addCsrf(); ?>
    <div class="container my-5">
    <h1 class="text-center mb-4">Choose Your Plan</h1>
<div class="row text-center">
    <!-- First Plan -->
    <div class="col-md-6">
        <div class="card h-100 shadow-sm d-flex flex-column position-relative">
            <div class="card-header bg-primary text-white position-relative">
                <h3 class="m-0">Monthly Plan</h3>
                <span class="badge bg-danger badge-custom">Most Popular</span>
            </div>
            <div class="card-body flex-grow-1">
                <h4 class="card-title">30-Day Free Trial</h4>
                <p>Then $4.99/month</p>
                <ul class="list-unstyled mt-3 mb-4">
                    <li>Access to basic features</li>
                    <li>Email support</li>
                    <li>Cancel anytime</li>
                </ul>
            </div>
            <div class="card-footer mt-auto bg-primary">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="plan" id="monthly" value="monthly" required <?php if(!empty($client_arguments['plan']=="monthly")){ echo "checked";}?>>
                    <label class="form-check-label text-light" for="monthly">
                        Monthly Plan
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Second Plan -->
    <div class="col-md-6">
        <div class="card h-100 shadow-sm d-flex flex-column position-relative">
            <div class="card-header bg-primary text-white position-relative">
                <h3 class="m-0">Annual Plan</h3>
                <span class="badge bg-success badge-custom">33.33% Less</span>
            </div>
            <div class="card-body flex-grow-1">
                <h4 class="card-title">30-Day Free Trial</h4>
                <p>Then $39.99/year</p>
                <ul class="list-unstyled mt-3 mb-4">
                    <li>Access to all features</li>
                    <li>Premium support</li>
                    <li>Save 33.33%</li>
                </ul>
            </div>
            <div class="card-footer mt-auto bg-primary">
                <div class="form-check">
                    <input class="form-check-input" type="radio" name="plan" id="annual" value="annual" required <?php if(!empty($client_arguments['plan']=="annual")){ echo "checked";}?>>
                    <label class="form-check-label text-light" for="annual">
                        Annual Plan
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>

</div>

<?php
// Assuming $client_arguments['name'] contains the full name (e.g., "Tom Riddle")
$name_parts = isset($client_arguments['name']) ? explode(' ', $client_arguments['name']) : [];
$first_name = isset($name_parts[0]) ? $name_parts[0] : '';
$last_name = isset($name_parts[1]) ? $name_parts[1] : '';
?>
    <div class="form-group row">
        <label for="name" class="col-sm-4 control-label"><?php _e('First Name', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <input type="text" name="first_name" id="first_name" class="form-control required" value="<?php echo $first_name; ?>" placeholder="<?php echo $first_name_placeholder; ?>" required />
        </div>
    </div>
    <div class="form-group row">
        <label for="name" class="col-sm-4 control-label"><?php _e('Last Name', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <input type="text" name="last_name" id="last_name" class="form-control required" value="<?php echo $last_name; ?>" placeholder="<?php echo $last_name_placeholder; ?>" required />
        </div>
    </div>

    <div class="form-group row">
        <label for="username" class="col-sm-4 control-label"><?php _e('Log in username', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <input type="text" name="username" id="username" class="form-control <?php if (!$disable_user) { echo 'required'; } ?>" maxlength="<?php echo MAX_USER_CHARS; ?>" value="<?php echo (isset($client_arguments['username'])) ? format_form_value($client_arguments['username']) : ''; ?>" <?php if ($disable_user) { echo 'readonly'; } ?> placeholder="<?php _e("Must be alphanumeric", 'cftp_admin'); ?>" required />
        </div>
    </div>

    <div class="form-group row">
        <label for="password" class="col-sm-4 control-label"><?php _e('Password', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <div class="input-group">
                <input type="password" name="password" id="password" class="form-control attach_password_toggler <?php if ($require_pass) { echo 'required'; } ?>" maxlength="<?php echo MAX_PASS_CHARS; ?>" />
            </div>
            <button type="button" name="generate_password" id="generate_password" class="btn btn-light btn-sm btn_generate_password" data-ref="password" data-min="<?php echo MAX_GENERATE_PASS_CHARS; ?>" data-max="<?php echo MAX_GENERATE_PASS_CHARS; ?>"><?php _e('Generate', 'cftp_admin'); ?></button>
            <?php echo password_notes(); ?>
        </div>
    </div>

    <div class="form-group row">
        <label for="email" class="col-sm-4 control-label"><?php _e('E-mail', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <input type="email" name="email" id="email" class="form-control required" value="<?php echo (isset($client_arguments['email'])) ? format_form_value($client_arguments['email']) : ''; ?>" placeholder="<?php _e("Must be valid and unique", 'cftp_admin'); ?>" required />
        </div>
    </div>

    <div class="form-group row">
        <label for="address" class="col-sm-4 control-label"><?php _e('Address', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <input type="text" name="address" id="address" class="form-control required" value="<?php echo (isset($client_arguments['address'])) ? format_form_value($client_arguments['address']) : ''; ?>" />
        </div>
    </div>

    <div class="form-group row">
        <label for="phone" class="col-sm-4 control-label"><?php _e('Telephone', 'cftp_admin'); ?></label>
        <div class="col-sm-8">
            <input type="text" name="phone" id="phone" class="form-control required" value="<?php echo (isset($client_arguments['phone'])) ? format_form_value($client_arguments['phone']) : ''; ?>" />
        </div>
    </div>
    <?php  if ($clients_form_type == 'new_client_self') { ?>
    <div class="form-group row">
        <label for="card-element" class="col-sm-4 control-label">Credit Card</label>
        <div class="col-sm-8">
            <div id="card-element">
                <!-- Stripe Element will be inserted here -->
            </div>
            <div id="card-errors" role="alert"></div>
        </div>
    </div>
    <input type="hidden" name="stripeToken" id="stripeToken">
 <?php   }?>
<!-- Include Flatpickr -->
<script src="https://js.stripe.com/v3/"></script>
<link rel="stylesheet" href="https://code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>

<script>
$(document).ready(function () {
    const expiryDateInput = $("#expiry_date");
    let datepickerInitialized = false;
    let previousValue = expiryDateInput.val(); // Store the initial value before datepicker

    // Function to initialize the datepicker
    function initializeDatepicker() {
        expiryDateInput.datepicker({
            dateFormat: "MM/yy",   // Show month and year format (e.g., "January 2025")
            changeMonth: true,     // Allow month selection
            changeYear: true,      // Allow year selection
            showButtonPanel: true,
            defaultDate: null, // Show button panel for easy closing
            onClose: function (dateText, inst) {
                const selectedMonth = $("#ui-datepicker-div .ui-datepicker-month :selected").val();
                const selectedYear = $("#ui-datepicker-div .ui-datepicker-year :selected").val();
                const selectedDate = new Date(selectedYear, selectedMonth, 1);
                $(this).val($.datepicker.formatDate("MM yy", selectedDate)); // Set the value back in MM/YY format
            },
            beforeShow: function (input, inst) {
                inst.dpDiv.addClass("month_year_picker");  // Apply custom styling class
            }
        });
        datepickerInitialized = true;
    }

    // When the input field is clicked, initialize the datepicker if not already initialized
    expiryDateInput.on('focus', function () {
        if (!datepickerInitialized) {
            initializeDatepicker();  // Initialize only the first time it gains focus
        }
        // Save the current value when the datepicker is opened, so we can revert it if no selection is made
        previousValue = $(this).val();
        // In case the field loses focus and is clicked again, ensure datepicker is still active
        $(this).datepicker('show');
    });

    // When the input field loses focus (blur), revert to previous value if no date was selected
    expiryDateInput.on('blur', function () {
        const value = expiryDateInput.val();

        // If the value is not selected (empty or invalid), revert back to previous value
        if (!value || value === previousValue) {
            expiryDateInput.val(previousValue);  // Revert to the original value
        } else {
            // If a date was selected, format it as MM/YY
            const date = $.datepicker.parseDate('MM/yy', value);
            if (date) {
                expiryDateInput.val($.datepicker.formatDate('MM/yy', date)); // Set the value back in MM/YY format
            }
        }
    });
    var stripe = Stripe('pk_test_51QkX2yKgzD75154hUn0E2KdV8LJIBaYswMiDxL7qFsKWztlh1jIhrwPRmr0fq3tIF2Yqj3pvMLS2dxBs7V6my2zs00hcNGn5Lr');  // Use your Stripe public key
    var elements = stripe.elements();
    const style = {
    base: {
        color: "#32325d",
        fontSize: "16px",
        fontFamily: "Arial, sans-serif",
        '::placeholder': { color: "#aab7c4" }
    },
    invalid: { color: "#fa755a" }
};
    const card = elements.create('card',{style});
    if (card) {

    card.mount('#card-element');  // Use the correct element id
    document.getElementById('client_form').addEventListener('submit', function(event) {
        event.preventDefault();

        // Create a token using the card information
        stripe.createToken(card).then(function(result) {
            if (result.error) {
                // Handle error (e.g., show error to the user)
                document.getElementById('card-errors').textContent = result.error.message;
            } else {
                // Place the token in the hidden input field
                document.getElementById('stripeToken').value = result.token.id;

                // Submit the form after the token has been added
                document.getElementById('client_form').submit();
            }
        });
    });
    } else {
        // If Stripe element doesn't exist, submit form normally
        document.getElementById('client_form').addEventListener('submit', function(event) {
            this.submit(); // Submit the form without Stripe processing
        });
    }
});


</script>
    <?php
    if ($extra_fields == true) {
    ?>
        <div class="form-group row">
            <label for="contact" class="col-sm-4 control-label"><?php _e('Internal contact name', 'cftp_admin'); ?></label>
            <div class="col-sm-8">
                <input type="text" name="contact" id="contact" class="form-control" value="<?php echo (isset($client_arguments['contact'])) ? format_form_value($client_arguments['contact']) : ''; ?>" />
            </div>
        </div>

        <div class="form-group row">
            <label for="max_file_size" class="col-sm-4 control-label"><?php _e('Max. upload filesize', 'cftp_admin'); ?></label>
            <div class="col-sm-8">
                <div class="input-group">
                    <input type="text" name="max_file_size" id="max_file_size" class="form-control" value="<?php echo (isset($client_arguments['max_file_size'])) ? format_form_value($client_arguments['max_file_size']) : '0'; ?>" />
                    <span class="input-group-text">MB</span>
                </div>
                <p class="field_note form-text"><?php _e("Set to 0 to use the default system limit", 'cftp_admin'); ?> (<?php echo MAX_FILESIZE; ?> MB)</p>
            </div>
        </div>
        <?php
    }

    if ($group_field == true) {
        /**
         * Make a list of public groups in case clients can only request
         * membership to those
         */
        $arguments = [];

        /** Groups to search on based on the current user level */
        $role = (defined('CURRENT_USER_LEVEL')) ? CURRENT_USER_LEVEL : null;
        if (!empty($role) && in_array($role, [8, 9])) {
            /** An admin or client manager is creating a client account */
        } else {
            /** Someone is registering an account for himself */
            if ($clients_can_select_group == 'public') {
                $arguments['public'] = true;
            }
        }

        $sql_groups = get_groups($arguments);

        $selected_groups = (!empty($found_groups)) ? $found_groups : '';
        $my_current_groups = [];
        /** Dirty and awful quick test, mark as selected the current groups which have requests for a client that's editing their own account */
        if (isset($override_groups_list)) {
            $selected_groups = $override_groups_list;
            if (!empty($found_groups)) {
                foreach ($sql_groups as $array_key => $sql_group) {
                    if (in_array($sql_group['id'], $found_groups)) {
                        $my_current_groups[] = $sql_group;
                        unset($sql_groups[$array_key]);
                    }
                }
            }
        }

        if (count($sql_groups) > 0) {
        ?>
            <div class="form-group row assigns">
                <label for="groups_request" class="col-sm-4 control-label"><?php echo $group_label; ?></label>
                <div class="col-sm-8">
                    <select class="form-select select2 none" multiple="multiple" name="groups_request[]" id="groups-select" data-placeholder="<?php _e('Select one or more options. Type to search.', 'cftp_admin'); ?>">
                        <?php
                        foreach ($sql_groups as $group) {
                        ?>
                            <option value="<?php echo $group['id']; ?>" <?php if (!empty($selected_groups) && in_array($group['id'], $selected_groups)) { echo ' selected="selected"'; } ?>><?php echo $group['name']; ?></option>
                        <?php
                        }
                        ?>
                    </select>
                    <?php
                    if (!empty($role) && in_array($role, [8, 9])) {
                    ?>
                        <div class="select_control_buttons">
                            <button type="button" class="btn btn-pslight add-all" data-target="groups-select"><?php _e('Add all', 'cftp_admin'); ?></button>
                            <button type="button" class="btn btn-pslight remove-all" data-target="groups-select"><?php _e('Remove all', 'cftp_admin'); ?></button>
                        </div>
                    <?php
                    }
                    ?>
                </div>
            </div>
    <?php
        }
    }

    if ($extra_fields == true) {
    ?>
        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
                <label for="active">
                    <input type="checkbox" name="active" id="active" <?php echo (isset($client_arguments['active']) && $client_arguments['active'] == 1) ? 'checked="checked"' : ''; ?>> <?php _e('Active (client can log in)', 'cftp_admin'); ?>
                </label>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
                <label for="can_upload_public">
                    <input type="checkbox" name="can_upload_public" id="can_upload_public" <?php echo (isset($client_arguments['can_upload_public']) && $client_arguments['can_upload_public'] == 1) ? 'checked="checked"' : ''; ?>> <?php _e('Can set own files as public', 'cftp_admin'); ?>
                </label>
                <?php if (get_option('clients_can_set_public') != 'allowed') { ?>
                    <p class="field_note form-text"><?php _e("This has no effect according to your current settings.", 'cftp_admin'); ?> <a href="options.php?section=clients" target="blank"><?php _e("Go to settings", 'cftp_admin'); ?></a></p>
                <?php } ?>
            </div>
        </div>
    <?php
    }
    ?>

    <div class="form-group row">
        <div class="col-sm-8 offset-sm-4">
            <label for="notify_upload">
                <input type="checkbox" name="notify_upload" id="notify_upload" <?php echo (isset($client_arguments['notify_upload']) && $client_arguments['notify_upload'] == 1) ? 'checked="checked"' : ''; ?>> <?php _e('Notify new uploads by e-mail', 'cftp_admin'); ?>
            </label>
        </div>
    </div>

    <?php
    if ($clients_form_type == 'new_client') {
    ?>
        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
                <label for="notify_account">
                    <input type="checkbox" name="notify_account" id="notify_account" <?php echo (isset($client_arguments['notify_account']) && $client_arguments['notify_account'] == 1) ? 'checked="checked"' : ''; ?>> <?php _e('Send welcome email', 'cftp_admin'); ?>
                </label>
            </div>
        </div>

        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
                <label for="require_password_change">
                    <input type="checkbox" name="require_password_change" id="require_password_change" <?php echo (isset($client_arguments['require_password_change']) && $client_arguments['require_password_change'] == 1) ? 'checked="checked"' : ''; ?>> <?php _e('Require password change after first login', 'cftp_admin'); ?>
                </label>
            </div>
        </div>
    <?php
    }
    ?>

    <?php
    if ($clients_form_type == 'new_client_self') {
        recaptcha2_render_widget();?>
        <div class="form-group row">
            <div class="col-sm-8 offset-sm-4">
            <input  type="checkbox" id="termsCheckbox"  name="terms&condition" required>
            <label class="form-check-label" for="termsCheckbox">
                I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
            </label>
            </div>
        </div>
    <div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms and Conditions</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Dummy Terms & Conditions:</strong></p>
                <p>1. You agree to comply with all rules and policies of the service.</p>
                <p>2. The subscription is non-refundable after the trial period.</p>
                <p>3. We reserve the right to modify these terms at any time.</p>
                <p>4. Your data will be protected according to our privacy policy.</p>
                <p>5. By continuing, you agree to these terms.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
        <?php
    }
    ?>
    
    <div class="inside_form_buttons">
        <button type="submit" class="btn btn-wide btn-primary"><?php echo html_output($submit_value); ?></button>
    </div>
    <script>
    document.getElementById('termsCheckbox').addEventListener('change', function () {
        if (this.checked) {
            var modal = new bootstrap.Modal(document.getElementById('termsModal'));
            modal.show();
        }
    });
</script>
    <?php
    if ($info_box == true) {
        $msg = __('This account information will be e-mailed to the address supplied above', 'cftp_admin');
        echo system_message('info', $msg);
    }
    ?>
</form>
