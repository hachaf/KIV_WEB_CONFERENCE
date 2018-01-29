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
        return $template->render($template_params);
    }

    public function edit($id) {
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
        $template_params["post"] = $post;
        return $template->render($template_params);
    }

    public function published() {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        $authorization = $_SESSION["user"]->getType();
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
        $authorization = $_SESSION["user"]->getType();
        $menu = $menuCtrl->render($authorization);

        $conPostDB = new dbPost();
        $conPostDB->Connect();
        $post = $conPostDB->getById($id);
        $conPostDB->Disconnect();

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('viewpost.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["post"] = $post;
        return $template->render($template_params);
    }

    public function addReview($id) {
        if ($_SESSION["user"]->getType() != 'REV') {
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
        $template = $twig->loadTemplate('addreview.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["post"] = $post;
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

}