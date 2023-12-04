<?php
namespace ETL\Services;

/**
 * Class DBConfigService
 *
 * This class provides a service for retrieving database configuration parameters.
*/
class DBConfigService {
  private $config;

  /**
   * Constructor for DBConfigService.
   *
   * Loads database configuration parameters from the 'database.php' file during object instantiation.
  */
  public function __construct() {
    $this->config = require_once __DIR__ . '/../config/database.php';
  }

  /**
   * Retrieves a specific database configuration parameter based on the provided key.
   *
   * @param string $key The key representing the desired database configuration parameter.
   * @param mixed $default The default value to return if the specified key is not found.
   *
   * @return mixed The database configuration parameter associated with the provided key, or the default value if not found.
  */
  public function get($key, $default = null) {
    return isset($this->config[$key]) ? $this->config[$key] : $default;
  }
}