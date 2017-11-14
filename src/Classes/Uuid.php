<?php

namespace TakeawayTown\LaravelUuid\Classes;

use Exception;
use Illuminate\Support\Facades\Config;
use TakeawayTown\LaravelUuid\Providers\UuidServiceProvider;

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
     * Namespace UUIDs
     * @var string
     */
    const NS_DNS = '6ba7b810-9dad-11d1-80b4-00c04fd430c8';
    const NS_URL = '6ba7b811-9dad-11d1-80b4-00c04fd430c8';
    const NS_OID = '6ba7b812-9dad-11d1-80b4-00c04fd430c8';
    const NS_X500 = '6ba7b814-9dad-11d1-80b4-00c04fd430c8';
    const NS_NIL = '00000000-0000-0000-0000-000000000000';

    const MD5 = 3;
    const SHA1 = 5;

    /**
     * Regular expression for validation of UUID.
     */
    const REGEX = '^[0-9A-Fa-f]{8}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{4}-[0-9A-Fa-f]{12}$';

    /**
     * Variant reserved for future use
     * @var int
     */
    const VAR_RES = 224;

    /**
     * Microsoft UUID variant
     * @var int
     */
    const VAR_MS = 192;

    /**
    * @param string $uuid
    * @throws Exception
    */
    protected function __construct($uuid)
    {
        if (!empty($uuid) && strlen($uuid) !== 16) {
            throw new InvalidArgumentException('Input must be a 128-bit integer.');
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
    public static function generate($ver = null, $node = null, $namespace = null)
    {
        $ver = $ver !== null ? $ver : config('uuid.default_version');
        $node = $node !== null ? $node : config('uuid.default_node');
        /* Create a new UUID based on provided data. */
        switch ((int)$ver) {
            case 1:
                return new static(static::timeGenerator($node));
            case 2:
                throw new UnexpectedValueException('Version ' . $ver . ' is unsupported.');
            case 3:
                return new static(static::nameGenerator(static::MD5, $node, $namespace));
            case 4:
                return new static(static::randomGenerator());
            case 5:
                return new static(static::nameGenerator(static::SHA1, $node, $namespace));
            default:
                throw new UnexpectedValueException('Version ' . $ver . ' is unsupported.');
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
            $node = static::makeBinary($node, 6);
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
    protected static function makeBinary($str, $len)
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

    protected static function timeString()
    {

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
    protected static function nameGenerator($ver, $node, $namespace)
    {
        if (empty($node)) {
            throw new InvalidArgumentException('A name-string is required for Version 3 or 5 UUIDs.');
        }

        // if the namespace UUID isn't binary, make it so
        $namespace = static::makeBinary($namespace, 16);
        if (is_null($namespace)) {
            throw new InvalidArgumentException('A binary namespace is required for Version 3 or 5 UUIDs.');
        }

        $version = null;
        $uuid = null;

        switch ($ver) {
            case static::MD5:
                $version = static::VERSION_3;
                $uuid = md5($namespace . $node, 1);
                break;
            case static::SHA1:
                $version = static::VERSION_5;
                $uuid = substr(sha1($namespace . $node, 1), 0, 16);
                break;
            default:
                $version = static::VERSION_3;
                $uuid = md5($namespace . $node, 1);
                break;
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

    /**
     * Import a pre-existing UUID
     *
     * @param string $uuid
     * @return Uuid
     */
    public static function import($uuid)
    {
        return new static(static::makeBinary($uuid, 16));
    }

    /**
     * Import and validate an UUID
     *
     * @param Uuid|string $uuid
     *
     * @return boolean
     */
    public static function validate($uuid)
    {
        return (boolean) preg_match('~' . static::REGEX . '~', static::import($uuid)->string);
    }

    /**
     * @param string $var
     * @return string|string|number|number|number|number|number|NULL|number|NULL|NULL
     */
    public function __get($var)
    {
        switch ($var) {
            case "bytes":
                return $this->bytes;
                // no break
            case "hex":
                return bin2hex($this->bytes);
                // no break
            case "node":
                if (ord($this->bytes[6]) >> 4 == 1) {
                    return bin2hex(substr($this->bytes, 10));
                } else {
                    return null;
                }
                // no break
            case "string":
                return (string) $this->__toString();
                // no break
            case "time":
                if (ord($this->bytes[6]) >> 4 == 1) {
                    // Restore contiguous big-endian byte order
                    $time = bin2hex($this->bytes[6] . $this->bytes[7] . $this->bytes[4] . $this->bytes[5] .
                    $this->bytes[0] . $this->bytes[1] . $this->bytes[2] . $this->bytes[3]);
                    // Clear version flag
                    $time[0] = "0";

                    // Do some reverse arithmetic to get a Unix timestamp
                    return (int) (hexdec($time) - static::INTERVAL) / 10000000;
                } else {
                    return null;
                }
                // no break
            case "urn":
                return "urn:uuid:" . $this->__toString();
                // no break
            case "variant":
                $byte = ord($this->bytes[8]);
                if ($byte >= static::VAR_RES) {
                    return (int) 3;
                } elseif ($byte >= static::VAR_MS) {
                    return (int) 2;
                } elseif ($byte >= static::VAR_RFC) {
                    return (int) 1;
                } else {
                    return (int) 0;
                }
                // no break
            case "version":
                return (int) ord($this->bytes[6]) >> 4;
                // no break
            default:
                return;
                // no break
        }
    }

    /**
     * Return the UUID
     *
     * @return string
     */
    public function __toString()
    {
        return $this->string;
    }
}
