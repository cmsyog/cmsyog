<?php
require_once('Core/Controller/ControllerFactory.php');
require_once('Core/View/ViewFactory.php');
require_once('Core/Session/RedisSession.php');

class YogApplication
{
    var $controller = null;
    var $headerDisplayed = false;
    var $default_module = 'Home';
    var $default_view = 'detail';
    var $default_action = 'sidecar';
    protected $whiteListActions = array(
        'index',
        'ListView',
        'DetailView',
        'EditView',
    );

    function YogApplication()
    {
        $session =  RedisSession::init();
        session_set_save_handler(
            array($session, "open"),
            array($session, "close"),
            array($session, "read"),
            array($session, "write"),
            array($session, "destroy"),
            array($session, "gc")
        );
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
        $this->controller = ControllerFactory::getController($module, $view);

        $this->controller->execute();

    }

    static function redirect(
        $url
    )
    {
        if (headers_sent()) {
            echo "<script>document.location.href='$url';</script>\n";
        } else {
            session_write_close();
            header('HTTP/1.1 301 Moved Permanently');
            header("Location: " . $url);
        }
        exit();
    }


    function startSession()
    {
        $sessionIdCookie = isset($_COOKIE['PHPSESSID']) ? $_COOKIE['PHPSESSID'] : null;
        if(isset($_REQUEST['MSID'])) {
            session_id($_REQUEST['MSID']);
            session_start();
            if(isset($_SESSION['user_id']) && isset($_SESSION['seamless_login'])){
                unset ($_SESSION['seamless_login']);
            }else{
                if(isset($_COOKIE['PHPSESSID'])){
                    self::setCookie('PHPSESSID', '', time()-42000, '/');
                }
                session_destroy();
                exit('Not a valid entry method');
            }
        }else{
            if(can_start_session()){
                session_start();
            }
        }

        if ( isset($_REQUEST['login_module']) && isset($_REQUEST['login_action'])
            && !($_REQUEST['login_module'] == 'Home' && $_REQUEST['login_action'] == 'index') ) {
            if ( !is_null($sessionIdCookie) && empty($_SESSION) ) {
                self::setCookie('loginErrorMessage', 'LBL_SESSION_EXPIRED', time()+30, '/');
            }
        }

    }

    function endSession(){
        session_destroy();
    }

    public static function setCookie(
        $name,
        $value,
        $expire = 0,
        $path = '/',
        $domain = null,
        $secure = false,
        $httponly = false
    )
    {
        if ( is_null($domain) )
            if ( isset($_SERVER["HTTP_HOST"]) )
                $domain = $_SERVER["HTTP_HOST"];
            else
                $domain = 'localhost';

        if (!headers_sent())
            setcookie($name,$value,$expire,$path,$domain,$secure,$httponly);

        $_COOKIE[$name] = $value;
    }
}