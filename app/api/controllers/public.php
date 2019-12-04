<?php
function api_public_controller($action = null) {
    $config = HTMLPurifier_Config::createDefault();
    $purifier = new HTMLPurifier($config);
    if (!isset($action)) echo response_error();
    else {
        if ($action === 'list') {
            echo json_encode(get_all_posts());
        }
        elseif ($action === 'show') echo json_encode(
            get_single_post($GLOBALS['post_id'])
        );
        else echo response_error();
    }
}