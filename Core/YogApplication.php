<?php
require_once('../Core/Controller/ControllerFactory.php');
require_once('../Core/View/ViewFactory.php');

class YogApplication
{
    var $controller = null;
    var $headerDisplayed = false;
    var $default_module = 'Home';
    var $default_view= 'main';
    var $default_action = 'sidecar';
    protected $whiteListActions = array(
        'index',
        'ListView',
        'DetailView',
        'EditView',
    );

    function YogApplication()
    {

    }

    function execute()
    {
        $module = $this->default_module;
        $view = $this->default_view;
        if (!empty($_REQUEST['module'])) {
            $module = $_REQUEST['module'];
        }
        if (!empty($_REQUEST['view'])) {
            $view = $_REQUEST['view'];
        }
        insert_charset_header();
        $this->controller = ControllerFactory::getController($module,$view);

        $this->controller->execute();

    }

   static function redirect(
        $url
    )
    {
        /*
         * If the headers have been sent, then we cannot send an additional location header
         * so we will output a javascript redirect statement.
         */
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {
            //@ob_end_clean(); // clear output buffer
            session_write_close();
            header( 'HTTP/1.1 301 Moved Permanently' );
            header( "Location: ". $url );
        }
        exit();
    }
}