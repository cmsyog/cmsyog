<?php
namespace SlimModel\Tests;

use Doctrine\DBAL\DriverManager;
use SlimModel\Query\IncludeManyQuery;
use SlimModel\Tests\Fixtures\MockModel;

class IncludeManyQueryTest extends \PHPUnit_Framework_TestCase
{

    public $db;

    public function setup()
    {
        $params = ['driver' => 'pdo_sqlite','memory' => true];
        $this->db = DriverManager::getConnection($params);
    }

    public function testConstruct() {
        $query = new IncludeManyQuery($this->db, ["join"=>"test"], [1]);
        $this->assertEquals("test", $query->options["join"]);
        $this->assertEquals([1], $query->key_filter);
    }

    public function testJoinQuery()
    {
        $model = new MockModel($this->db);
        $model->add_include("many", ["table"=>"jointable"]);

        foreach($model->getManyIncludes() as $options) {
            $query = new IncludeManyQuery($this->db, $options);
            $query->build();
            $this->assertEquals(
                $query->query->getSQL(),
                "SELECT l.id as lkey, r.* FROM example l LEFT JOIN example_jointable j ON j.example_id = l.id LEFT JOIN jointable r ON r.id = j.jointable_id WHERE r.id IS NOT NULL"
            );
        }

    }

    public function testKeyFilter()
    {
        $model = new MockModel($this->db);
        $model->add_include("many", ["table"=>"jointable"]);

        foreach($model->getManyIncludes() as $options) {
            $query = new IncludeManyQuery($this->db, $options, [1]);
            $query->build();
            $this->assertEquals(
                $query->query->getSQL(),
                "SELECT l.id as lkey, r.* FROM example l LEFT JOIN example_jointable j ON j.example_id = l.id LEFT JOIN jointable r ON r.id = j.jointable_id WHERE (r.id IS NOT NULL) AND (l.id IN (1))"
            );
        }

    }

}