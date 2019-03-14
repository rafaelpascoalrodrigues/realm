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
        $this->realm->router = self::FindDomainFiles($this->realm->domain, $this->realm->subdomain, $this->realm->request);

        if ($this->realm->router->code->exists) {
            include $this->realm->router->code->path;
        }

        if ($this->realm->redirect != "") {
            $redirect = preg_replace('/(?<!http:)\/{2,}/', '/', $this->realm->uri->full . '/' . $this->realm->redirect);
            header("Location: " . $redirect, true, 307);
        } else {
            if ($this->realm->router->template->exists) {
                include $this->realm->router->template->path;
            }

            if (!$this->realm->router->code->exists && !$this->realm->router->template->exists) {
                self::DefaultNotFound();
            }
        }

        return;
    }

    private static function FindDomainFiles($domain, $subdomain, $request) {
        $files = array();

        $path = DOMAINS;
        if ($domain != "") {
            $path .= DIRECTORY_SEPARATOR . $domain;
            if ($subdomain != "") {
                $path .= DIRECTORY_SEPARATOR . $subdomain;
                if ($request != "") {
                    $path .= DIRECTORY_SEPARATOR . $request;
                }
            }
        }


        $code_exists = file_exists($path . '.php');
        $template_exists = file_exists($path . '.html');
        if ($code_exists || $template_exists) {
            $files['code'] = array(
                'path' => $path . '.php',
                'exists' => file_exists($path . '.php'),
            );

            $files['template'] = array(
                'path' => $path . '.html',
                'exists' => file_exists($path . '.html'),
            );

            return $files;
        } else {
            if ($request != "") {
                $path = substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
            }

            for ($lifespan = 10; $lifespan > 0; $lifespan--) {
                $code_exists = file_exists($path . DIRECTORY_SEPARATOR . 'default.php');
                $template_exists = file_exists($path . DIRECTORY_SEPARATOR . 'default.html');
                if ($code_exists || $template_exists) {
                    $files['code'] = array(
                        'path' => $path . DIRECTORY_SEPARATOR . 'default.php',
                        'exists' => file_exists($path . DIRECTORY_SEPARATOR . 'default.php'),
                    );

                    $files['template'] = array(
                        'path' => $path . DIRECTORY_SEPARATOR . 'default.html',
                        'exists' => file_exists($path . DIRECTORY_SEPARATOR . 'default.html'),
                    );

                    return $files;
                }

                $path = substr($path, 0, strrpos($path, DIRECTORY_SEPARATOR));
            }
        }

        return $files;
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
