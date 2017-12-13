<?php

function phpWrapperFromFile($filename, $params = array()) {
    extract($params);
    ob_start();

    if (file_exists($filename) && !is_dir($filename))
    {
        include($filename);
    }

    // nacte to z outputu
    $obsah = ob_get_clean();
    return $obsah;
}

function printr($array) {
    echo "<hr /><pre>";
    echo print_r($array);
    echo "</pre><hr />";
}