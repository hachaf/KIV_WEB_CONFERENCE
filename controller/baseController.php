<?php

class baseController {

    public function loginAction() {
        $content = file_get_contents("./view/login.php");
        return $content;
    }

//    private $twig;
//
//    public function __construct($twig) {
//        $this->twig = $twig;
//    }
//
//    public function indexAction($params) {
//        echo "missing indexAction method";
//    }
//
//    public function makeUrl() {
//        return "tady bude URL";//TODO
//    }

    //public function render($obsah, $menu) {
//        $loader = new Twig_Loader_Filesystem('view');
//        $twig = new Twig_Environment($loader); // takhle je to bez cache
//        // nacist danou sablonu z adresare
//        $template = $twig->loadTemplate('view1.html');
//
//        // render vrati data pro vypis nebo display je vypise
//        // v poli jsou data pro vlozeni do sablony
//        $template_params = array();
//        $template_params["obsah"] = $obsah;
//        $template_params["nadpis1"] = "Nadpis 1";
//        echo $template->render($template_params);

        //$template = $this->twig->loadTemplate('view1.html');
        //$template_params = array();
        //$template_params["obsah"] = $obsah;
        //$template_params["nadpis1"] = "Nadpis 1";
        //echo $template->render($template_params);

//            echo "<!DOCTYPE html>
//            <html>
//                <head>
//                    <meta charset=\"UTF-8\">
//                </head>
//            <body>";
//            echo $menu;
//            echo "<h1>Start sablony</h1>";
//            echo $obsah;
//            echo "<div>Footer</div>";
//            echo "</body></html>";
    //}

}