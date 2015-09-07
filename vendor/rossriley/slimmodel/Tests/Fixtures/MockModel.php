<?php
namespace SlimModel\Tests\Fixtures;

use SlimModel\Base;

class MockModel extends Base {
  public $table        = "example";
  public $primary_key  = "id";

  public function setup() {
    $this->define("id",   "integer",  ["autoincrement"=>true]);
    $this->define("title","string",   []);

  }

}
