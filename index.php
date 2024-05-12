<?php

if (!isset($_GET['rt'])){
    $con = 'pacijent';
    $action = 'index';
} else {
    $rt = $_GET['rt'];
    $x = explode('/', $rt);
    if (count($x) == 1){
        $con = $x[0];
        $action = 'index';
    } else {
        $con = $x[0];
        $action = $x[1];
    }
}

  $controllerName = $con . 'Controller';
  require_once __DIR__ . '/controller/' . $controllerName . '.class.php';
  $c = new $controllerName;
  $c->$action();

?>
