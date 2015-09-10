<?php


class ControllerFactory
{

   static function getController($module,$view)
    {

        require_once('YogController.php');
        $controller = new YogController();
        $controller->setup($module,$view);
        return $controller;
    }

}