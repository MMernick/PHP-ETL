<?php
namespace ETL\Services;

use ETL\Services\DBConfigService;

/**
 * Class DBConnectionService
 *
 * This class provides a service for managing Oracle database connections in a connection pool.
*/
class DBConnectionService extends DBConfigService {
  private $pool = [];
  private $poolSize = 20;
  private $username;
  private $password;
  private $host;
  private $port;
  private $serviceName;

  /**
   * Constructor for DBConnectionService.
   *
   * Retrieves database connection parameters from the parent class (DBConfigService) and initializes instance variables.
  */
  public function __construct() {
    parent::__construct();

    $this->username = parent::get('DB_USERNAME');
    $this->password = parent::get('DB_PASSWORD');
    $this->host = parent::get('DB_HOST');
    $this->port = parent::get('DB_PORT');
    $this->serviceName = parent::get('DB_SERVICE_NAME');
  }

  /**
   * Retrieves a database connection from the connection pool or creates a new connection if the pool is not full.
   *
   * @return resource The Oracle database connection resource.
   *
   * @throws \Exception If there is an issue creating or retrieving a database connection.
  */
  public function getConnection() {
    try{
      if (count($this->pool) < $this->poolSize) {
        $connection = $this->createConnection();

        $this->pool[] = $connection;
      } else {
        $connection = array_shift($this->pool);
      }

      return $connection;
    }catch(\Exception $e){
      throw $e;
    }
  }

  /**
   * Releases a database connection back to the connection pool.
   *
   * @param resource $connection The Oracle database connection resource to release.
   *
   * @throws \Exception If there is an issue releasing the database connection.
  */
  public function releaseConnection($connection) {
    try{
      if (count($this->pool) < $this->poolSize) {
        $this->pool[] = $connection;
      } else {
        oci_close($connection);
      }
    }catch(\Exception $e){
      throw $e;
    }
  }

  /**
   * Creates a new Oracle database connection.
   *
   * @return resource The Oracle database connection resource.
   *
   * @throws \Exception If there is an issue creating the database connection.
  */
  private function createConnection() {
    try{
      $connection = oci_connect($this->username, $this->password, "//{$this->host}:{$this->port}/{$this->serviceName}");

      if (!$connection) {
        $error = oci_error();
        throw new \Exception($error['message']);
      }

      return $connection;
    }catch(\Exception $e){
      throw $e;
    }
  }

  /**
   * Executes a query and fetches the result as an associative array.
   *
   * @param string $query The SQL query to execute.
   *
   * @return array The result set as an associative array.
   *
   * @throws \Exception If there is an issue executing the query or fetching the result.
  */
  public function fetchAssoc(String $query): Array {
    try{
      $connection = $this->getConnection();
      $statement = oci_parse($connection, $query);
      
      oci_execute($statement);

      $result = [];
      while ($row = oci_fetch_assoc($statement)) {
        $result[] = $row;
      }

      oci_free_statement($statement);
      $this->releaseConnection($connection);

      return $result;
    }catch(\Exception $e){
      throw $e;
    }
  }
}