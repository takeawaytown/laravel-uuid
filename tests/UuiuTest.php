<?php

use PHPUnit\Framework\TestCase;
use TakeawayTown\Uuid\Uuid;

class UuidTest extends TestCase
{
    public function testStaticGeneration()
    {
        $uuid = Uuid::generate(1);
        $this->assertInstanceOf('TakeawayTown\Uuid\Uuid', $uuid);
    }
}
