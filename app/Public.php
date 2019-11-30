<?php
function render_posts_index() {
    $posts = get_all_posts();
    echo get_kumis()->render('public.list', array(
        'title' => 'Blog Posts',
        'user_logout' => false,
        'posts' => $posts
    ));
}

function render_single_post($id) {
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