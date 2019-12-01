<?php

function api_admin_controller($action = null) {

    $api_key = getenv('API_KEY');

    if (isset($_POST['api_key']) && $_POST['api_key'] === $api_key) {
        $users_data = get_all_user_view();      // Removes admin
        unset($users_data[0]);                  // from the
        $users_data = array_values($users_data);// users list
        $user_count = count($users_data);

        if (!isset($action)) { // Main Home view renders here
            echo response_error();
        }
        elseif (isset($action) && $action === 'list_users') {
            echo json_encode(
                $users_data
            );
        }
        elseif (isset($action) && $action === 'create_user') {
            if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password'])) {
                $_SESSION['reply'] = api_create_user($_POST['username'], $_POST['password'], $_POST['name']);
                sync_pp_path(null, null, true);
                echo response_success();
            }
            else echo response_error();
        }
        elseif (isset($action) && $action === 'edit_user') {
            if (isset($_POST['user_id']) && isset($_POST['name']) && isset($_POST['username'])) { // Password is optional
                $_SESSION['reply'] = api_edit_user(
                    $_POST['user_id'], $_POST['username'], isset($_POST['password']) ? $_POST['password'] : null, $_POST['name']
                );
                echo response_success();
            }
            else echo response_error();
        }
        elseif (isset($action) && $action === 'delete_user' && isset($_POST['user_id'])) {
            $_SESSION['reply'] = api_delete_user($_POST['user_id']);
            if ($_SESSION['reply'] === 'success') echo response_success();
            else echo response_error();
        }
    }
    else {
        echo response_error();
    }
}

function response_error() {
    return json_encode(array('status'=>'error'));
}

function response_success() {
    return json_encode(array('status'=>'success'));
}

function api_create_user($username, $password, $name) {
    return insert_user($username, $password, $name);
}

function api_edit_user($user_id, $username, $password, $name) {
    return update_user($user_id, $username, $password, $name);
}

function api_delete_user($user_id) {
    return remove_user($user_id);
}