<?php
namespace SlimModel;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\DBAL\DBALException;
use Doctrine\Common\EventManager;

use SlimModel\Event\IncludeManager;
use SlimModel\Event\MigrateManager;
use SlimModel\Event\ModelEventArgs;


class Base
{

    public $db;
    public $table;
    public $columns      = [];
    public $primary_key  = "id";
    public $events;

    public $includeManager  = false;
    public $migrateManager  = false;
    public $freeze          = false;
    public $includes        = false;
    public $result          = false;

    public function __construct($db = false, EventManager $eventManager = null) {
      $this->db = $db;
      $this->setup();
      if(!$eventManager) $this->events = new EventManager();
      $this->includeManager = new IncludeManager($this);
      $this->migrateManager = new MigrateManager($this);
    }


    public function setup(){}

    public function define($name, $type="string", $options=[])
    {
        $this->columns[$name] = ["type"=>$type, "options"=>$options];
    }

    public function fetch()
    {
        return $this->result;
    }

    public function first()
    {
        if(is_array($this->result)) return $this->result[0];
        return false;
    }

    protected function setResult($result)
    {
        $this->result = $result;
    }


    /* This ensures the auto-included managers are available. */
    public function initManagers()
    {
        if($this->includeManager) $this->events->addEventSubscriber($this->includeManager);
        if($this->migrateManager) $this->events->addEventSubscriber($this->migrateManager);
    }


    /* The following methods manage include functionality */
    public function add_include($type, $options = [])
    {
        if(!isset($options["table"])) throw new \InvalidArgumentException("Table must be specified in an include");
        if(!isset($options["join"])) $options["join"] = $this->table."_".$options["table"];
        if(!isset($options["key"])) $options["key"] = "id";
        if(!isset($options["join_key"])) $options["join_key"] = "id";
        if(!isset($options["as"])) $options["as"] = $options["table"];
        if(!isset($options["join_left_key"])) $options["join_left_key"] = $this->table."_id";
        if(!isset($options["join_right_key"])) $options["join_right_key"] = $options["table"]."_id";
        $options["origin"] = $this->table;
        $this->includes[$type][] = $options;
    }


    public function getManyIncludes()
    {
        if(isset($this->includes) && count($this->includes["many"])) {
            return $this->includes["many"];
        }
        return [];
    }


    /* The following methods all hit the database connection */
    public function all()
    {
        return $this->execute(function(){
            $sql = "SELECT * FROM `$this->table`";
            $this->setResult($this->db->fetchAll($sql));
        });
    }

    public function find($id)
    {
        return $this->execute(function() use($id){
            $sql = "SELECT * FROM `$this->table` WHERE `$this->primary_key` = ?";
            $this->setResult( $this->db->fetchAssoc($sql, [$id]) );
        });
    }

    public function delete($filters)
    {
        return $this->execute(function() use($filters){
            $this->setResult( $this->db->delete($this->table, $filters) );
        });
    }

    public function insert($params=[])
    {
        return $this->execute(function() use($params){
            $this->setResult( $this->db->insert($this->table, $params) );
        });
    }

    public function update($id, $params=[])
    {
        return $this->execute(function() use($id, $params){
            $this->setResult( $this->db->update($this->table, $params, [$this->primary_key => $id]) );
        });
    }


    protected function execute($callable)
    {

        if(!$this->db) throw new ConnectionException("No database Connection Specified", 1);
        $this->initManagers();
        $this->events->dispatchEvent("preFetch", new ModelEventArgs($this));

        try {
            $callable();
        } catch (DBALException $e) {
            if($this->freeze) throw $e;
            $exception = $e->getPrevious();
            $error = $exception->errorInfo;
            switch($error[0]) {
              case "HY000":
                try {
                  $this->events->dispatchEvent("onSchemaException", new ModelEventArgs($this));
                  $callable();
                } catch (Exception $e) {
                  throw new SchemaException("Invalid Schema", 1);
                }
              break;

            }
        }
        $this->events->dispatchEvent("postFetch", new ModelEventArgs($this));
        return $this->fetch();
    }




}
