<?php
use Core\Model\BaseModel;

class ExampleModel extends BaseModel
{
    public  $table = "example";
    public  $primary_key = "id";

    public function __construct()
    {
        parent::__construct();
    }
    public function setup()
    {
        $this->define("id", "integer", ["autoincrement" => true]);
        $this->define("title", "string", []);
    }

}