<?php


class ControllerFactory
{

   static function getController($module)
    {

        require_once('YogController.php');
        $controller = new YogController();
        $controller->setup($module);
        return $controller;
    }

}