<?php
namespace SlimModel\Event;

use Doctrine\Common\EventSubscriber;
use SlimModel\Query\IncludeManyQuery;

/**
 * Hooks onto Model Events which is used to perform modifications on resultsets.
 *
 */
class IncludeManager implements EventSubscriber
{

    protected $model = false;


    public function __construct($model = false)
    {
        $this->model = $model;
    }

    public function getSubscribedEvents()
    {
        return [
            "postFetch"
        ];
    }


    public function postFetch(ModelEventArgs $data)
    {
        foreach($data->model->getManyIncludes() as $include_options) {
            $this->include_many($include_options);
        }
    }


    private function include_many($options)
    {
        $model_indices = [];
        foreach($this->model->result as $res) {
            $model_indices[]= $res[$options["key"]];
        }
        $query = new IncludeManyQuery($this->model->db, $options, $model_indices);
        $joins = $query->execute();
        $this->fill_data($joins, $options);
    }


    private function fill_data($joins, $options) {
        array_walk($this->model->result, function(&$value, $key, $params){
            $options = $params["options"];
            foreach($params["joins"] as $row) {
                if($row["lkey"]==$value[$options["key"]]) {
                    unset($row["lkey"]);
                    $value[$options["as"]][] = $row;
                }
            }
        },["joins"=>$joins,"options"=>$options]);
    }


}
