<?php

if (!isset($_GET['rt'])) {
    $controllerName = 'loginController';
    $action = 'index';
} else {
    $rt = $_GET['rt'];
    $parts = explode('/', $rt);
    $controllerName = $parts[0] . 'Controller';
    $action = isset($parts[1]) ? $parts[1] : 'index';
}

if($controllerName==='loginController' && $action==='index' && isset($_COOKIE['oib']) && isset($_COOKIE['ovlasti'])){
    $keksic = $_COOKIE['ovlasti'];
    if($keksic === 0){
      $action = 'provjeraLijecnik';
    }
    else if($keksic === 1){
      $action = 'provjeraPacijent';
    }
    else {
      $action = 'provjeraAdmin';
    }
}

if(!isset($_COOKIE['oib']) && !isset($_POST['oib'])){
  $controllerName = 'loginController';
}

require_once __DIR__ . '/controller/' . $controllerName . '.class.php';

$c = new $controllerName;

$c->$action();

?>
