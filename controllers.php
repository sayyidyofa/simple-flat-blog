<?php

/*import app start*/
require_once dirname(__FILE__).'/vendor/autoload.php';
require_once dirname(__FILE__).'/app/Public.php';
require_once dirname(__FILE__).'/app/Auth.php';
require_once dirname(__FILE__).'/app/User.php';
require_once dirname(__FILE__).'/app/Admin.php';
require_once dirname(__FILE__).'/app/Misc.php';
/*import app stop*/


/*initialize template object start*/
Mustache_Autoloader::register();
$mustache = new Mustache_Engine(array(
    'loader' =>new Mustache_Loader_FilesystemLoader(dirname(__FILE__).'/views',
        array('extension' => '.mustache')),
    'logger' => new Mustache_Logger_StreamLogger('php://stdout')
));

function get_kumis() { // PHP variable scoping is bullshit
    global $mustache;
    return $mustache;
}
/*initialize template object stop*/


/*public functions start*/
function list_posts() {
    render_posts_index();
}

function show_post($id) {
    render_single_post($id);
}
/*public functions stop*/


/*auth functions start*/
function login_form($error) {
    render_login($error);
}

function authenticate_login($uname, $passwd) {
    do_auth($uname, $passwd);
}

function user_logout() {
    do_logout();
}
/*auth functions stop*/


/*user functions start*/
function user_home($action = null) {
    user_controller($action);
}
/*user functions stop*/


/*admin functions start*/
function admin_home($action = null) {
    admin_controller($action);
}
/*admin functions stop*/

function not_found_404($uri) {
    echo get_kumis()->render('public.404', array(
        'title' => '404 Not Found',
        'uri' => $uri
    ));
}