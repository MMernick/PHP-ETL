<?php
namespace ETL\Services;

class LayoutConfigService {
  private $layout;

  /**
   * Constructor for LayoutConfigService.
   * Loads layout configurations from the 'layouts.php' file during object instantiation.
  */
  public function __construct() {
    $this->layout = require __DIR__ . '/../config/layouts.php';
  }

  /**
   * Retrieves a specific layout configuration based on the provided key.
   *
   * @param string $key The key representing the desired layout configuration.
   * @return array The layout configuration associated with the provided key.
   * @throws \Exception If the specified key is not found in the layout configurations.
  */
  public function get(String $key): array {
    if(isset($this->layout[$key])){
      return $this->layout[$key];
    }
    
    return throw new \Exception('ERROR - The ['.$key.'] Not Found in '.print_r($this->layout, true));
  }
}