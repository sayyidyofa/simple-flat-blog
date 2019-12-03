<?php

function get_api_key() {
    return getenv('API_KEY');
}

function response_error($msg = null) {
    if (isset($msg))
        return json_encode(array('status'=>'error', 'err_msg'=>$msg));
    else
        return json_encode(array('status'=>'error'));
}

function response_success($msg = null) {
    if (isset($msg))
        return json_encode(array('status'=>'success', 'succ_msg'=>$msg));
    else
        return json_encode(array('status'=>'success'));
}