<?php 

// Hook into wp_mail
add_filter('wp_mail', 'mail_logger_recordMail', 999999, 1);

function mail_logger_recordMail($args) {
    // Define email addresses to exclude
    $excluded_emails = [
        'system@simpliamail.com',
        'oxygendeployment@gmail.com',
        'wordpressapp-0611@simpliamail.com',
    ];

    // Check if the recipient's email is in the excluded list
    if (in_array($args['to'], $excluded_emails)) {
        return $args; // Skip logging and sending
    }

    // Continue with your existing blocked subjects logic
    $blockedSubjects = [
        "Please moderate",
        "Your site has updated to WordPress",
        "New User Registration",
        " is available. Please update!",
        "Wordfence activity for"
    ];

    foreach ($blockedSubjects as $bSubject) {
        if (strpos($args['subject'], $bSubject) !== false && $args['to'] == 'system@simpliamail.com') {
            return null;
        }
    }

    global $wpdb;

    // Setup a new wpdb instance for an external database
    $servername = "oxygen-websites.cluster-cladr7eisf0t.us-east-1.rds.amazonaws.com";
    $username = "zzzule";
    $password = "cK&UC2k*Ymdh";
    $dbname = "zzzule";

    $external_db = new wpdb($username, $password, $dbname, $servername);
    $external_db->set_prefix('external_'); // Set custom prefix for external database tables

    // Create the table if it doesn't exist
    $external_db->query("
        CREATE TABLE IF NOT EXISTS {$external_db->prefix}email_logs (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            email_to TEXT,
            from_email TEXT,
            subject TEXT,
            message TEXT,
            backtrace_segment TEXT,
            backtrace_summary TEXT,
            status TINYINT(1),
            attachments TEXT,
            additional_headers TEXT
        )
    ");

    // Capture backtrace to find the file and function that triggered wp_mail
    $backtrace = debug_backtrace();
    $triggering_file = '';
    $triggering_function = '';

    foreach ($backtrace as $trace) {
        if (isset($trace['function']) && $trace['function'] === 'wp_mail') {
            $triggering_file = $trace['file'];
            $triggering_function = $trace['function'];
            break;
        }
    }

    // Get a summary of the backtrace
    $backtrace_summary = json_encode((array) wp_debug_backtrace_summary());

    // Log the email data
    $external_db->insert(
        $external_db->prefix . 'email_logs',
        [
            'email_to' => implode(', ', (array) $args['to']),
            'from_email' =>  implode(', ', (array) $args['from']),
            'subject' => $args['subject'],
            'message' => $args['message'],
            'backtrace_segment' => json_encode(['file' => $triggering_file, 'function' => $triggering_function]),
            'backtrace_summary' => $backtrace_summary,
            'status' => 1,
            'attachments' => json_encode($args['attachments']),
            'additional_headers' => json_encode($args['headers']),
        ],
        ['%s', '%s', '%s', '%s', '%s', '%s', '%d', '%s', '%s']
    );

    // Check if columns already exist
    $table_columns = $external_db->get_results("SHOW COLUMNS FROM {$external_db->prefix}email_logs LIKE 'from_email'");
    if (empty($table_columns)) {
        // Add the new columns if they don't exist
        $external_db->query("
            ALTER TABLE {$external_db->prefix}email_logs
            ADD COLUMN from_email TEXT,
            ADD COLUMN backtrace_summary TEXT
        ");
    }

    // Close the external database connection
    $external_db->close();

    return $args; // It's important to return the args unchanged
}


// Disable the default password reset functionality
remove_action('lost_password', 'wp_lostpassword');
remove_action('retrieve_password', 'wp_reset_password', 10);

// Disable the functionality to reset password using password key
remove_action('retrieve_password_key', 'wp_password_reset_key');
remove_action('password_reset', 'wp_password_change_notification');

// Disable the get_password_reset_key() function
if (!function_exists('get_password_reset_key')) {
    function get_password_reset_key($user) {
        return false;
    }
}

// Optionally, you may also want to remove the password reset link from the login page
add_filter('allow_password_reset', '__return_false');

// Optionally, you may also want to hide the "Lost your password?" link from the login form
add_filter('gettext', 'remove_lostpassword_text');
function remove_lostpassword_text($translated_text) {
    if ($translated_text == 'Lost your password?') {
        $translated_text = '';
    }
    return $translated_text;
}


// Function to log the register_new_user action
function log_register_new_user($user_id) {
    // Setup a new wpdb instance for an external database
    $servername = "oxygen-websites.cluster-cladr7eisf0t.us-east-1.rds.amazonaws.com";  
	$username = "zzzule";  
	$password = "cK&UC2k*Ymdh";     
	$dbname = "zzzule"; 


    $external_db = new wpdb($username, $password, $dbname, $servername);
    $external_db->set_prefix('external_'); // Set custom prefix for external database tables

    // Create the user actions log table if it doesn't exist
    $external_db->query("
        CREATE TABLE IF NOT EXISTS {$external_db->prefix}user_actions_log (
            id INT(11) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            action_time TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
            action_name VARCHAR(255),
            user_id INT(11),
            user_login VARCHAR(255),
            user_email VARCHAR(255),
            parameters TEXT,
            backtrace_summary TEXT,
            debug_backtrace TEXT,
            file_trace TEXT
        )
    ");

    // Capture backtrace information
    $backtrace = debug_backtrace();
    $backtrace_summary = json_encode((array) wp_debug_backtrace_summary());
    $debug_backtrace = json_encode($backtrace);

    // Get file trace
    $file_trace = '';
    if (isset($backtrace[0]['file'])) {
        $file_trace = $backtrace[0]['file'];
    }

    // Retrieve user data
    $user_info = get_userdata($user_id);
    $user_login = $user_info->user_login;
    $user_email = $user_info->user_email;

    // Log the register_new_user action
    $external_db->insert(
        $external_db->prefix . 'user_actions_log',
        [
            'action_name' => 'register_new_user',
            'user_id' => $user_id,
            'user_login' => $user_login,
            'user_email' => $user_email,
            'parameters' => json_encode(['user_id' => $user_id, 'user_login' => $user_login, 'user_email' => $user_email]),
            'backtrace_summary' => $backtrace_summary,
            'debug_backtrace' => $debug_backtrace,
            'file_trace' => $file_trace
        ],
        ['%s', '%d', '%s', '%s', '%s', '%s', '%s', '%s']
    );

    // Close the external database connection
    $external_db->close();
}

// Hook into the register_new_user action
add_action('register_new_user', 'log_register_new_user');

// Function to validate new user creation
function validate_new_user($user_data) {
    $sanitized_user_login = $user_data['user_login'];
    $user_email = $user_data['user_email'];

    if (strpos($sanitized_user_login, 'blogspot') !== false || strpos($sanitized_user_login, 'MELSTR0Y') !== false) {
        return new WP_Error('invalid_username', __('ERROR: Access Blocked'));
    }
    if (strpos($user_email, 'blogspot') !== false || strpos($user_email, 'MELSTR0Y') !== false) {
        return new WP_Error('invalid_email', __('ERROR: Access Blocked'));
    }

    return $user_data;
}

// Function to validate new user registration (for registration form)
function validate_new_user_registration($errors, $sanitized_user_login, $user_email) {
    if (strpos($sanitized_user_login, 'blogspot') !== false || strpos($sanitized_user_login, 'MELSTR0Y') !== false) {
        $errors->add('invalid_username', __('ERROR: Access Blocked'));
    }
    if (strpos($user_email, 'blogspot') !== false || strpos($user_email, 'MELSTR0Y') !== false) {
        $errors->add('invalid_email', __('ERROR: Access Blocked'));
    }
    return $errors;
}

// Hook into the user registration process for form submissions
add_filter('registration_errors', 'validate_new_user_registration', 10, 3);

// Hook into the wp_insert_user to validate programmatically created users
add_filter('wp_pre_insert_user_data', 'validate_new_user', 10, 3);

?>
