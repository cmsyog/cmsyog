<?php


class TestViewDetail extends YogView {

    function display() {


        $model = new ExampleModel();
        $model->insert(["title"=>"Hello World!"]);
        $model->insert(["title"=>"Hello World!!"]);


        $example=$model->all();
        var_dump($example);

    }
}