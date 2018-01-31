<?php

include_once ('dbconnector/dbPost.php');
include_once ('dbconnector/dbConUser.php');
include_once ('dbconnector/dbAssignment.php');

class postController extends baseController {

    public function myPosts() {
        if ($_SESSION["user"]->getType() != 'AUT') {
            return $this->notAuthorized();
        }
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();

        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $posts = $conPostDB->getByAuthor($_SESSION["user"]->getID());
        $conPostDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('myposts.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["posts"] = $posts;
        return $template->render($template_params);
    }

    public function allPosts() {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();

        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $posts = $conPostDB->getAll();
        $conPostDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('allposts.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["posts"] = $posts;
        return $template->render($template_params);
    }

    public function assignedPosts($id) {
        if ($_SESSION["user"]->getType() != 'REV') {
            return $this->notAuthorized();
        }
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();

        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conAssignmentDB = new dbAssignment();
        $conAssignmentDB->Connect();
        $posts = $conAssignmentDB->getAssignedPosts($id);
        $conAssignmentDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('assignedposts.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["posts"] = $posts;
        return $template->render($template_params);
    }

    public function create() {
        $msg = null;
        if (array_key_exists("title", $_POST) || array_key_exists("abstract", $_POST) || array_key_exists("pdffile", $_FILES)) {
            if (!array_key_exists("title", $_POST)) {
                $msg = "Title required";
            }
            if (!array_key_exists("abstract", $_POST)) {
                $msg = "Abstract required";
            }
            if (!array_key_exists("pdffile", $_POST)) {
                $msg = "PDF file required";
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
                        $dbPostConnect->update($post);
                        $dbPostConnect->Disconnect();
                        return $this->view($id);
                    }else{
                        $msg = "PDF file required";
                    }
                }
            }
            $dbPostConnect->update($post);
            $dbPostConnect->Disconnect();
        }

        if ($_SESSION["user"]->getType() != 'AUT') {
            return $this->notAuthorized();
        }
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('createpost.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["msg"] = $msg;
        return $template->render($template_params);
    }

    public function edit($id, $msg = null) {
        if ($_SESSION["user"]->getType() != 'AUT') {
            return $this->notAuthorized();
        }
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $post = $conPostDB->getById($id);
        $conPostDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('editpost.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["msg"] = $msg;
        $template_params["post"] = $post;
        return $template->render($template_params);
    }

    public function published() {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        if ($_SESSION["user"] != null) {
            $authorization = $_SESSION["user"]->getType();
        } else {
            $authorization = "NON";
        }
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $posts = $conPostDB->getPublished();
        $conPostDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('published.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["posts"] = $posts;
        return $template->render($template_params);
    }

    public function view($id) {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        if ($_SESSION["user"] != null) {
            $authorization = $_SESSION["user"]->getType();
        } else {
            $authorization = "NON";
        }
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $post = $conPostDB->getById($id);
        $conPostDB->Disconnect();

        $conUserDB = new dbConUser();
        $conUserDB->Connect();
        $author = $conUserDB->getById($post->getID());
        $conUserDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('viewpost.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["post"] = $post;
        $template_params["author"] = $author;
        return $template->render($template_params);
    }

    public function addReview($id) {
        if ($_SESSION["user"]->getType() != 'REV') {
            return $this->notAuthorized();
        }

        $reviewDb = new dbReview();
        $reviewDb->Connect();
        if ($reviewDb->hasReviewed($_SESSION["user"]->getID(), $_GET["id"])) {
            return $this->message("You have already reviewed this post.");
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
            return $this->message("Review added.");
        }

        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $post = $conPostDB->getById($id);
        $conPostDB->Disconnect();

        $conUserDB = new dbConUser();
        $conUserDB->Connect();
        $author = $conUserDB->getById($post->getID());
        $conUserDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('addreview.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["post"] = $post;
        $template_params["author"] = $author;
        return $template->render($template_params);
    }

    public function assign($id) {
        if ($_SESSION["user"]->getType() != 'ADM') {
            return $this->notAuthorized();
        }
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $post = $conPostDB->getById($id);
        $conPostDB->Disconnect();

        $conUserDB = new dbConUser();
        $conUserDB->Connect();
        $reviewers = $conUserDB->getReviewers();
        $author = $conUserDB->getById($post->getAuthorID());
        $conUserDB->Disconnect();

        $rights = array();
        $conAssignmentDB = new dbAssignment();
        $conAssignmentDB->Connect();
        foreach ($reviewers as $reviewer) {
            $rights[$reviewer->getID()] = $conAssignmentDB->hasAssigned($id, $reviewer->getID());
        }
        $conAssignmentDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('assignpost.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["post"] = $post;
        $template_params["rights"] = $rights;
        $template_params["author"] = $author;
        $template_params["reviewers"] = $reviewers;
        return $template->render($template_params);
    }

    public function showReviews() {
        if ($_SESSION["user"]->getType() != 'ADM') {
            return $this->notAuthorized();
        }
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);
        $dbReview = new dbReview();
        $dbReview->Connect();
        $reviews = $dbReview->getByPost($_GET["id"]);
        $dbReview->Disconnect();

        $dbUser = new dbConUser();
        $authors = array();
        $dbUser->Connect();
        foreach ($reviews as $r) {
            $authors[$r->getID()] = $dbUser->getById($r->getAuthorID());
        }
        $dbUser->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('showreviews.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["reviews"] = $reviews;
        $template_params["authors"] = $authors;
        return $template->render($template_params);

    }

}