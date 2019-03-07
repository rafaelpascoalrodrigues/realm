<?php
namespace Realm;

class Viewer {
    private $realm;


    public function __construct() {
        global $REALM;

        $this->realm = &$REALM;
       
        return true;
    }


    public function Show() {
        self::DefaultNotFound();
    }


    private function DefaultNotFound() {
        header("HTTP/1.0 404 Not Found", true, 404);

        echo '<html><body>';
        echo '<div style="font-family: sans-serif; color: #000; font-size: 4.6em; font-weight: bold; margin:  0.0em  0.0em -1.2em  0.0em;">404</div>';
        echo '<div style="font-family: sans-serif; color: #000; font-size: 2.0em; font-weight: bold; margin:  0.3em  0.0em  0.0em  4.0em;">Not</div>';
        echo '<div style="font-family: sans-serif; color: #000; font-size: 2.0em; font-weight: bold; margin: -0.3em  0.0em  0.0em  4.0em;">Found</div>';
        echo '<hr style="border: 0.1em solid #000;"/>';
        echo '<div style="font-family: sans-serif; color: #000; font-size: 0.8em; font-style: italic; ">Realm over ' . $_SERVER['SERVER_SOFTWARE'] . '</div>';
        echo '</body></html>';
    }
}
