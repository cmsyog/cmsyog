<?php

require_once('YogView.php');

class ViewFactory
{
    function loadView($type = 'detail', $module, $bean = null, $view_object_map = array(), $target_module = '')
    {
        $type = strtolower($type);
        $view = null;
        if (file_exists(BASEPATH.'application/' . $module . '/views/view.' . $type . '.php')) {
            $view = ViewFactory::_buildFromFile(BASEPATH.'application/' . $module . '/views/view.' . $type . '.php', $bean, $view_object_map, $type, $module);
        } else {
            $file = BASEPATH.'/views/view.' . $type . '.php';
            if (file_exists($file)) {
                $view = ViewFactory::_buildFromFile($file, $bean, $view_object_map, $type, $module);
            }
        }
        if (!isset($view))
            $view = new YogView();

        return $view;
    }

    function _buildFromFile($file, &$bean, $view_object_map, $type, $module)
    {
        require_once($file);

        $class = ucfirst($module) . 'View' . ucfirst($type);

        if (class_exists($class)) {
            return ViewFactory::_buildClass($class, $bean, $view_object_map);
        }
        return new YogView($bean, $view_object_map);
    }


    function _buildClass($class, $bean, $view_object_map)
    {
        $view = new $class();
        if ($view instanceof YogView) {
            return $view;
        } else
            return new YogView($bean, $view_object_map);
    }
}