<?php

class baseController {

    public function notAuthorized() {
        return $this->message("You are not authorized.");
    }

    public function message($message) {
        require_once 'menuController.php';
        require_once 'twig/lib/Twig/Autoloader.php';
        $menuCtrl = new menuController();
        if (array_key_exists("user", $_SESSION)) {
            $authorization = $_SESSION["user"]->getType();
        } else {
            $authorization = 'NON';
        }
        $menu = $menuCtrl->render($authorization);

        Twig_Autoloader::register();
        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader);
        $template = $twig->loadTemplate('message.html');
        $template_params = array();
        $template_params["menu"] = $menu;
        $template_params["message"] = $message;
        return $template->render($template_params);
    }

}