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
                if (array_key_exists("username", $_POST)) {
                    $connector_dbUser = new dbConUser();
                    $connector_dbUser->connect();
                    $user = $connector_dbUser->getByNameAndPwd($_POST["username"], $_REQUEST["pwd"]);
                    $connector_dbUser->Disconnect();
                    if ($user != null) {
                        $_SESSION["user"] = $user;
                        echo $this->homeCtrl->indexAction($_SESSION["user"]);
                    } else {
                        echo $this->homeCtrl->login("Wrong login or password");
                    }
                } else {
                    echo $this->homeCtrl->login();
                }
                break;

            case "logout":
                session_abort();
                echo $this->homeCtrl->indexAction(null);
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
                        echo $this->homeCtrl->register($user);
                    } else { //uzivatel jiz existuje
                        $msg = 'User with this name already exists.';
                        echo $this->homeCtrl->register(null, $msg);
                    }
                } else {
                    echo $this->homeCtrl->register(null);
                }
                break;

            case "edituser":
                if (array_key_exists("login", $_POST)) {
                    $blocked = (isset($_POST["blocked"]) && $_POST["blocked"] == 1) ? 1 : 0;
                    $this->userCtrl->save($_GET["id"], $_POST["login"], $_POST["password"], $_POST["type"], $blocked);
                }
                if (array_key_exists("id", $_GET)) {
                    echo $this->userCtrl->edit($_GET["id"]);
                } else {
                    echo $this->homeCtrl->indexAction($_SESSION["user"]);
                }
                break;

            case "userslist":
                echo $this->userCtrl->usersList();
                break;

            case "myposts":
                echo $this->postCtrl->myPosts();
                break;

            case "published":
                echo $this->postCtrl->published();
                break;

            case "allposts":
                echo $this->postCtrl->allPosts();
                break;

            case "assignedposts":
                echo $this->postCtrl->assignedPosts($_SESSION["user"]->getID());
                break;

            case "addreview":
                $reviewDb = new dbReview();
                $reviewDb->Connect();
                if ($reviewDb->hasReviewed($_SESSION["user"]->getID(), $_GET["id"])) {
                    echo $this->postCtrl->message("You have already reviewed this post.");
                    break;
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
                echo $this->postCtrl->addReview($_GET["id"]);
                break;

            case "viewpost":
                echo $this->postCtrl->view($_GET["id"]);
                break;

            case "assignpost":
                $dbAssignmentConnection = new dbAssignment();
                $dbAssignmentConnection->Connect();
                if (array_key_exists("add", $_GET)) {
                    $dbAssignmentConnection->assignPost($_GET["id"], $_GET["add"]);
                }
                if (array_key_exists("rem", $_GET)) {
                    $dbAssignmentConnection->unassignPost($_GET["id"], $_GET["rem"]);
                }
                $dbAssignmentConnection->Disconnect();
                echo $this->postCtrl->assign($_GET["id"]);
                break;

            case "createpost":
                if (array_key_exists("title", $_POST) || array_key_exists("abstract", $_POST) || array_key_exists("pdffile", $_FILES)) {
                    if (!array_key_exists("title", $_POST)) {
                        $msg = "Title required";
                        echo $this->postCtrl->create($msg);
                        break;
                    }
                    if (!array_key_exists("abstract", $_POST)) {
                        $msg = "Abstract required";
                        echo $this->postCtrl->create($msg);
                        break;
                    }
                    if (!array_key_exists("pdffile", $_POST)) {
                        $msg = "PDF file required";
                        echo $this->postCtrl->create($msg);
                        break;
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
                echo $this->postCtrl->create();
                break;

            case "editpost":
                $msg = null;
                if (array_key_exists("title", $_POST) && !array_key_exists("abstract", $_POST)) {
                    $msg = "Abstract required";
                    echo $this->postCtrl->edit($_GET["id"], $msg);
                    break;
                }
                if (!array_key_exists("title", $_POST) && array_key_exists("abstract", $_POST)) {
                    $msg = "Title required";
                    echo $this->postCtrl->edit($_GET["id"], $msg);
                    break;
                }

                if (!isset($_FILES['pdffile']) || !array_key_exists("pdffile", $_FILES)) {
                    $msg = "Post PDF file required";
                    echo $this->postCtrl->edit($_GET["id"], $msg);
                    break;
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
                    echo $this->homeCtrl->indexAction($_SESSION["user"]);
                } else {
                    echo $this->postCtrl->edit($_GET["id"], $msg);
                }
                break;

            default:
                echo $this->homeCtrl->indexAction($_SESSION["user"]);
                break;
        }
    }

}