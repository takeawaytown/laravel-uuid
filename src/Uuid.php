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
   * Time (in 100ns steps) between the start of the UTC and Unix epochs
   *
   * @var int
   */
  const INTERVAL = 0x01b21dd213814000;

  /**
   * Clears all relevant bits of variant byte with AND
   *
   * @var int
   */
  const CLEAR_VAR = 63;

  /**
   * Clears all bits of version byte with AND
   * @var int
   */
  const CLEAR_VER = 15;

  /**
   * The RFC 4122 variant
   *
   * @var int
   */
  const VAR_RFC = 128;

  /**
   * Version constants
   * @var int
   */
  const VERSION_1 = 16;
  const VERSION_3 = 48;
  const VERSION_4 = 64;
  const VERSION_5 = 80;

  /**
   * @var string
   */
  const NS_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';

  const MD5 = 3;
  const SHA1 = 5;

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
   *
   * @param  integer $ver  The UUID version to use. Currently versions 1,3,4 and 5 are supported
   * @param  string  $node Name string required for version 3 or 5 UUIDs
   * @param  string  $ns   Binary namespace is required for version 3 or 5 UUIDs
   * @return UUID          Returns a formatted UUID
   * @throws Exception
   */
  public static function generate($ver = 1, $node = null, $ns = null) {
    /* Create a new UUID based on provided data. */
    switch ((int)$ver) {
      case 1:
        return new static(static::timeGenerator($node));
      case 2:
        throw new Exception('Version ' . $ver . ' is unsupported.');
      case 3:
        return new static(static::nameGenerator(static::MD5, $node, $ns));
      case 4:
        return new static(static::randomGenerator());
      case 5:
        return new static(static::nameGenerator(static::SHA1, $node, $ns));
      default:
        throw new Exception('Version ' . $ver . ' is unsupported.');
    }
  }

  /**
   * Generates a UUID.
   *
   * @param string $node Name string required for version 3 or 5 UUIDs
   * @return string
   */
  protected static function timeGenerator($node = null)
  {

    $time = static::timeString();

    // Reorder bytes to their proper locations in the UUID
    $uuid = $time[4] . $time[5] . $time[6] . $time[7] . $time[2] . $time[3] . $time[0] . $time[1];

    // Generate a random clock sequence
    $uuid .= random_bytes(2);

    // set variant
    $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);

    // set version
    $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | static::VERSION_1);

    // Set the final 'node' parameter, a MAC address
    if (!is_null($node)) {
        $node = static::makeBin($node, 6);
    }

    // If no node was provided or if the node was invalid,
    //  generate a random MAC address and set the multicast bit
    if (is_null($node)) {
        $node = static::randomBytes(6);
        $node[0] = pack("C", ord($node[0]) | 1);
    }

    $uuid .= $node;

    return $uuid;
  }

  /**
   * Insure that an input string is either binary or hexadecimal.
   * Returns binary representation, or null on failure.
   *
   * @param string $str
   * @param integer $len
   * @return string|null
   */
  protected static function makeBin($str, $len)
  {
    if ($str instanceof self) {
      return $str->bytes;
    }
    if (strlen($str) === $len) {
      return $str;
    } else {
      // strip URN scheme and namespace
      $str = preg_replace('/^urn:uuid:/is', '', $str);
    }
    // strip non-hex characters
    $str = preg_replace('/[^a-f0-9]/is', '', $str);

    if (strlen($str) !== ($len * 2)) {
      return null;
    } else {
      return pack("H*", $str);
    }
  }

  protected static function timeString() {

    // Get time since Gregorian calendar reform in 100ns intervals
    $time = microtime(1) * 10000000 + static::INTERVAL;

    // Convert to a string representation
    $time = sprintf("%F", $time);

    // Strip decimals
    preg_match("/^\d+/", $time, $time);

    // And now to a 64-bit binary representation
    $time = base_convert($time[0], 10, 16);
    $time = pack("H*", str_pad($time, 16, "0", STR_PAD_LEFT));

    return $time;
  }

  /**
   * Generates a version 3 or 5 UUID string which are derived from a name and
   * its namespace, in binary form.
   *
   * @param  integer $ver  The UUID version to use. Currently versions 1,3,4 and 5 are supported
   * @param  string  $node Name string required for version 3 or 5 UUIDs
   * @param  string  $ns   Binary namespace is required for version 3 or 5 UUIDs
   *
   * @return string A fully-formatted, version 3 or 5 UUID
   * @throws Exception
   */
  protected static function nameGenerator($ver, $node, $ns)
  {
      if (empty($node)) {
          throw new Exception('A name-string is required for Version 3 or 5 UUIDs.');
      }

      // if the namespace UUID isn't binary, make it so
      $ns = static::makeBin($ns, 16);
      if (is_null($ns)) {
          throw new Exception('A binary namespace is required for Version 3 or 5 UUIDs.');
      }

      $version = null;
      $uuid = null;

      switch ($ver) {
          case static::MD5:
              $version = static::VERSION_3;
              $uuid = md5($ns . $node, 1);
              break;
          case static::SHA1:
              $version = static::VERSION_5;
              $uuid = substr(sha1($ns . $node, 1), 0, 16);
              break;
          default:
              // no default really required here
      }

      // set variant
      $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);

      // set version
      $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | $version);

      return ($uuid);
  }

  /**
   * Randomness is returned as a string of bytes
   *
   * @param $bytes
   * @return string
   */
  public static function randomBytes($bytes)
  {
    return random_bytes($bytes);
  }

  /**
   * Generates a version 4 UUID, which are derived solely from random numbers.
   *
   * @return string
   */
  protected static function randomGenerator()
  {
    $uuid = static::randomBytes(16);
    $uuid[8] = chr(ord($uuid[8]) & static::CLEAR_VAR | static::VAR_RFC);
    $uuid[6] = chr(ord($uuid[6]) & static::CLEAR_VER | static::VERSION_4);

    return $uuid;
  }

}
