<?php

function api_auth_controller($action = null) {
    $api_key = get_api_key();
    if (!isset($action)) echo response_error();
    else {
        if (isset($action) && $action === 'auth' && isset($_POST['username']) && isset($_POST['password'])) {
            $user_cred = get_login($_POST['username'], $_POST['password']);
            if ($_POST['username'] === $user_cred['username'] && $_POST['password'] === $user_cred['password']) {
                //$_SESSION['user_id'] = $user_cred['user_id'];
                if ($user_cred['priority'] === 1 || $user_cred['priority'] === '1') // Check if the Administrator logs in.
                    echo response_success($api_key);
                else echo response_error('Not an admin');
            }
            else echo response_error('Invalid credentials');
        }
        else echo response_error();
    }
}