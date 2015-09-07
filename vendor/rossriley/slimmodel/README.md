## Slim-Model

### A lighter ORM style wrapper for Doctrine DBAL

Slim-Model is a small library that wraps Doctrine DBAL allowing fast prototypes , whilst keeping
many of the features of the original gargantuan library.

This package delegates all the heavy lifting to Doctrine/DBAL and throws out the concept of complex objects for models
and fields. Instead, everything returned is a plain PHP object.

### What Slim-Model Can Do

#### Automatic Databaase Syncing

In development mode, Slim-model can automatically sync the database to columns defined in the `define()` method.
Model objects can also be set to frozen when this behaviour is no longer required.

As before the sync will be triggered automatically whenever a query cannot run because of a schema exception.

## How to install

Via composer, just add the following to any project's `composer.json` file.

    "require": {
      "rossriley/slimmodel": "1.0.*@dev"
    }

Then define your models to extend the Base class, like below..

    <?php
    use SlimModel\Base;

    class Example extends Base {........}


## Run tests.

Run the unit tests to ensure compliance with API. Clone the repo then run...

    composer update

This will put the necessary dependencies in the vendor directory.

Then ensure that phpunit is in your path. If not you'll need to install it.

Go into the root of the project directory and run:

    phpunit

Hopefully you'll see a nice green bar.

## Getting Started and Basic Usage


#### Model construction, passing in a connection

First up you need to create a new model that extends `SlimModel\Base`

It will look something like the below...

    ....
    use SlimModel\Base;

    class Example extends Base {
      protected $table        = "example";
      protected $primary_key  = "id";

      public function setup() {
        $this->define("id",   "integer",  ["autoincrement"=>true]);
        $this->define("title","string",   []);
      }

    }

Note that there's no automatic fields in this package. You need to define a primary key.

Now that you have a model, we can get to work. The only assumption is that you have a DBALConnection object ready to pass in.
In reality you will probably want to delgate object creation to your application to avoid continually passing around the connection object.
For this example and to show that the module works without any tight coupling we're going to pass in the connection on construct.

#### Inserts

Inserts to the database just take an array of properties.

    $model = new Example($db_connection);
    $result = $model->insert(["title"=>"Hello World"]);

#### Updates

Updates take a primary key and some data to update.

    $model = new Example($db_connection);
    $result = $model->update(1, ["title"=>"Hello Again"]);

#### Deleting Rows

Select a row by properties and delete...

    $model = new Example($db_connection);
    $result = $model->delete(["id"=>1]);

#### Finding Rows

This is just a quick helper to fetch a database row by primary key; for example:

    $model = new Example($db_connection);
    $result = $model->find(1]);
    // returns ["id"=>1, "title"=>"Hello Again"]

To do any more advanced queries, use the query builder that ships with DBAL. You can get a builder object by doing the following:

    $queryBuilder = $db_connection->createQueryBuilder();

And then make a query like below:

      $queryBuilder
          ->select('u.id', 'u.name')
          ->from('users', 'u')
          ->where('u.email = ?')
          ->setParameter(1, $userInputEmail)
      ;

### And What it Can't Do

Magic.

Data is returned as simple data. If you need anything more complicated then write helper methods to transform.

Joins and advanced filters can be created by using the functionality in Querybuilder.


### Notes on field types for defines.

All Doctrine DBAL types are available including guid, so check the api docs for details at:

http://www.doctrine-project.org/api/dbal/2.4/namespace-Doctrine.DBAL.Types.html



## Including join data

Slim model can eager load and include joined data by way of the `includes` functionality. *NOTE:* This is primarily for use as a helper when your use case is simple. If you need to do anything complex, such as ordering on the join table or filtering of the join query this is not supported, but in these cases it's best to used the querybuilder to optimise your performance.

### An example, joining images to content

First we need to use our content model to define an include. This will look like the following.

    use SlimModel\Base;


    class Content extends Base {
      protected $table = "wildfire_content";


      public function setup() {
        $this->add_include("many", ["table"=>"media","as"=>"images"]);
      }
    }


