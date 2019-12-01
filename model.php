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

function get_login($username, $password) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM user WHERE username=:username AND password=:password';
    $statement = $connection->prepare($query);
    $statement->bindValue(':username', $username, PDO::PARAM_STR);
    $statement->bindValue(':password', $password, PDO::PARAM_STR);
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

function insert_post($user_id, $title, $tags, $content) {
    $connection = open_database_connection();

    $query = 'INSERT INTO posts (user_id, title, tags, content) VALUES (:user_id, :title, :tags, :content)';
    $statement = $connection->prepare($query);

    $statement->bindValue(':user_id', $user_id, PDO::PARAM_INT);
    $statement->bindValue(':title', $title, PDO::PARAM_STR);
    $statement->bindValue(':tags', $tags, PDO::PARAM_STR);
    $statement->bindValue(':content', $content, PDO::PARAM_STR);

    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function update_post($id, $title, $tags, $content) {
    $connection = open_database_connection();

    $query = 'UPDATE posts SET title=:title, tags=:tags, timestamp=current_timestamp(), content=:content WHERE posts.post_id=:id';
    $statement = $connection->prepare($query);

    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->bindValue(':title', $title, PDO::PARAM_STR);
    $statement->bindValue(':tags', $tags, PDO::PARAM_STR);
    $statement->bindValue(':content', $content, PDO::PARAM_STR);

    error_log(print_r($query, true));
    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function update_post_sync_tags($id, $tags) {
    $connection = open_database_connection();

    $query = 'UPDATE posts SET tags=:tags, timestamp=current_timestamp() WHERE posts.post_id=:id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->bindValue(':tags', $tags, PDO::PARAM_STR);
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

function get_all_tags($fetch_all = false) {
    $connection = open_database_connection();

    $result = $connection->query('SELECT * from tags');

    $tags_arr = [];
    if ($fetch_all === true) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $tags_arr[] = $row;
        }
    }
    else {
        while ($row = $result->fetch(PDO::FETCH_NUM)) {
            $tags_arr[] = $row[1];
        }
    }

    close_database_connection($connection);
    return $tags_arr;
}

function get_single_tag($id) {
    $connection = open_database_connection();

    $query = 'SELECT * FROM tags WHERE tag_id=:id';
    $statement = $connection->prepare($query);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);
    $statement->execute();

    $row = $statement->fetch(PDO::FETCH_ASSOC);

    close_database_connection($connection);

    return $row;
}

function insert_tag($tag_name) {
    $connection = open_database_connection();

    $query = 'INSERT INTO tags (tag_name) VALUES (:tag_name)';
    $statement = $connection->prepare($query);

    if ($statement->execute(array(
        ':tag_name' => $tag_name
    ))) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function insert_tags_sync_post($tag_array) {
    if (count($tag_array) === 0) return 'success';

    $connection = open_database_connection();

    $query = 'INSERT INTO tags (tag_name) VALUES (:tag_name)';
    $statement = $connection->prepare($query);

    foreach ($tag_array as $tag_name) {
        try {
            $statement->execute(array(':tag_name' => $tag_name));
            $_SESSION['query_status'] = "success";
        }
        catch (PDOException $e) {
            $_SESSION['query_status'] = implode(" ", $statement->errorInfo());
        }
    }

    close_database_connection($connection);

    $status = $_SESSION['query_status'];
    unset($_SESSION['query_status']);
    return $status;
}

function update_tag($id, $tag_name) {
    $connection = open_database_connection();

    $query = 'UPDATE tags SET tag_name=:tag_name WHERE tag_id=:id';

    $statement = $connection->prepare($query);

    $statement->bindValue(':tag_name', $tag_name, PDO::PARAM_STR);
    $statement->bindValue(':id', $id, PDO::PARAM_INT);

    if ($statement->execute()) $status = "success";
    else $status = implode(" ", $statement->errorInfo());

    close_database_connection($connection);

    return $status;
}

function remove_tag($id) {
    $connection = open_database_connection();

    $query = 'DELETE FROM tags WHERE tags.tag_id=:id';
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
        ':passwd' => $password,
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
        $statement->bindValue(':passwd', $password, PDO::PARAM_STR);
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