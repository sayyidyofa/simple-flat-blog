<?php

function render_login($error) {
    echo get_kumis()->render('user.login', array(
        'title' => 'LOGIN',
        'error' => isset($error) ? $error : null
    ));
    unset($_SESSION['error']);
}

function do_auth($uname, $passwd) {
    if (validate_login($uname, $passwd)) {
        $user = get_user_by_uname($uname);
        $_SESSION['user_id'] = $user['user_id'];
        if ($uname === 'admin') { // Check if the Administrator logs in.
            $_SESSION['admin'] = true;
            header('location: /admin');
        }
        else header('location: /user');
    }
    else {
        $_SESSION['error'] = 'Invalid credentials';
        header('location: /login');
    }
}

function do_logout() {
    unset($_SESSION['user_id']);
    session_destroy();
    header('location: /login');
}