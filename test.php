<?php

include_once ('inc/db_settings.inc.php');
include_once ('dbconnector/dbReview.php');
include_once ('model/review.php');
include_once ('inc/functions.php');

include_once ('controller/baseController.php');
include_once ('controller/menuController.php');
include_once ('controller/homeController.php');
include_once ('controller/userController.php');
include_once ('dbconnector/dbConUser.php');

$connector_dbReview = new dbReview();
$connector_dbReview->Connect();

$menuCtrl = new menuController();
$homeCtrl = new homeController();

$page = null;
$user = null;
session_start();

if (session_id() == '' || !isset($_SESSION) || $_SESSION["user"]->getType() == 'NON') {

    $regMsg = null;
    if (array_key_exists("username", $_POST) && array_key_exists("pwd", $_POST)) {
        $connector_dbUser = new dbConUser();
        $connector_dbUser->connect();
        $user = $connector_dbUser->getByNameAndPwd($_POST["username"], $_REQUEST["pwd"]);
    }
    if (array_key_exists("reg-username", $_POST) && array_key_exists("reg-pwd", $_POST)) { //pokus o registraci

        $connector_dbUser = new dbConUser();
        $connector_dbUser->Connect();
        if (!$connector_dbUser->userExist($_POST["reg-username"])) { //uzivatel neni v databazi a lze je zalozit
            $user = new conUser();
            $user->setLogin($_POST["reg-username"]);
            $user->setBlocked(0);
            $user->setPassword($_POST["reg-pwd"]);
            $user->setType("AUT");
            $connector_dbUser->create($user);
        } else { //uzivatel jiz existuje
            $regMsg = 'User with this name already exists.';
        }
    }
    if ($user != null) {
        session_start();
        $_SESSION["user"] = $user;
    }
}

/**
 * Routing
 */
if (array_key_exists("p",  $_GET)) {

    switch ($_GET["p"]) {

        case "login":
            if (array_key_exists("username", $_POST)) {
                $connector_dbUser = new dbConUser();
                $connector_dbUser->connect();
                $user = $connector_dbUser->getByNameAndPwd($_POST["username"], $_REQUEST["pwd"]);
                $connector_dbUser->Disconnect();
                if ($user != null) {
                    $_SESSION["user"] = $user;
                    echo $homeCtrl->indexAction($_SESSION["user"]);
                }
            }
            echo $homeCtrl->login();
            break;

        case "logout":
            session_abort();
            echo $homeCtrl->indexAction(null);
            break;

        case "register":
            if (array_key_exists("reg-username", $_POST) && array_key_exists("reg-pwd", $_POST)) { //pokus o registraci
                $connector_dbUser = new dbConUser();
                $connector_dbUser->Connect();
                if (!$connector_dbUser->userExist($_POST["reg-username"])) { //uzivatel neni v databazi a lze je zalozit
                    $user = new conUser();
                    $user->setLogin($_POST["reg-username"]);
                    $user->setBlocked(0);
                    $user->setPassword($_POST["reg-pwd"]);
                    $user->setType("AUT");
                    $connector_dbUser->create($user);
                    echo $homeCtrl->register($user);
                } else { //uzivatel jiz existuje
                    $regMsg = 'User with this name already exists.';
                    echo $homeCtrl->register(null, $regMsg);
                }
            } else {
                echo $homeCtrl->register(null);
            }
            break;

        case "edituser":
            if (array_key_exists("login", $_POST)) {
                $userCtrl = new userController();
                if (isset($_POST["blocked"]) && $_POST["blocked"] == 1) {
                    $blocked = 1;
                } else {
                    $blocked = 0;
                }
                $userCtrl->save($_GET["id"], $_POST["login"], $_POST["password"], $_POST["type"], $blocked);
            }
            if (array_key_exists("id", $_GET)) {
                $userCtrl = new userController();
                echo $userCtrl->edit($_GET["id"]);
            } else {
                echo $homeCtrl->indexAction($_SESSION["user"]);
            }
            break;

        case "userslist":
            $userCtrl = new userController();
            echo $userCtrl->usersList();
            break;

        default:
            echo $homeCtrl->indexAction($_SESSION["user"]);
            break;
    }
} else {
    echo $homeCtrl->indexAction($_SESSION["user"]);
}

