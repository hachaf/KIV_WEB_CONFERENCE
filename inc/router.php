<?php

include_once ('controller/baseController.php');
include_once ('controller/menuController.php');
include_once ('controller/homeController.php');
include_once ('controller/userController.php');
include_once ('controller/postController.php');
include_once ('inc/db_settings.inc.php');
include_once ('model/review.php');
include_once ('inc/functions.php');
include_once ('dbconnector/dbReview.php');
include_once ('dbconnector/dbConUser.php');
include_once ('dbconnector/dbPost.php');
include_once ('dbconnector/dbAssignment.php');

class router {

    private $menuCtrl;
    private $homeCtrl;
    private $userCtrl;
    private $postCtrl;

    public function router() {
        $this->menuCtrl = new menuController();
        $this->homeCtrl = new homeController();
        $this->userCtrl = new userController();
        $this->postCtrl = new postController();
    }

    public function route($p) {

        switch ($p) {
            case "login":
                echo $this->login();
                break;

            case "logout":
                echo $this->logout();
                break;

            case "register":
                echo $this->register();
                break;

            case "edituser":
                echo $this->edituser();
                break;

            case "userslist":
                echo $this->userlist();
                break;

            case "myposts":
                echo $this->myposts();
                break;

            case "published":
                echo $this->published();
                break;

            case "allposts":
                echo $this->allposts();
                break;

            case "assignedposts":
                echo $this->assignedposts();
                break;

            case "addreview":
                echo $this->addreview();
                break;

            case "viewpost":
                echo $this->viewpost();
                break;

            case "assignpost":
                echo $this->assignpost();
                break;

            case "createpost":
                echo $this->createpost();
                break;

            case "editpost":
                echo $this->editpost();
                break;

            default:
                echo $this->home();
                break;
        }
    }

    private function addreview() {
        $reviewDb = new dbReview();
        $reviewDb->Connect();
        if ($reviewDb->hasReviewed($_SESSION["user"]->getID(), $_GET["id"])) {
            return $this->postCtrl->message("You have already reviewed this post.");
        }
        if (array_key_exists("reviewtext", $_POST)) {
            $review = new review();
            $review->setText($_POST["reviewtext"]);
            $review->setAuthorID($_SESSION["user"]->getID());
            $review->setPostID($_GET["id"]);
            $review->setVerdict(1);
            $review->setLocked(0);
            $review->setPublicated(date("m.d.y"));
            $reviewDb->create($review);
            $countOfReviews = $reviewDb->reviewsCount($_GET["id"]);
            $reviewDb->Disconnect();
            if ($countOfReviews >= 3) {
                $dbPost = new dbPost();
                $dbPost->Connect();
                $dbPost->publishPost($_GET["id"]);
                $dbPost->Disconnect();
            }
        }
        return $this->postCtrl->addReview($_GET["id"]);
    }

    private function allposts() {
        return $this->postCtrl->allPosts();
    }

    private function assignedposts() {
        return $this->postCtrl->assignedPosts($_SESSION["user"]->getID());
    }

    private function assignpost() {
        $dbAssignmentConnection = new dbAssignment();
        $dbAssignmentConnection->Connect();
        if (array_key_exists("add", $_GET)) {
            $dbAssignmentConnection->assignPost($_GET["id"], $_GET["add"]);
        }
        if (array_key_exists("rem", $_GET)) {
            $dbAssignmentConnection->unassignPost($_GET["id"], $_GET["rem"]);
        }
        $dbAssignmentConnection->Disconnect();
        return $this->postCtrl->assign($_GET["id"]);
    }

    private function createpost() {
        $msg = null;
        if (array_key_exists("title", $_POST) || array_key_exists("abstract", $_POST) || array_key_exists("pdffile", $_FILES)) {
            if (!array_key_exists("title", $_POST)) {
                $msg = "Title required";
                return $this->postCtrl->create($msg);
            }
            if (!array_key_exists("abstract", $_POST)) {
                $msg = "Abstract required";
                return $this->postCtrl->create($msg);
            }
            if (!array_key_exists("pdffile", $_POST)) {
                $msg = "PDF file required";
                return $this->postCtrl->create($msg);
            }
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
                    $file_size = $_FILES['pdffile']['size'];
                    $file_tmp  = $_FILES['pdffile']['tmp_name'];
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
                        $id = $post->getID();
                        $post->setFilename($id . ".pdf");
                        $this->postCtrl->view($id);
                    }else{
                        $msg = "PDF file required";
                    }
                }
            }
            $dbPostConnect->update($post);
            $dbPostConnect->Disconnect();
        }
        return $this->postCtrl->create($msg);
    }

    private function editpost() {
        $msg = null;
        if (array_key_exists("title", $_POST) && !array_key_exists("abstract", $_POST)) {
            $msg = "Abstract required";
            return $this->postCtrl->edit($_GET["id"], $msg);
        }
        if (!array_key_exists("title", $_POST) && array_key_exists("abstract", $_POST)) {
            $msg = "Title required";
            return $this->postCtrl->edit($_GET["id"], $msg);
        }

        if ((!isset($_FILES['pdffile']) || !array_key_exists("pdffile", $_FILES)) && array_key_exists("abstract", $_POST)) {
            $msg = "Post PDF file required";
            return $this->postCtrl->edit($_GET["id"], $msg);
        }
        if (array_key_exists("title", $_POST) && array_key_exists("abstract", $_POST)) {
            $dbPostConnect = new dbPost();
            $dbPostConnect->Connect();
            $post = $dbPostConnect->getById($_GET["id"]);
            $post->setTitle($_POST["title"]);
            $post->setAbstract($_POST["abstract"]);

            if (array_key_exists("pdffile", $_FILES)) {
                if(isset($_FILES['pdffile'])){
                    $errors = array();
                    $file_size = $_FILES['pdffile']['size'];
                    $file_tmp  = $_FILES['pdffile']['tmp_name'];
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
                        $msg = "Post edited";
                    }else{
                        $msg = "Error during file-uploading";
                    }
                }
            }

            $dbPostConnect->update($post);
            $dbPostConnect->Disconnect();
        }

        if (!array_key_exists("id", $_GET)) {
            return $this->homeCtrl->indexAction($_SESSION["user"]);
        } else {
            return $this->postCtrl->edit($_GET["id"], $msg);
        }
    }

    private function edituser() {
        if (array_key_exists("login", $_POST)) {
            $blocked = (isset($_POST["blocked"]) && $_POST["blocked"] == 1) ? 1 : 0;
            $this->userCtrl->save($_GET["id"], $_POST["login"], $_POST["password"], $_POST["type"], $blocked);
        }
        if (array_key_exists("id", $_GET)) {
            return $this->userCtrl->edit($_GET["id"]);
        } else {
            return $this->homeCtrl->indexAction($_SESSION["user"]);
        }
    }

    private function home() {
        return $this->homeCtrl->indexAction($_SESSION["user"]);
    }

    private function login() {
        if (array_key_exists("username", $_POST)) {
            $connector_dbUser = new dbConUser();
            $connector_dbUser->connect();
            $user = $connector_dbUser->getByNameAndPwd($_POST["username"], $_REQUEST["pwd"]);
            $connector_dbUser->Disconnect();
            if ($user != null) {
                $_SESSION["user"] = $user;
                return $this->homeCtrl->indexAction($_SESSION["user"]);
            } else {
                return $this->homeCtrl->login("Wrong login or password");
            }
        } else {
            return $this->homeCtrl->login();
        }
    }

    private function logout() {
        unset($_SESSION["user"]);
        session_abort();
        return $this->homeCtrl->indexAction(null);
    }

    private function myposts() {
        return $this->postCtrl->myPosts();
    }

    private function published() {
        return $this->postCtrl->published();
    }

    private function register() {
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
                return $this->homeCtrl->register($user);
            } else { //uzivatel jiz existuje
                $msg = 'User with this name already exists.';
                return $this->homeCtrl->register(null, $msg);
            }
        } else {
            return $this->homeCtrl->register(null);
        }
    }

    private function userlist() {
        return $this->userCtrl->usersList();
    }

    private function viewpost() {
        return $this->postCtrl->view($_GET["id"]);
    }

}