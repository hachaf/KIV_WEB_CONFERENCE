<?php

include_once ('dbconnector/dbConUser.php');
include_once 'menuController.php';

class userController {

    public function usersList() {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();

        if (array_key_exists("user", $_SESSION)) {
            $authorization = $_SESSION["user"]->getType();
            $menu = $menuCtrl->render($authorization);
        } else {
            $authorization = 'NON';
            $menu = $menuCtrl->render($authorization);
        }

        $conUserDB = new dbConUser();
        $conUserDB->Connect();
        $users = $conUserDB->getAll();
        $conUserDB->Disconnect();

        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('userslist.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["users"] = $users;
        return $template->render($template_params);
    }

    public function edit($id) {
        $conUserDB = new dbConUser();
        $conUserDB->Connect();
        $user = $conUserDB->getById($id);
        $conUserDB->Disconnect();

        $menuCtrl = new menuController();
        $menu = $menuCtrl->render($_SESSION["user"]->getType());

        require_once 'twig/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('edituser.html');
        $template_params = array();
        $template_params["user"] = $user;
        $template_params["menu"] = $menu;
        return $template->render($template_params);
    }

    public function save($id, $login, $password, $type, $blocked) {
        $conUserDB = new dbConUser();
        $conUserDB->Connect();
        $user = $conUserDB->getById($id);
        $user->setLogin($login);
        $user->setType($type);
        $user->setPassword($password);
        $user->setBlocked($blocked);
        $conUserDB->update($user);
        $conUserDB->Disconnect();
    }

}