<?php
namespace SlimModel\Event;

use Doctrine\DBAL\Schema\Schema;
use Doctrine\Common\EventSubscriber;

/**
 * Listens in to a model, on Schema Exception, it attempts to auto-migrate the database.
 * It then returns control to the model to attempt a resume.
 *
 * Note, in theory this happens in development only, activation is triggered by the `freeze` attribute on the model.
 *
 */
class MigrateManager implements EventSubscriber {

    protected $model;
    protected $event_data;


    public function __construct($model = false)
    {
        $this->model = $model;
    }


    public function getSubscribedEvents()
    {
        return [
            "onSchemaException"
        ];
    }


    public function onSchemaException(ModelEventArgs $event)
    {
        if($event->model->freeze) return true;
        else $this->migrate($event->model);
    }


    public function migrate($model)
    {

        /* Initialise the schema variables from the connection */
        $platform = $model->db->getDatabasePlatform();
        $sm = $model->db->getSchemaManager();
        $original_schema = $sm->createSchema();
        $schema = new Schema();


        /* Now use the Schema object to create a table */
        if(!$schema->hasTable($model->table)) $table = $schema->createTable($model->table);
        else $table = $schema->getTable($model->table);

        foreach($model->columns as $name=>$options) {
            $table->addColumn($name,   $options["type"],  $options["options"]);
        }

        $table->setPrimaryKey(array($model->primary_key));

        /* We now have a current Schema object, which is compared against the original to create migration sql */
        $queries = $schema->getMigrateFromSql($original_schema, $platform);

        /* It produces a series of queries, which we run in sequence. The database should be up to date now. */
        foreach ($queries as $query) {
            $model->db->query($query);
        }
    }


}