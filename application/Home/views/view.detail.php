<?php


class HomeViewDetail extends YogView
{

    function __construct()
    {
        $this->dir = __DIR__;
    }

    function display()
    {
//        $tpl = new YogTemplate();
//        $tpl->hello = "I'm very Tiny!";
//        echo $tpl->render($this->dir, "main.tpl");
        require_once('Core/Session/RedisSession.php');
        RedisSession::init(array(
            'session_name' => 'redis_session',
            'cookie_path' => '/',
            'cookie_domain' => '.yog.com',
            'lifetime' => 3600,
            'server' => array(
                'host' => '127.0.0.1',
                'port' => 6379)));


        $_SESSION['z'] = 'zhang';
        echo $_SESSION['z'] ;

        $redis=new YogRedis();
        $redis::init();
        $redis::set('z','jian');
        var_dump($redis::get('z'));
    }
}