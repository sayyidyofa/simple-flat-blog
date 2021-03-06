<?php
// The front controller, or router
require_once 'model.php';
require_once 'controllers.php';
require_once 'app/api/routes.php';
session_start();

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (strpos($uri, 'api')) { // API handler
    api_router($uri);
}

elseif (strpos($uri, 'env')) {
    not_found_404($uri);
}

elseif ('/' === $uri || '' === $uri) {
    public_home();
} elseif (('/show' === $uri || '/show/' === $uri) && isset($_GET['id']) && '' != $_GET['id']) {
    public_home('show');
}

elseif ('/login' === $uri ||'/login/' === $uri) {
    login_form(isset($_SESSION['error']) ? $_SESSION['error'] : null);
} elseif (('/login_post' === $uri ||'/login_post/' === $uri) && isset($_POST['username']) && isset($_POST['password'])) {
    authenticate_login($_POST['username'], $_POST['password']);
} elseif ('/logout' === $uri ||'/logout/' === $uri) {
    user_logout();
}

elseif (strpos($uri, 'admin')) { // The '/admin' route has some children
    if ('/admin' === $uri || '/admin/' === $uri) admin_home(); // Somehow switch-case doesn't work, so here's if-else

    elseif ('/admin/create_user' === $uri || '/admin/create_user/' === $uri)
        admin_home('create_user');
    elseif ('/admin/edit_user' === $uri || '/admin/edit_user/' === $uri && isset($_GET['user_id'])) // PHP is starting to get really weird
        admin_home('edit_user');
    elseif ('/admin/delete_user' === $uri || '/admin/delete_user/' === $uri && isset($_GET['user_id']))
        admin_home('delete_user');

    else admin_home(); // If delete or update is not triggered properly, fallback to admin home
}

elseif (strpos($uri, 'user')) { // The '/user' route has some children
    if ('/user' === $uri || '/user/' === $uri) user_home(); // Somehow switch-case doesn't work, so here's if-else

    elseif ('/user/upload_pp' === $uri || '/user/upload_pp/' === $uri)
        user_home('upload_pp');
    elseif ('/user/edit_profile' === $uri || '/user/edit_profile/' === $uri)
        user_home('edit_profile');
    elseif ('/user/delete_profile' === $uri || '/user/delete_profile/' === $uri)
        user_home('delete_profile');

    elseif ('/user/create_post' === $uri || '/user/create_post/' === $uri)
        user_home('create_post');
    elseif ('/user/edit_post' === $uri || '/user/edit_post/' === $uri && isset($_GET['post_id'])) // PHP is starting to get really weird
        user_home('edit_post');
    elseif ('/user/delete_post' === $uri || '/user/delete_post/' === $uri && isset($_GET['post_id']))
        user_home('delete_post');

    else user_home(); // If delete or update is not triggered properly, fallback to user home
}

else {
    not_found_404($uri);
}
