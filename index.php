<?php

include_once ('inc/db_settings.inc.php');
include_once ('dbconnector/dbReview.php');
include_once ('model/review.php');
include_once ('inc/functions.php');

include_once ('controller/baseController.php');
include_once ('controller/menuController.php');
include_once ('controller/homeController.php');
include_once ('controller/userController.php');
include_once ('controller/postController.php');
include_once ('dbconnector/dbConUser.php');
include_once ('dbconnector/dbPost.php');

$connector_dbReview = new dbReview();
$connector_dbReview->Connect();

$menuCtrl = new menuController();
$homeCtrl = new homeController();

$page = null;
$user = null;
session_start();

if (!array_key_exists("user", $_SESSION)) {
    $_SESSION["user"] = null;
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

        case "myposts":
            $postCtrl = new postController();
            echo $postCtrl->myPosts();
            break;

        case "published":
            $postCtrl = new postController();
            echo $postCtrl->published();
            break;

        case "viewpost":
            $postCtrl = new postController();
            echo $postCtrl->view($_GET["id"]);
            break;

        case "createpost":
            if (array_key_exists("title", $_POST) && array_key_exists("abstract", $_POST) && array_key_exists("pdffile", $_FILES)) {
                $post = new post();
                $post->setTitle($_POST["title"]);
                $post->setAbstract($_POST["abstract"]);
                $post->setState(0);
                $post->setAuthorID($_SESSION["user"]->getID());

                $dbPostConnect = new dbPost();
                $dbPostConnect->Connect();
                $post = $dbPostConnect->create($post);

                if (array_key_exists("pdffile", $_FILES)) {
                    if(isset($_FILES['pdffile'])){
                        $errors= array();
                        $file_name = $_FILES['pdffile']['name'];
                        $file_size = $_FILES['pdffile']['size'];
                        $file_tmp  = $_FILES['pdffile']['tmp_name'];
                        $file_type = $_FILES['pdffile']['type'];
                        $expl = explode('.',$_FILES['pdffile']['name']);
                        $end = end($expl);
                        $file_ext = strtolower($end);
                        $expension = 'pdf';

                        if($file_ext != $expension){
                            $errors[] = "extension not allowed, please choose a PDF file.";
                        }

                        if($file_size > 20971520){
                            $errors[] = 'File size must be excately 20 MB';
                        }

                        if(empty($errors)==true){
                            move_uploaded_file($file_tmp,"posts/" . $post->getID() . ".pdf");
                            $post->setFilename($post->getID() . ".pdf");
                            echo "Success";
                        }else{
                            print_r($errors);
                        }
                    }
                }

                $dbPostConnect->update($post);
                $dbPostConnect->Disconnect();
            }


            $postCtrl = new postController();
            echo $postCtrl->create();
            break;

        case "editpost":
            if (array_key_exists("title", $_POST) && array_key_exists("abstract", $_POST)) {
                $dbPostConnect = new dbPost();
                $dbPostConnect->Connect();
                $post = $dbPostConnect->getById($_GET["id"]);
                $post->setTitle($_POST["title"]);
                $post->setAbstract($_POST["abstract"]);

                if (array_key_exists("pdffile", $_FILES)) {
                    if(isset($_FILES['pdffile'])){
                        $errors= array();
                        $file_name = $_FILES['pdffile']['name'];
                        $file_size = $_FILES['pdffile']['size'];
                        $file_tmp  = $_FILES['pdffile']['tmp_name'];
                        $file_type = $_FILES['pdffile']['type'];
                        $expl = explode('.',$_FILES['pdffile']['name']);
                        $end = end($expl);
                        $file_ext = strtolower($end);
                        $expension = 'pdf';

                        if($file_ext != $expension){
                            $errors[] = "extension not allowed, please choose a PDF file.";
                        }

                        if($file_size > 20971520){
                            $errors[] = 'File size must be excately 20 MB';
                        }

                        if(empty($errors)==true){
                            move_uploaded_file($file_tmp,"posts/" . $post->getID() . ".pdf");
                            $post->setFilename($post->getID() . ".pdf");
                            echo "Success";
                        }else{
                            print_r($errors);
                        }
                    }
                }

                $dbPostConnect->update($post);
                $dbPostConnect->Disconnect();
            }

            if (!array_key_exists("id", $_GET)) {
                echo $homeCtrl->indexAction($_SESSION["user"]);
            } else {
                $postCtrl = new postController();
                echo $postCtrl->edit($_GET["id"]);
            }
            break;

        default:
            echo $homeCtrl->indexAction($_SESSION["user"]);
            break;
    }
} else {
    echo $homeCtrl->indexAction($_SESSION["user"]);
}

