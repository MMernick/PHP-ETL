<?php
ini_set('html_errors', '1'); 
ini_set('error_prepend_string', '<pre style="color: #333; font-face:monospace; font-size:8pt;">'); 
ini_set('error_append_string ', '</pre>'); 

require_once __DIR__ . '/vendor/autoload.php';

use Dotenv\Dotenv;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

use ETL\Controllers\ExampleController;

try{
  $route = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

  switch ($route) {
    case '/ETL/Example':
      $gestao = new ExampleController();
      $gestao->index();
      break;
    default:
      echo json_encode([
        'status' => 404, 
        'data' => 'Processo ['.$route.'] Não Encontrado'
      ]);
      return; 
  }

  echo json_encode([
    'status' => 200, 
    'data' => 'Processo ['.$route.'] Finalizado com Sucesso'
  ]);
}catch(\Exception $e){
  throw new \Exception($e);
}