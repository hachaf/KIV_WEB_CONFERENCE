<?php

class homeController extends baseController {

    public function indexAction($user, $regMsg = null) {
        require_once 'twig/lib/Twig/Autoloader.php';
        require_once 'menuController.php';
        require_once "userController.php";

        $template_params = array();

        $menuCtrl = new menuController();
        $userCtrl = new userController();

        if ($user != null) {
            $authorization = $user->getType();
            $menu = $menuCtrl->render($authorization);
            if ($user->getType() == 'ADM') {
                $template_params["usersList"] = $userCtrl->usersList();
            } else {
                $template_params["usersList"] = null;
            }
        } else {
            $authorization = 'NON';
            $menu = $menuCtrl->render($authorization);
        }

        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('home.html');

        $template_params["authorization"] = $authorization;
        $template_params["menu"] = $menu;
        $template_params["regMsg"] = $regMsg;
        return $template->render($template_params);
    }

    public function login() {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';

        $menuCtrl = new menuController();
        $menu = $menuCtrl->render('NON');

        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('login.html');

        $template_params["menu"] = $menu;
        return $template->render($template_params);
    }

    public function register($user, $regMsg = null) {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();

        if ($user != null) {
            $authorization = $user->getType();
            $menu = $menuCtrl->render($authorization);
        } else {
            $authorization = 'NON';
            $menu = $menuCtrl->render($authorization);
        }

        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('register.html');

        $template_params["menu"] = $menu;
        $template_params["regMsg"] = $regMsg;
        return $template->render($template_params);
    }

}