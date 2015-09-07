<?php
namespace SlimModel\Tests;

use Doctrine\DBAL\DriverManager;
use SlimModel\Event\IncludeManager;
use SlimModel\Tests\Fixtures\MockModel;


class ModelEventsTest extends \PHPUnit_Framework_TestCase {

  public $db;

  public function setup() {
    $params = ['driver' => 'pdo_sqlite','memory' => true];
    $this->db = DriverManager::getConnection($params);

  }

  public function test_include_manager_trigger() {
    $model = new MockModel($this->db);
    $model->add_include("many", ["table"=>"jointable"]);

    $mock_includer = $this->getMockBuilder('SlimModel\Event\IncludeManager')
                          ->setMethods(["postFetch"])
                          ->getMock();

    $mock_includer->expects($this->once())
                  ->method('postFetch')
                  ->with($this->isInstanceOf("SlimModel\Event\ModelEventArgs"));

    $model->includeManager = $mock_includer;
    $model->find(1);

  }

  public function test_migrate_manager_trigger() {
    $this->setExpectedException('Doctrine\DBAL\DBALException');

    $model = new MockModel($this->db);

    $mock_migrator = $this->getMockBuilder('SlimModel\Event\MigrateManager')
                          ->setMethods(["onSchemaException"])
                          ->getMock();

    $mock_migrator->expects($this->once())
                  ->method('onSchemaException')
                  ->with($this->isInstanceOf("SlimModel\Event\ModelEventArgs")) ;

    $model->migrateManager = $mock_migrator;
    $model->insert(["title"=>"Hello World"]);

  }

  public function test_post_fetch_invoked() {
    $model = new MockModel($this->db);
    $mock_subscriber = $this->getMockBuilder('Doctrine\Common\EventSubscriber')
                          ->setMethods(["preFetch","getSubscribedEvents"])
                          ->getMock();
    $mock_subscriber->expects($this->any())
                    ->method('getSubscribedEvents')
                    ->will($this->returnCallback(function(){return ["preFetch"];}));

    $mock_subscriber->expects($this->once())
                    ->method('preFetch');

    $model->events->addEventSubscriber($mock_subscriber);
    $model->find(1);
  }


}