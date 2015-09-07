<?php
namespace SlimModel\Tests;

use Doctrine\DBAL\DriverManager;
use SlimModel\Event\IncludeManager;
use SlimModel\Query\IncludeManyQuery;
use SlimModel\Tests\Fixtures\MockModel;

class ModelIncludeTest extends \PHPUnit_Framework_TestCase
{

    public $db;

    public function setup()
    {
        $params = ['driver' => 'pdo_sqlite','memory' => true];
        $this->db = DriverManager::getConnection($params);
    }

    public function testInitialise()
    {
        $manager = new IncludeManager;
        $this->assertEquals(["postFetch"], $manager->getSubscribedEvents());
    }


    public function testIncludeSetup()
    {
        $model = new MockModel($this->db);
        $model->add_include("many", ["table"=>"jointable"]);
        $this->assertEquals(count($model->includes["many"]),1);

        // Test all default values are setup correctly
        $test = $model->includes["many"][0];
        $this->assertEquals($test["origin"],        "example");
        $this->assertEquals($test["table"],         "jointable");
        $this->assertEquals($test["join"],          "example_jointable");
        $this->assertEquals($test["key"],           "id");
        $this->assertEquals($test["as"],            "jointable");
        $this->assertEquals($test["join_key"],      "id");
        $this->assertEquals($test["join_left_key"], "example_id");
        $this->assertEquals($test["join_right_key"],"jointable_id");

    }




    public function test_join_query_with_filters()
    {
        $model = new MockModel($this->db);
        $model->add_include("many", ["table"=>"jointable"]);

        foreach($model->getManyIncludes() as $name=>$options) {
            $query = new IncludeManyQuery($this->db, $options, [1,2,3]);
            $query->build();
            $this->assertEquals(
                $query->query->getSQL(),
                "SELECT l.id as lkey, r.* FROM example l LEFT JOIN example_jointable j ON j.example_id = l.id LEFT JOIN jointable r ON r.id = j.jointable_id WHERE (r.id IS NOT NULL) AND (l.id IN (1, 2, 3))"
            );
        }


    }


}