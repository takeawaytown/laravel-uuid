<?php

namespace TakeawayTown\Uuid;

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
    protected function __construct($uuid)
    {
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
}
