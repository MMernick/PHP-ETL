<?php
namespace ETL\Services;

use ETL\Services\DataTransformerService;
use ETL\Services\LayoutConfigService;
use ETL\Traits\HelperTrait as Helper;

/**
 * ETLProcessorService class extends LayoutConfigService and provides functionality for ETL processing.
*/
class ETLProcessorService extends LayoutConfigService {
  private $layoutPath;
  private $outputPath;
  private $filename;
  private $sourceData;

  use Helper;

  /**
   * Constructor for ETLProcessorService.
   *
   * @param array $sourceData An array of source data for the ETL process.
   * @param string|null $layout Optional layout configuration key.
  */
  public function __construct(Array $sourceData = [], String $layout = null){
    parent::__construct();

    $layoutConfig = parent::get($layout);

    $this->layoutPath = $layoutConfig['layoutPath'];
    $this->outputPath = $this->_hasWildCard($layoutConfig['outputPath'], $sourceData[0]);
    $this->filename = $this->_hasWildCard($layoutConfig['outFileName'], $sourceData[0]);

    $this->sourceData = $sourceData;
  }

  /**
   * Executes the data transformation service and writes the output to a file.
  */
  private function runTransformerService(): void{
    if (!file_exists($this->outputPath)) {
      mkdir($this->outputPath, 0777, true);
    }

    $filename = $this->outputPath.'/'.$this->filename;

    $transformer = new DataTransformerService($this->layoutPath, $this->sourceData);
    $transformer->transformToFile($filename);
  }

  /**
   * Main method to execute the ETL process.
  */
  public function run(): void{
    $this->runTransformerService();
  }
}