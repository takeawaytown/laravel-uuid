<?php

use PHPUnit\Framework\TestCase;
use TakeawayTown\LaravelUuid\Uuid;

class UuidTest extends TestCase
{
    public function testStaticGeneration()
    {
        $uuid = Uuid::generate(1);
        $this->assertInstanceOf('TakeawayTown\LaravelUuid\Uuid', $uuid);
    }
}
