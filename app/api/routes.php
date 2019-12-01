<?php
function api_router($uri) {
    if ('/api/posts' === $uri || '/api/posts/' === $uri) {
        public_home();
    } elseif (('/api/post' === $uri || '/api/post/' === $uri) && isset($_GET['id']) && '' != $_GET['id']) {
        public_home('show');
    }

    /*elseif ('/api/login' === $uri ||'/api/login/' === $uri) {
        login_form(isset($_SESSION['error']) ? $_SESSION['error'] : null);
    }*/ elseif (('/api/auth/login' === $uri ||'/api/auth/login/' === $uri) && isset($_POST['username']) && isset($_POST['password'])) {
        authenticate_login($_POST['username'], $_POST['password']);
    } elseif ('/api/auth/logout' === $uri ||'/api/auth/logout/' === $uri) {
        user_logout();
    }

    elseif (strpos($uri, '/api/admin')) { // The '/admin' route has some children
        if ('/admin' === $uri || '/admin/' === $uri) admin_home(); // Somehow switch-case doesn't work, so here's if-else

        elseif ('/api/admin/create_user' === $uri || '/api/admin/create_user/' === $uri)
            admin_home('create_user');
        elseif ('/api/admin/edit_user' === $uri || '/api/admin/edit_user/' === $uri && isset($_GET['user_id'])) // PHP is starting to get really weird
            admin_home('edit_user');
        elseif ('/api/admin/delete_user' === $uri || '/api/admin/delete_user/' === $uri && isset($_GET['user_id']))
            admin_home('delete_user');

        else admin_home(); // If delete or update is not triggered properly, fallback to admin home
    }

    elseif (strpos($uri, '/api/user')) { // The '/user' route has some children
        if ('/user' === $uri || '/user/' === $uri) user_home(); // Somehow switch-case doesn't work, so here's if-else

        elseif ('/api/user/upload_pp' === $uri || '/api/user/upload_pp/' === $uri)
            user_home('upload_pp');
        elseif ('/api/user/edit_profile' === $uri || '/api/user/edit_profile/' === $uri)
            user_home('edit_profile');
        elseif ('/api/user/delete_profile' === $uri || '/api/user/delete_profile/' === $uri)
            user_home('delete_profile');

        elseif ('/api/user/create_post' === $uri || '/api/user/create_post/' === $uri)
            user_home('create_post');
        elseif ('/api/user/edit_post' === $uri || '/api/user/edit_post/' === $uri && isset($_GET['post_id'])) // PHP is starting to get really weird
            user_home('edit_post');
        elseif ('/api/user/delete_post' === $uri || '/api/user/delete_post/' === $uri && isset($_GET['post_id']))
            user_home('delete_post');

        elseif ('/api/user/tags' === $uri || '/api/user/tags/' === $uri)
            user_home('crud_tags');
        elseif ('/api/user/create_tag' === $uri || '/api/user/create_tag/' === $uri)
            user_home('create_tag');
        elseif ('/api/user/edit_tag' === $uri || '/api/user/edit_tag/' === $uri && isset($_GET['tag_id'])) // PHP is starting to get really weird
            user_home('edit_tag');
        elseif ('/api/user/delete_tag' === $uri || '/api/user/delete_tag/' === $uri && isset($_GET['tag_id']))
            user_home('delete_tag');

        else user_home(); // If delete or update is not triggered properly, fallback to user home
    }
}