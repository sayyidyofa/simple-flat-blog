<?php

function user_controller($action = null) {
    if (isset($_SESSION['user_id'])) {
        $user_data = get_user_view($_SESSION['user_id']);
        $posts_by_user = get_user_posts($_SESSION['user_id']);

        if (!isset($action)) { // Main Home view renders here
            echo get_kumis()->render('user.home', array(
                'reply' => isset($_SESSION['reply']) ? $_SESSION['reply'] : null,
                'title' => 'User Home',
                'user_logout' => true,
                'user_sidebar' => true,
                'user_id' => $user_data['user_id'],
                'user_name' => $user_data['name'],
                'pp_path' => $user_data['photo_path'],
                'post_count' => $user_data['post_count'],
                'user_posts_view' => $user_data['post_count'] > 0 ? true : false,
                'user_posts' => $posts_by_user,
                'profile_form' => $user_data,
                'post_id' => isset($posts_by_user['post_id']) ? $posts_by_user['post_id'] : null
            ));
            unset($_SESSION['reply']); // So that status only shows once
        }
        elseif (isset($action) && $action === 'upload_pp') {
            $_SESSION['reply'] = upload_image($_FILES['profile_image'], $user_data['user_id']);
            if (strpos($_SESSION['reply'], "has been uploaded")) // TODO: return dari sync_pp_path() unused
                sync_pp_path("uploads/".str_replace(" has been uploaded", "", str_replace("The image ", "", $_SESSION['reply'])), $user_data['user_id']);
            // https://www.w3schools.com/php/func_string_str_replace.asp
            header('location: /user');
        }
        elseif (isset($action) && $action === 'create_post') {
            if (isset($_POST['title']) && isset($_POST['content'])) {
                $_SESSION['reply'] = create_post($_SESSION['user_id'], $_POST['title'], $_POST['content']);
                header('location: /user');
            }
            else echo get_kumis()->render('user.create_post', array(
                'title' => 'Create Post',
                'user_logout' => true,
                'user_sidebar' => true,
                'user_id' => $user_data['user_id'],
                'user_name' => $user_data['name'],
                'pp_path' => $user_data['photo_path'],
                'profile_form' => $user_data
            ));
        }
        elseif (isset($action) && $action === 'edit_post') {
            $post = get_single_post($_GET['post_id']);
            if (isset($_GET['post_id']) && isset($_POST['title']) && isset($_POST['content'])) {
                $_SESSION['reply'] = edit_post($_GET['post_id'], $_POST['title'], $_POST['content']);
                header('location: /user');
            }
            else echo get_kumis()->render('user.edit_post', array(
                'title' => 'Edit Post',
                'user_logout' => true,
                'user_sidebar' => true,
                'user_name' => $user_data['name'],
                'pp_path' => $user_data['photo_path'],
                'post_id' => $_GET['post_id'],
                'post_title' => $post['title'],
                'post_tags' => $post['tags'],
                'post_content' => $post['content']
            ));
        }
        elseif (isset($action) && $action === 'delete_post') {
            $_SESSION['reply'] = delete_post($_GET['post_id']);
            header('location: /user');
        }
        elseif (isset($action) && $action === 'edit_profile') {
            if (isset($_POST['name']) && isset($_POST['username']) && isset($_POST['password']))
                $_SESSION['reply'] = update_user($user_data['user_id'], $_POST['username'], $_POST['password'], $_POST['name']);
            else
                $_SESSION['reply'] = 'User data remains unchanged';
            header('location: /user');
        }
        elseif (isset($action) && $action === 'delete_profile') {
            $_SESSION['reply'] = remove_user($user_data['user_id']);
            header('location: /logout');
        }
        else { // If not triggered properly, fallback to /user
            header('location: /user');
        }
    }
    else {
        $_SESSION['error'] = 'Session expired';
        header('location: /login');
    }
}

function create_post($user_id, $title, $content) {
    if ('success' === insert_post($user_id, $title, $content)) return "success";
    else return "error";
}

function edit_post($id, $title, $content) {
    if ('success' === update_post($id, $title, $content)) return "success";
    else return "error";
}

function delete_post($id) {
    return remove_post($id);
}