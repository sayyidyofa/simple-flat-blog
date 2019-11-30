<?php

function public_controller($action = null) {
    if (!isset($action)) {
        $posts = get_all_posts();
        echo get_kumis()->render('public.list', array(
            'title' => 'Blog Posts',
            'user_logout' => false,
            'posts' => $posts
        ));
    }
    elseif(isset($action) && $action === 'show' && isset($_GET['id'])) {
        $id = $_GET['id'];
        $post = get_single_post($id);
        echo get_kumis()->render('public.show', array(
            'title' => $post['title'],
            'post_title' => $post['title'],
            'created_at' => $post['timestamp'],
            'post_tags' => $post['tags'],
            'post_content' => $post['content'],
            'created_by' => $post['name']
        ));
    }
}