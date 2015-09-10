<?php

class YogController
{
    protected $action_remap = array('index' => 'listview');
    /**
     * The name of the current module.
     */
    public $module = 'Home';
    /**
     * The name of the target module.
     */
    public $target_module = null;
    /**
     * The name of the current action.
     */
    public $action = 'index';
    /**
     * The id of the current record.
     */
    public $record = '';
    /**
     * The name of the return module.
     */
    public $return_module = null;
    /**
     * The name of the return action.
     */
    public $return_action = null;
    /**
     * The id of the return record.
     */
    public $return_id = null;
    /**
     * If the action was remapped it will be set to do_action and then we will just
     * use do_action for the actual action to perform.
     */
    protected $do_action = 'index';
    /**
     * If a bean is present that set it.
     */
    public $bean = null;
    /**
     * url to redirect to
     */
    public $redirect_url = '';
    /**
     * any subcontroller can modify this to change the view
     */
    public $view = 'detail';
    /**
     * this array will hold the mappings between a key and an object for use within the view.
     */
    public $view_object_map = array();

    /**
     * This array holds the methods that handleAction() will invoke, in sequence.
     */
    protected $tasks = array(
        'pre_action',
        'do_action',
        'post_action'
    );
    /**
     * List of options to run through within the process() method.
     * This list is meant to easily allow additions for new functionality as well as
     * the ability to add a controller's own handling.
     */
    public $process_tasks = array(
        'blockFileAccess',
        'handleEntryPoint',
        'callLegacyCode',
        'remapAction',
        'handle_action',
        'handleActionMaps',
    );
    /**
     * Whether or not the action has been handled by $process_tasks
     *
     * @var bool
     */
    protected $_processed = false;
    /**
     * Map an action directly to a file
     */
    /**
     * Map an action directly to a file. This will be loaded from action_file_map.php
     */
    protected $action_file_map = array();
    /**
     * Map an action directly to a view
     */
    /**
     * Map an action directly to a view. This will be loaded from action_view_map.php
     */
    protected $action_view_map = array();

    /**
     * This can be set from the application to tell us whether we have authorization to
     * process the action. If this is set we will default to the noaccess view.
     */
    public $hasAccess = true;

    /**
     * Map case sensitive filenames to action.  This is used for linux/unix systems
     * where filenames are case sensitive
     */
    public static $action_case_file = array(
        'editview' => 'EditView',
        'detailview' => 'DetailView',
        'listview' => 'ListView'
    );

    public function setup($module = '', $view = '')
    {
        if (empty($module) && !empty($_REQUEST['module']))
            $module = $_REQUEST['module'];

        if (empty($view) && !empty($_REQUEST['view']))
            $view = $_REQUEST['view'];
        //set the module
        if (!empty($module))
            $this->setModule($module);
        if (!empty($view))
            $this->setView($view);


    }

    function SugarController()
    {
    }


    public function setModule($module)
    {
        $this->module = $module;
    }

    public function setView($view)
    {
        $this->view = $view;
    }

    final public function execute()
    {
        $this->process();
        if (!empty($this->view)) {
            $this->processView();
        } elseif (!empty($this->redirect_url)) {
            $this->redirect();
        }
    }

    public function process()
    {
        $GLOBALS['action'] = $this->action;
        $GLOBALS['module'] = $this->module;

        //check to ensure we have access to the module.
        if ($this->hasAccess) {
            $this->do_action = $this->action;


//            $this->loadBean();

            $processed = false;
//            foreach($this->process_tasks as $process){
//                $this->$process();
//                if($this->_processed)
//                    break;
//            }

            $this->redirect();
        } else {
            $this->no_access();
        }
    }

    protected function no_action()
    {
        die('no action');
    }

    protected function no_access()
    {
        $this->view = 'noaccess';
    }


    protected function set_redirect($url)
    {
        $this->redirect_url = $url;
    }

    protected function redirect()
    {
        if (!empty($this->redirect_url))
            YogApplication::redirect($this->redirect_url);
    }

    private function processView()
    {
        $view = ViewFactory::loadView($this->view, $this->module, $this->bean, $this->view_object_map, $this->target_module);

        $view->display();
    }

}
