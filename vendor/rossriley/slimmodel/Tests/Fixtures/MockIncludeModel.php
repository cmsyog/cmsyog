<?php
namespace SlimModel\Tests;

use SlimModel\Base;

class MockIncludeModel extends Base {
  protected $table        = "jointable";

  public function setup() {
    $this->define("id",   "integer",  ["autoincrement"=>true]);
    $this->define("title","string",   []);

  }

}
