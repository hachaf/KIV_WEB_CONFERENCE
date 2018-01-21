<?php

class menuController {

    public function render($authorization) {

        require_once 'twig/lib/Twig/Autoloader.php';
        Twig_Autoloader::register();

        $loader = new Twig_Loader_Filesystem('view');
        $twig = new Twig_Environment($loader); // takhle je to bez cache
        $template = $twig->loadTemplate('menu.html');

        $template_params = array();
        $template_params["authorization"] = $authorization;
        return $template->render($template_params);
    }

}