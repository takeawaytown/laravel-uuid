<?php

namespace TakeawayTown\LaravelUuid;

use Exception;

/**
 * Class Uuid
 * @package TakeawayTown\Uuid
 **/
class Uuid
{
  /**
  * @param string $uuid
  * @throws Exception
  */
  protected function __construct($uuid) {
    if (!empty($uuid) && strlen($uuid) !== 16) {
      throw new Exception('Input must be a 128-bit integer.');
    }

    $this->bytes = $uuid;

    // Optimize the most common use
    $this->string = bin2hex(substr($uuid, 0, 4)) . "-" .
    bin2hex(substr($uuid, 4, 2)) . "-" .
    bin2hex(substr($uuid, 6, 2)) . "-" .
    bin2hex(substr($uuid, 8, 2)) . "-" .
    bin2hex(substr($uuid, 10, 6));
  }

  /**
   * Generates a UUID, with the verion being based on $ver
   * @param  integer $ver  The UUID version to use. Currently versions 1,3,4 and 5 are supported
   * @param  string  $node Name string required for Version 3 or 5 UUIDs
   * @param  string  $ns   Binary namespace is required for Version 3 or 5 UUIDs
   * @return UUID          Returns a formatted UUID
   * @throws Exception
   */
  public static function generate($ver = 1, $node = null, $ns = null) {
    /* Create a new UUID based on provided data. */
    switch ((int)$ver) {
      case 1:
        throw new Exception('Version 1 is unsupported.');
      case 2:
        throw new Exception('Version 2 is unsupported.');
      case 3:
        throw new Exception('Version 3 is unsupported.');
      case 4:
        throw new Exception('Version 4 is unsupported.');
      case 5:
        throw new Exception('Version 5 is unsupported.');
      default:
        throw new Exception('Selected version is invalid or unsupported.');
    }
  }
}
