<?php

function id_tag_combine($ids, $tags) {
    $i0 = 0; $i1 = 0;
    $fixed = [];
    foreach ($ids as $id) {
        $fixed[$i0]['post_id'] = $id;
        $i0++;
    }
    foreach ($tags as $tag) {
        $fixed[$i1]['tags'] = strtolower($tag);
        $i1++;
    }
    return $fixed;
}

function change_key( $array, $old_key, $new_key ) { // https://stackoverflow.com/questions/240660/in-php-how-do-you-change-the-key-of-an-array-element

    if( ! array_key_exists( $old_key, $array ) )
        return $array;

    $keys = array_keys( $array );
    $keys[ array_search( $old_key, $keys ) ] = $new_key;

    return array_combine( $keys, $array );
}

function extract_index($array, $key) {
    $ret_arr = [];
    foreach ($array as $sub_arr) {
        $ret_arr[] = $sub_arr[$key];
    }
    return $ret_arr;
}

function recursive_replace($array, $target, $is_update = false, $updt_tag = null) { // For replacing tags
    $preset = $target;
    $sub_arr = [];
    foreach ($array as $list) {
        if (count(tag_arr_gen($list)) === 1) $preset = $target;
        if ($is_update === true && isset($updt_tag))
            $sub_arr[] = str_replace($preset, tag_str_gen($updt_tag), $list);
        else $sub_arr[] = str_replace($preset, "", $list);
    }
    for ($idx = 0; $idx < count($sub_arr); $idx++) {
        if (empty($sub_arr[$idx])) $sub_arr[$idx] = "Undefined";
    }
    $i1 = 0;
    $i2 = 0;
    $i3 = 0;
    $i4 = 0;
    foreach ($sub_arr as $trimmed_list) {
        $trimmed_arr = tag_arr_gen($trimmed_list);
        for ($idx = 0; $idx < count($trimmed_arr); $idx++) {
            if (0 === strlen($trimmed_arr[$idx])) {
                unset($trimmed_arr[$idx]);
                $trimmed_arr = array_values($trimmed_arr);
            }
        }
        $sub_arr[$i1] = $trimmed_list;
        $i1++;
    }
    foreach ($sub_arr as $tagstr) {
        $tagarr = tag_arr_gen($tagstr);
        foreach ($tagarr as $ttgg) {
            if(strlen($ttgg) === 0) {
                unset($tagarr[$i3]);
                $tagarr = array_values($tagarr);
            }
            $i3++;
        }
        $tagarr = implode(", ", $tagarr);
        $sub_arr[$i4] = $tagarr;
        $i4++;
    }
    return $sub_arr;
}

function refresh_tag_str($tag_str) { // Removes empty tag values from tags string
    $tag_array = tag_arr_gen($tag_str);
    return implode(", ", array_filter($tag_array));
}

function tag_arr_gen($tag_str) {
    $tag_array = explode(',', strtolower($tag_str));
    foreach ($tag_array as &$tag ) $tag = trim(preg_replace('/\s+/', ' ', $tag)); // magic : https://www.techfry.com/php-tutorial/how-to-remove-whitespace-characters-in-a-string-in-php-trim-function
    return $tag_array;
}

function tag_str_gen($tag_str) {
    $tag_array = explode(',', strtolower($tag_str));
    foreach ($tag_array as &$tag ) $tag = trim(preg_replace('/\s+/', ' ', $tag)); // magic : https://www.techfry.com/php-tutorial/how-to-remove-whitespace-characters-in-a-string-in-php-trim-function
    return implode(", ", $tag_array);
}

function upload_image($img, $user_id) { // $img = $_FILES['profile_image']
    if(!empty($img))
    {
        $file_info = getimagesize($img['tmp_name']); // https://stackoverflow.com/questions/9314164/php-uploading-files-image-only-checking

        if ($file_info === FALSE)
            $_SESSION['pp_upload_status'] = "Unable to determine image type of uploaded file";
        elseif ($file_info[0] > 256 || $file_info[1] > 256 )
            $_SESSION['pp_upload_status'] = "Image is bigger than 256px square";
        elseif (($file_info["mime"] !== "image/gif")
            && ($file_info["mime"] !== "image/jpeg")
            && ($file_info["mime"] !== "image/png")) {
            $_SESSION['pp_upload_status'] = "Not a gif/jpeg/png";
        }
        else { // https://gist.github.com/taterbase/2688850
            $path = "uploads/";
            $img_file_name ="profile_user_".$user_id.".".pathinfo($img['name'])["extension"]; // https://stackoverflow.com/questions/173868/how-do-i-get-extract-a-file-extension-in-php
            $path = $path . basename($img_file_name); // https://www.php.net/manual/en/function.basename.php
            if(move_uploaded_file($img['tmp_name'], $path)) {
                $_SESSION['pp_upload_status'] = "The image ".$img_file_name." has been uploaded";
            } else{
                $_SESSION['pp_upload_status'] = "There was an error uploading the file, please try again!";
                die();
            }
        }
    }
    else $_SESSION['pp_upload_status'] = "Error: form is empty";
    $status = $_SESSION['pp_upload_status'];
    unset($_SESSION['pp_upload_status']);
    return $status;
}
