<?php


class TestViewOut extends YogView
{

    function __construct()
    {
        $this->dir = __DIR__;
    }

    //http://www.yog.com/index.php?module=test&view=out&type=test
    function display()
    {
        require_once('Core\Response\Response.php');
        $Response = new Response();
        $Response->setSerializer(new SerializerJSON());
        $Response->setBody(json_encode(array('id'=>1)));
        $Response->Send();

    }
}