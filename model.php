<?php
require_once dirname(__FILE__).'/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::create(__DIR__);
$dotenv->load();

function open_database_connection() {
    /*var_dump("mysql:host=".getenv('MYSQL_HOST').";dbname=".getenv('MYSQL_SCHEMA')."");
    die();*/
    return new PDO("mysql:host=".getenv('MYSQL_HOST').";dbname=".getenv('MYSQL_SCHEMA')."",
        getenv('MYSQL_USER'),
        getenv('MYSQL_PASS')
    );
}

function close_database_connection(&$connection) {
    $connection = null;
}

function get_all_posts() {
    $connection = open_database_connection();

    $result = $connection->query('SELECT * from post_view');

    $posts = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $posts[] = $row;
    }
    close_database_connection($connection);

    return $posts;
}

function get_single_post($id) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM post_view WHERE post_id=:id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    close_database_connection($connection);

    return $row;
}

function validate_login($username, $password) {
    $connection = open_database_connection();

    $query = 'SELECT password FROM user WHERE username=:username';
    $statement = $connection->prepare($query);
    $statement->bindValue(':username', $username, PDO::PARAM_STR);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    close_database_connection($connection);

    return password_verify($password, $row["password"]);
}

function get_user_by_uname($uname) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM user WHERE user.username=:username';
    $statement = $connection->prepare($query);
    $statement->bindValue(':username', $uname, PDO::PARAM_STR);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    close_database_connection($connection);

    return $row;
}

function get_all_user_view() {
    $connection = open_database_connection();

    $result = $connection->query('SELECT * FROM user_view');

    $user_views = [];
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $user_views[] = $row;
    }
    close_database_connection($connection);

    return $user_views;
}

function get_user_view($user_id) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM user_view where user_id=:user_id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    close_database_connection($connection);

    return $row;
}

function get_user_posts($user_id) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM post_view where user_id=:user_id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $statement->execute();

    $user_posts = [];
    while ($row = $statement->fetch(PDO::FETCH_ASSOC)) $user_posts[] = $row;

    close_database_connection($connection);

    return $user_posts;
}

function insert_post($user_id, $title, $content) {
    $connection = open_database_connection();

    $query = 'INSERT INTO posts (user_id, title, content) VALUES (:user_id, :title, :content)';
    $statement = $connection->prepare($query);

    $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindValue(':title', $title, PDO::PARAM_STR);
    $statement->bindValue(':content', $content, PDO::PARAM_STR);

    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function update_post($id, $title, $content) {
    $connection = open_database_connection();

    $query = 'UPDATE posts SET title=:title, timestamp=current_timestamp(), content=:content WHERE posts.post_id=:id';
    $statement = $connection->prepare($query);

    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->bindValue(':title', $title, PDO::PARAM_STR);
    $statement->bindValue(':content', $content, PDO::PARAM_STR);

    error_log(print_r($query, true));
    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function remove_post($id) {
    $connection = open_database_connection();

    $query = 'DELETE FROM posts WHERE posts.post_id=:id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function sync_pp_path($pp_path, $user_id, $is_insert = false) {
    $connection = open_database_connection();
    if ($is_insert === true) {
        $query2 = "INSERT INTO profile_photo (photo_id, user_id, photo_path) VALUES (null, :id, :pp_path)";
        $stmt = $connection->prepare($query2);
        $stmt->bindValue(':id', $_SESSION['last_id'], PDO::PARAM_INT);
        $stmt->bindValue(':pp_path', 'uploads/blank_profile.png', PDO::PARAM_STR);
        if ($stmt->execute()) $status = "success";
        else $status = implode(" ", $stmt->errorInfo());
        close_database_connection($connection);
        return $status;
    }
    else {
        $query = 'UPDATE profile_photo SET photo_path=:pp_path WHERE profile_photo.user_id=:id';
        $statement = $connection->prepare($query);
        $statement->bindValue(':pp_path', $pp_path, PDO::PARAM_STR);
        $statement->bindValue(':id', $user_id, PDO::PARAM_INT);
        if ($statement->execute()) $status = "success";
        else $status = implode(" ", $statement->errorInfo());
        close_database_connection($connection);
        return $status;
    }
}

function get_single_user($id) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM user WHERE user.user_id=:id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    close_database_connection($connection);

    return $row;
}

function insert_user($username, $password, $name) {
    $connection = open_database_connection();

    $query = 'INSERT INTO user (username, password, name) VALUES (:username, :passwd, :uname)';
    $statement = $connection->prepare($query);

    if ($statement->execute(array(
        ':username' => $username,
        ':passwd' => password_hash($password, PASSWORD_BCRYPT),
        ':uname' => $name
    ))) $status = "success";
    else $status = $statement->errorCode();
    $_SESSION['last_id'] = $connection->lastInsertId();

    close_database_connection($connection);

    return $status;
}

function update_user($id, $username, $password, $name) {
    $connection = open_database_connection();

    if (null !== $password && "" !== $password && isset($password)) {
        $query = 'UPDATE user SET username=:username, password=:passwd, name=:uname WHERE user.user_id=:id';
        $statement = $connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':username', $username, PDO::PARAM_STR);
        $statement->bindValue(':passwd', password_hash($password, PASSWORD_BCRYPT), PDO::PARAM_STR);
        $statement->bindValue(':uname', $name, PDO::PARAM_STR);
    }
    else {
        $query = 'UPDATE user SET username=:username, name=:uname WHERE user.user_id=:id';
        $statement = $connection->prepare($query);
        $statement->bindValue(':id', $id, PDO::PARAM_INT);
        $statement->bindValue(':username', $username, PDO::PARAM_STR);
        $statement->bindValue(':uname', $name, PDO::PARAM_STR);
    }

    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function remove_user($id) {
    $connection = open_database_connection();

    $query = 'DELETE FROM user WHERE user.user_id=:id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}