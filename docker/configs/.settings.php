<?php

return [
  'utf_mode' =>
  [
    'value' => true,
    'readonly' => true,
  ],
  'cache_flags' =>
  [
    'value' =>
    [
      'config_options' => 3600.0,
      'site_domain' => 3600.0,
    ],
    'readonly' => false,
  ],
  'cookies' =>
  [
    'value' =>
    [
      'secure' => false,
      'http_only' => true,
    ],
    'readonly' => false,
  ],
  'exception_handling' =>
  [
    'value' =>
    [
      'debug' => true,
      'handled_errors_types' => E_ALL,
      'exception_errors_types' => E_ALL,
      'ignore_silence' => false,
      'assertion_throws_exception' => true,
      'assertion_error_type' => E_ALL,
      'log' => NULL,
    ],
    'readonly' => false,
  ],
  'connections' =>
  [
    'value' =>
    [
      'default' =>
      [
        'className' => '\\Bitrix\\Main\\DB\\MysqliConnection',
        'host' => 'db',
        'database' => 'sitemanager',
        'login' => 'root',
        'password' => 'root',
        'options' => 2.0,
      ],
    ],
    'readonly' => true,
  ],
];
