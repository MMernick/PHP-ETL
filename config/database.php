<?php
return [
  'DB_USERNAME'     => $_ENV['DB_USERNAME'] ?? 'default',
  'DB_PASSWORD'     => $_ENV['DB_PASSWORD'] ?? 'default',
  'DB_HOST'         => $_ENV['DB_HOST'] ?? 'default',
  'DB_PORT'         => $_ENV['DB_PORT'] ?? 'default',
  'DB_SERVICE_NAME' => $_ENV['DB_SERVICE_NAME'] ?? 'default'
];