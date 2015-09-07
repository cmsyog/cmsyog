<?php
namespace SlimModel\Event;

use Doctrine\Common\EventArgs;

/**
 * Slim Model Fetch Event Subscriber which is used to perform modifications on resultsets.
 *
 */
class ModelEventArgs extends EventArgs {

  public $model = false;
  public $model_data = false;

  public function __construct($model, $model_data = []) {
    $this->model = $model;
    $this->model_data = $model_data;
  }


}