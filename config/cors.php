<?php

return [
  'paths'                    => ['api/*', 'sanctum/csrf-cookie'],
  'allowed_methods'          => ['*'],
  'allowed_origins'          => ['*'],  // untuk development, bisa ganti jadi ['http://localhost:3000'] misalnya
  'allowed_origins_patterns' => [],
  'allowed_headers'          => ['*'],
  'exposed_headers'          => [],
  'max_age'                  => 0,
  'supports_credentials'     => false,
];
