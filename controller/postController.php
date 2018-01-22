<?php

include_once ('dbconnector/dbPost.php');

class postController {

    public function myPosts() {
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

    public function edit($id) {
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

}