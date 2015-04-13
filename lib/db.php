<?php

include 'medoo.php';
global $database;

$database = new medoo([
    'database_type' => 'mysql',
    'database_name' => '',
    'server' => 'localhost',
    'username' => 'root',
    'password' => '',
        ]);

function getUser($key = null) {
    $user = fromSession("user");
    if (isset($user)) {
        if ($key == null) {
            return $user;
        } else {
            if (isset($user[$key])) {
                return $user[$key];
            } else {
                return null;
            }
        }
    } else {
        return null;
    }
}

function getInit($id) {
    $init = [
        "localhost" => "localhost:8000",
        "dir" => $_SERVER['DOCUMENT_ROOT'],
    ];
    return $init[$id];
}

//Explicit Commands
function info() {
    global $database;
    print_r($database->info());
}

function currentdatetime() {
    date_default_timezone_set('Asia/Manila');
    return date("Y-m-d H:i:s");
}

function allowOrDie($data) {
    $user = fromSession("user");
    if ($data == "user") {
        if ($user == null) {
            sendTo("logout");
        }
    } else if (!in_array($user["position"], $data)) {
        sendTo("logout");
    }
}

function linkTo($data) {
    if($data == "back"){
        return $_SERVER['HTTP_REFERER'];
    }else{
        return "http://" . getInit('localhost') . "/" . $data;
    }
}

function sendTo($data) {
    if($data == "back"){
        header('Location: ' . $_SERVER['HTTP_REFERER']);
    }else{
        header("Location: http://" . getInit('localhost') . "/" . $data);
    }
    die();
}

function getPage($data) {
    $dir = $_SERVER['DOCUMENT_ROOT'];
    include $dir . "/view/" . $data . ".php";
}

function linkPage($data) {
    $dir = $_SERVER['DOCUMENT_ROOT'];
    return $dir . "/view/" . $data . ".php";
}

function linkPublic($data) {
    $dir = $_SERVER['DOCUMENT_ROOT'];
    return "http://" . getInit("localhost") . "/public/" . $data;
}

//////////////////////////
// ADMIN FUNCTIONS
//////////////////////////

function randomKey() {
    $key = "";
    $salt = rand(100000, 999999);
    $x = 0;
    while ($x < 1) {
        $key = md5(sha1(crypt($key, $salt)));
        $x++;
    }
    return $key;
}

function selectTable($table, $data = null) {
    global $database;

    if ($data == null) {
        $data = $database->select($table, "*");
    } else {
        $data = $database->select($table, "*", $data);
    }
    if (!isset($data)) {
        return [];
    } else {
        return $data;
    }
}

function deleteTable($table, $data) {
    global $database;

    if (is_array($data)) {
        $data = $database->delete($table, $data);
    } else {
        $data = $database->delete($table, ["id" => $data]);
    }
    if (!isset($data)) {
        return null;
    } else {
        return $data;
    }
}

function getTable($table, $data) {
    global $database;
    try {
        if (is_array($data)) {
            $data = $database->get($table, "*", $data);
        } else {
            $data = $database->get($table, "*", ["id" => $data]);
        }
    } catch (Exception $exc) {
        echo $exc->getTraceAsString();
    }
    if (!isset($data)) {
        return null;
    } else {
        return $data;
    }
}

function insertTable($table, $data) {
    global $database;
    $data = $database->insert($table, $data);
    if (!isset($data)) {
        return null;
    } else {
        return $data;
    }
}

function updateTable($table, $data, $id) {
    global $database;
    $data["date_updated"] = currentdatetime();
    if (is_array($data) && !is_array($id)) {
        $data = $database->update($table, $data, ["id" => $id]);
    } else if (is_array($data) && is_array($id)) {
        $data = $database->update($table, $data, $id);
    } else {
        $data = "invalid parameters";
    }
    if (!isset($data)) {
        return null;
    } else {
        return $data;
    }
}

//////////////////////////
// HELPER FUNCTIONS
//////////////////////////

function toSession($name, $data) {
    if (session_status() == 1) {
        session_start();
    }
    $_SESSION[$name] = $data;
    return $_SESSION[$name];
}

function fromSession($name) {
    if (session_status() == 1) {
        session_start();
    }
    if (isset($_SESSION[$name])) {
        return $_SESSION[$name];
    } else {
        return null;
    }
}

function stopSession() {
    session_cache_expire();
    session_destroy();
    toSession("user", null);
}

function getGet($key = null) {
    if (isset($_GET)) {
        $g = $_GET;
        if ($key != null) {
            if (isset($_GET[$key])) {
                return $g[$key];
            } else {
                return null;
            }
        } else {
            return $g;
        }
    } else {
        return [];
    }
}

function getPost($key = null) {
    if (isset($_POST)) {
        $p = $_POST;
        if ($key != null) {
            if (isset($_POST[$key])) {
                return $p[$key];
            } else {
                return null;
            }
        } else {
            return $p;
        }
    } else {
        return [];
    }
}

function fileUpload($dir, $file, $id) {
    $target_dir = $_SERVER['DOCUMENT_ROOT'] . "/public/" . $dir;
    $uploadfile = $target_dir . $id . "_" . basename($file["name"]);
//    $uploadfile = $target_dir . $name;
//    $fileType = pathinfo($uploadfile, PATHINFO_EXTENSION);
    try {
        if (move_uploaded_file($file['tmp_name'], $uploadfile)) {
            echo "File is valid, and was successfully uploaded.\n";
        } else {
            echo "Possible file upload attack!\n";
        }
    } catch (Exception $exc) {
        print_r($_FILES);
        echo $exc->getTraceAsString();
    }

    return $id . "_" . basename($file["name"]);
}