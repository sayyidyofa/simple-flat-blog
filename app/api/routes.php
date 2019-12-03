<?php

require_once dirname(__FILE__) . '/controllers/auth.php';
require_once dirname(__FILE__) . '/controllers/admin.php';
require_once dirname(__FILE__) . '/controllers/misc.php';

function api_router($uri) {

    if ('/api/auth' === $uri || '/api/auth/' === $uri && isset($_POST['username']) && isset($_POST['password']))
        api_auth_controller('auth');

    elseif ('/api/admin/users' === $uri || '/admin/users/' === $uri)
        api_admin_controller('list_users');
    elseif ('/api/admin/create_user' === $uri || '/api/admin/create_user/' === $uri)
        api_admin_controller('create_user');
    elseif ('/api/admin/edit_user' === $uri || '/api/admin/edit_user/' === $uri && isset($_GET['user_id'])) // PHP is starting to get really weird
        api_admin_controller('edit_user');
    elseif ('/api/admin/delete_user' === $uri || '/api/admin/delete_user/' === $uri && isset($_GET['user_id']))
        api_admin_controller('delete_user');
    else echo response_error();

}