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
                'user_posts' => $posts_by_user,
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
            if (isset($_POST['title']) && isset($_POST['tags']) && isset($_POST['content'])) {
                $_SESSION['reply'] = create_post($_SESSION['user_id'], $_POST['title'], $_POST['tags'], $_POST['content']);
                header('location: /user');
            }
            else echo get_kumis()->render('user.create_post', array(
                'title' => 'Create Post',
                'user_logout' => true,
                'user_sidebar' => true,
                'user_id' => $user_data['user_id'],
                'user_name' => $user_data['name'],
                'pp_path' => $user_data['photo_path']
            ));
        }
        elseif (isset($action) && $action === 'edit_post') {
            $post = get_single_post($_GET['post_id']);
            if (isset($_GET['post_id']) && isset($_POST['title']) && isset($_POST['tags']) && isset($_POST['content'])) {
                $_SESSION['reply'] = edit_post($_GET['post_id'], $_POST['title'], $_POST['tags'], $_POST['content']);
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
        elseif (isset($action) && $action === 'crud_tags') {
            $tags_data = get_all_tags(true);
            echo get_kumis()->render('user.crud_tag', array(
                'reply' => isset($_SESSION['reply']) ? $_SESSION['reply'] : null,
                'title' => 'Manage Tags',
                'user_logout' => true,
                'user_sidebar' => true,
                'user_id' => $user_data['user_id'],
                'user_name' => $user_data['name'],
                'pp_path' => $user_data['photo_path'],
                'post_count' => $user_data['post_count'],
                'tags_data' => $tags_data
            ));
            unset($_SESSION['reply']); // So that status only shows once
        }
        elseif (isset($action) && $action === 'create_tag' && isset($_POST['tag_name'])) {
            create_tag($_POST['tag_name']);
            header('location: /user/tags');
        }
        elseif (isset($action) && $action === 'edit_tag') {
            if (isset($_POST['tag_name']) && isset($_GET['tag_id'])) {
                edit_tag($_GET['tag_id'], $_POST['tag_name']);
                header('location: /user/tags');
            }
            else {
                echo get_kumis()->render('user.edit_tag', array(
                    'title' => 'Manage Tags',
                    'user_logout' => true,
                    'user_sidebar' => true,
                    'user_id' => $user_data['user_id'],
                    'user_name' => $user_data['name'],
                    'pp_path' => $user_data['photo_path'],
                    'tag_id' => $_GET['tag_id'],
                    'tag_name' => get_single_tag($_GET['tag_id'])['tag_name']
                ));
            }
        }
        elseif (isset($action) && $action === 'delete_tag' && isset($_GET['tag_id'])) {
            delete_tag($_GET['tag_id']);
            header('location: /user/tags');
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

function create_post($user_id, $title, $tags, $content) {
    $tag_arr = array_diff(tag_arr_gen($tags), get_all_tags()); // https://www.w3schools.com/php/func_array_diff.asp
    if ('success' === insert_tags_sync_post($tag_arr) && 'success' === insert_post($user_id, $title, $tags, $content)) return "success";
    else return "error";
}

function edit_post($id, $title, $tags, $content) {
    $tag_arr = array_diff(tag_arr_gen($tags), get_all_tags());
    $tag_str = tag_str_gen($tags);
    if ('success' === insert_tags_sync_post($tag_arr) && 'success' === update_post($id, $title, $tag_str, $content)) return "success";
    else return "error";
}

function delete_post($id) {
    return remove_post($id);
}

function create_tag($tag_name) {
    return insert_tags_sync_post(array($tag_name));
}

function edit_tag($id, $tag_name) {
    $target_tag = get_single_tag($id)['tag_name'];
    $fixed_tags_arr = recursive_replace(
        extract_index(get_all_posts(), "tags"),
        $target_tag,
        true,
        $tag_name);
    $ids_and_fixed_tags = id_tag_combine(
        extract_index(get_all_posts(), "post_id"),
        $fixed_tags_arr);
    error_log(print_r($ids_and_fixed_tags, true));

    foreach ($ids_and_fixed_tags as $key_value) {
        error_log(print_r($key_value['post_id']." : ".$key_value['tags'], true));
        update_post_sync_tags($key_value['post_id'], $key_value['tags']);
    }

}

function delete_tag($id) {
    $target_tag = get_single_tag($id)['tag_name'];
    $tags_arr = extract_index(get_all_posts(), "tags");
    $fixed_tags_arr = recursive_replace($tags_arr, $target_tag);
    $post_ids = extract_index(get_all_posts(), "post_id");
    $ids_and_fixed_tags = id_tag_combine($post_ids, $fixed_tags_arr);
    error_log(print_r($ids_and_fixed_tags, true));

    foreach ($ids_and_fixed_tags as $key_value) {
        error_log(print_r($key_value['post_id']." : ".$key_value['tags'], true));
        update_post_sync_tags($key_value['post_id'], $key_value['tags']);
    }
    return remove_tag($id);
}