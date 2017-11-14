<?php

use PHPUnit\Framework\TestCase;
use TakeawayTown\LaravelUuid\Uuid;

class UuidTest extends TestCase
{
  public function testVersionOneGeneration()
  {
    $uuid = Uuid::generate(1);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Uuid', $uuid);
  }

  public function testVersionThreeGeneration()
  {
    $uuid = Uuid::generate(3, 'takeawaytown.co.uk', Uuid::NS_DNS);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Uuid', $uuid);
  }

  public function testVersionFourGeneration()
  {
    $uuid = Uuid::generate(4);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Uuid', $uuid);
  }

  public function testVersionFiveGeneration()
  {
    $uuid = Uuid::generate(5, 'takeawaytown.co.uk', Uuid::NS_DNS);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Uuid', $uuid);
  }
}
