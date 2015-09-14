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



        $_SESSION['aaa'] = "aaaaaaaa";
        $_SESSION['bbb'] = "bbbbbbbb";
        $_SESSION['ccc'] = "cccccccc";
        echo  $_SESSION['ccc'];
        var_dump($_SESSION);
        $_SESSION['ccc'] = "ddddd";

//
        $redis=new YogRedis();
        $redis::init();
        $redis::set('z','jian');
        var_dump($redis::get('z'));
    }
}