<?php
namespace ETL\Controllers;

use ETL\Services\DBConnectionService as Connection;
use ETL\Services\ETLProcessorService as ETL;

use ETL\Traits\HelperTrait as Helper;

class ExampleController {
  private $connection;
  private $sourceData;
  private $groupByData;

  use Helper;

  public function __construct(){
    $this->connection = new Connection;
  }

  public function extract(): Object{
    $query = 'SELECT * FROM EXAMPLE_STAGE';
    $this->sourceData = $this->connection->fetchAssoc($query);

    return $this;
  }

  public function transform(): Object{
    $this->groupByData = $this->_groupBy($this->sourceData, 'UF');

    return $this;
  }

  public function load(): void{
    foreach($this->groupByData as $groupedBy){
      $etlProcessor = new ETL($groupedBy, 'EXAMPLE');
      $etlProcessor->run();
    }
  }

  public function index(){
    $this 
      ->extract()
      ->transform()
      ->load();
  }
}