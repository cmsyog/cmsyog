<?php


class HomeViewDetail extends YogView {

    function __construct()
    {
        $this->dir=__DIR__;
    }

    function display() {
        $tpl =  new YogTemplate();
        $tpl->hello = "I'm very Tiny!";
        echo $tpl->render($this->dir,"main.tpl");

    }
}