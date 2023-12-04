<?php
namespace ETL\Services;

use ETL\Services\SequenceService;
use ETL\Traits\HelperTrait as Helper;

/**
 * Class DataTransformerService
 *
 * This class is responsible for transforming data based on a specified layout configuration and writing the output to a file.
*/
class DataTransformerService extends TransformFieldsService {
  private \SimpleXMLElement $layoutXml;
  private array $recordset;
  private string $delimiter;
  private string $outputLine;
  private string $fieldValue;
  private $fileHandle;
  private string $EOL;

  use Helper;

  /**
   * Constructor for DataTransformerService.
   *
   * @param string $layoutFile The path to the layout XML file defining the data structure.
   * @param array $recordset The recordset containing data to be transformed.
  */
  public function __construct(string $layoutFile, array $recordset) {
    $this->layoutXml = simplexml_load_file($layoutFile);
    $this->recordset = $recordset;
    $this->delimiter = $this->layoutXml['delimitedby'] ?? '';
  }

  /**
   * Writes a section of transformed data to the output line based on the provided layout section and record.
   *
   * @param string $section The layout section to process.
   * @param array $record The record containing data for transformation.
   * @param int $sequence The sequence number for the current record.
   *
   * @return string The processed output line for the specified section.
   *
   * @throws \Exception If the specified XML section is not found.
  */
  private function writeSection(string $section, array $record, int $sequence): string {
    if (isset($this->layoutXml->$section)) {
      $this->outputLine = '';

      foreach ($this->layoutXml->$section->field as $field) {
        $this->fieldValue = $record[strtoupper((string)$field['name']) ?? ''] ?? (string)$field;

        $helpers = new TransformFieldsService($this->fieldValue, $field);
        $this->fieldValue = $helpers->formatFields($record, $sequence);
        
        $this->outputLine .= $this->fieldValue . $this->delimiter;
      }

      return rtrim($this->outputLine);
    }else{
      throw new \Exception('ERROR - XML [Section - '.$section.'] Not Found');
    }
  }

  /**
   * Transforms the data and writes it to the specified text file.
   *
   * @param string $txtFile The path to the text file where the transformed data will be written.
   *
   * @throws \Exception If the "lookfor" attribute is missing or does not match the expected values.
  */
  public function transformToFile(string $txtFile): void {
    $this->fileHandle = fopen($txtFile, 'w');

    $sequence = new SequenceService();

    foreach ($this->layoutXml->children() as $parent) {
      if(!$this->_isValidLookFor($parent)){
        throw new \Exception('ERROR - ['.$parent->getName().'] attribute "lookfor" not Found OR attribute on "lookfor" not Match '.print_r(['row', 'collection'], true));
        return;
      };
      
      if($parent->attributes()->lookfor->__toString() === 'row'){
        $this->outputLine = $this->writeSection($parent->getName(), $this->recordset[0],  $sequence->getCurrentValue() + 1);
        fwrite($this->fileHandle, $this->outputLine . PHP_EOL);

        $sequence->increment();
      }else if($parent->attributes()->lookfor->__toString() === 'collection'){
        foreach ($this->recordset as $index => $values) {
          $this->outputLine = $this->writeSection($parent->getName(), $values, (($index + 1) + $sequence->getCurrentValue()));
          fwrite($this->fileHandle, $this->outputLine . PHP_EOL);
        }

        $sequence->set(count($this->recordset) + 1);
      }
    }

    fclose($this->fileHandle);
  }
}