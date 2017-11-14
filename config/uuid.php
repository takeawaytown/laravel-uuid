<?php

return [
  /**
   * Default UUID column
   *
   * Every model that uses the UUID for scope, should have a UUID column to
   * reference the UUID value.
   */
  'uuid_column' => 'uuid',
  'default_node' => env('APP_URL', 'https://takeawaytown.co.uk'),
  'default_version' => env('UUID_VERSION', 1),
];
