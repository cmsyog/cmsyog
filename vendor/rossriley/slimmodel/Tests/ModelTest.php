<?php
namespace SlimModel\Tests;

use Doctrine\DBAL\DriverManager;
use SlimModel\Tests\Fixtures\MockModel;

class ModelTest extends \PHPUnit_Framework_TestCase {

  public $db;

  public function setup() {
    $params = ['driver' => 'pdo_sqlite','memory' => true];
    $this->db = DriverManager::getConnection($params);
  }

  public function test_fails_without_connection() {
    $this->setExpectedException('SlimModel\ConnectionException');
    $model = new MockModel();
    $model->find(2);
  }

  public function test_fails_with_frozen_schema() {
    $this->setExpectedException('Doctrine\DBAL\DBALException');
    $model = new MockModel($this->db);
    $model->freeze = true;
    $res = $model->find(2);
  }

  public function test_creates_with_unfrozen_schema() {
    $model = new MockModel($this->db);
    $res = $model->find(2);
    $this->assertEquals($res, null);
  }

  public function test_schema_alterations() {
    $model = new MockModel($this->db);
    $result = $model->insert(["title"=>"Hello World"]);
    $this->assertEquals($result, 1);

    $model->freeze = true;
    $this->setExpectedException('Doctrine\DBAL\DBALException');
    $model->define("newcolumn", "string");
    $result2 = $model->update(1,["newcolumn"=>"testing"]);

    $model->freeze = false;
    $result3 = $model->update(1,["newcolumn"=>"testing"]);
    $this->assertEquals($result3, 1);
  }

  public function test_insert_and_find() {
    $model = new MockModel($this->db);
    $result = $model->insert(["title"=>"Hello World"]);
    $this->assertEquals($result, 1);
    $model2 = new MockModel($this->db);
    $res = $model2->find(1);
    $this->assertEquals($res["title"], "Hello World");
    $model2->update(1,["title"=>"Goodbye World"]);
  }

  public function test_update() {
    $model = new MockModel($this->db);
    $result = $model->insert(["title"=>"Hello World"]);
    $this->assertEquals($result, 1);
    $model2 = new MockModel($this->db);
    $result2 = $model2->update(1,["title"=>"Goodbye World"]);

    $model3 = new MockModel($this->db);
    $result3 = $model3->find(1);
    $this->assertEquals($result3["title"], "Goodbye World");
  }

  public function test_delete() {
    $model = new MockModel($this->db);
    $result = $model->insert(["title"=>"Hello World"]);
    $this->assertEquals($result, 1);
    $model2 = new MockModel($this->db);
    $result2 = $model2->delete(["id"=>1]);
    $this->assertEquals($result2, 1);

    $model3 = new MockModel($this->db);
    $result3 = $model3->find(1);
    $this->assertFalse($result3);
  }

}
