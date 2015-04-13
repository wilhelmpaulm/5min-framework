<?php

$router->map('POST|GET', '/test', function() {
    echo "GET DATA ";
    echo "<br><br>";
    var_dump(getGet());
    echo "<br><hr><br>";
    echo "POST DATA \n\n";
    echo "<br><br>";
    var_dump(getPost());
    echo "<br><hr><br>";
    die();
});


$match = $router->match();

if ($match && is_callable($match['target'])) {
    call_user_func_array($match['target'], $match['params']);
} else if ($match) {
    require $match['target'];
} else {
    header("HTTP/1.0 404 Not Found");
    require '404.php';
}
  