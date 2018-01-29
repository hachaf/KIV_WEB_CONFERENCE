<?php

include_once ('inc/router.php');

$connector_dbReview = new dbReview();
$connector_dbReview->Connect();

$homeCtrl = new homeController();
session_start();

if (!array_key_exists("user", $_SESSION)) {
    $_SESSION["user"] = null;
}

/**
 * Routing
 */

$router = new router();

if (array_key_exists("p",  $_GET)) {
    $router->route($_GET["p"]);
} else {
    echo $homeCtrl->indexAction($_SESSION["user"]);
}

