<?php

function admin_controller($action = null) {
    if (isset($_SESSION['user_id']) && isset($_SESSION['admin'])) {
        $users_data = get_all_user_view();      // Removes admin
        unset($users_data[0]);                  // from the
        $users_data = array_values($users_data);// users list
        $user_count = count($users_data);

        $user_data = get_user_view($_SESSION['user_id']);

        if (!isset($action)) { // Main Home view renders here
            echo get_kumis()->render('admin.home', array(
                'reply' => isset($_SESSION['reply']) ? $_SESSION['reply'] : null,
                'user_logout' => true,
                'admin_sidebar' => true,
                'pp_path' => $user_data['photo_path'],
                'title' => 'Admin Home',
                'user_count' => $user_count,
                'users' => $users_data,
                'post_id' => isset($users_data['user_id']) ? $users_data['user_id'] : null
            ));
            unset($_SESSION['reply']); // So that status only shows once
        }
        elseif (isset($action) && $action === 'create_user') {
            if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password'])) {
                $_SESSION['reply'] = create_user($_POST['username'], $_POST['password'], $_POST['name']);
                //sync_pp_path("uploads/blank_profile.png", $_SESSION['last_id']);
                sync_pp_path(null, null, true);
                header('location: /admin');
            }
            else echo get_kumis()->render('admin.create_user', array(
                'title' => 'Create User',
                'user_logout' => true,
                'admin_sidebar' => true,
                'pp_path' => $user_data['photo_path'],
            ));
        }
        elseif (isset($action) && $action === 'edit_user') {
            $user = get_single_user($_GET['user_id']);

            if (isset($_GET['user_id']) && isset($_POST['name']) && isset($_POST['username'])) { // Password is optional
                $_SESSION['reply'] = edit_user(
                    $_GET['user_id'], $_POST['username'], isset($_POST['password']) ? $_POST['password'] : null, $_POST['name']
                );
                header('location: /admin');
            }
            else echo get_kumis()->render('admin.edit_user', array(
                'title' => 'Edit User',
                'user_logout' => true,
                'admin_sidebar' => true,
                'pp_path' => $user_data['photo_path'],
                'user_id' => $_GET['user_id'],
                'uname' => $user['name'],
                'username' => $user['username']
            ));
        }
        elseif (isset($action) && $action === 'delete_user') {
            $_SESSION['reply'] = delete_user($_GET['user_id']);
            header('location: /admin');
        }
    }
    else {
        $_SESSION['error'] = 'Session expired';
        header('location: /login');
    }
}

function create_user($username, $password, $name) {
    return insert_user($username, $password, $name);
}

function edit_user($user_id, $username, $password, $name) {
    return update_user($user_id, $username, $password, $name);
}

function delete_user($user_id) {
    return remove_user($user_id);
}