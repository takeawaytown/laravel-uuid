<?php
use TakeawayTown\LaravelUuid\Classes\Uuid;
use TakeawayTown\LaravelUuid\Providers\UuidServiceProvider;

class UuidTest extends \Orchestra\Testbench\TestCase
{
  /**
   * Set the package service provider.
   *
   * @param \Illuminate\Foundation\Application $app
   * @return array
   */
  protected function getPackageProviders($app)
  {
    return [UuidServiceProvider::class];
  }

  /**
   * Define environment setup.
   *
   * @param  \Illuminate\Foundation\Application  $app
   * @return void
   */
  protected function getEnvironmentSetUp($app)
  {
    $app['config']->set('uuid.default_version', 1);
    $app['config']->set('uuid.default_node', 'https://takeawaytown.co.uk');
  }

  // public function __construct() {
  //   $this->macAdress = Faker\Provider\Internet::macAddress();
  // }

  public function testVersionOneGeneration()
  {
    $uuid = Uuid::generate(NULL);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Classes\Uuid', $uuid);
  }

  public function testVersionThreeGeneration()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Classes\Uuid', $uuid);
  }

  public function testVersionFourGeneration()
  {
    $uuid = Uuid::generate(4);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Classes\Uuid', $uuid);
  }

  public function testVersionFiveGeneration()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertInstanceOf('TakeawayTown\LaravelUuid\Classes\Uuid', $uuid);
  }

  public function testAllZeroUuidEquals()
  {
      $uuid = Uuid::import(Uuid::NS_NIL);
      $this->assertEquals(Uuid::NS_NIL, (string) $uuid);
  }

  public function testAllZeroUuidInstance()
  {
      $uuid = Uuid::import(Uuid::NS_NIL);
      $this->assertInstanceOf('TakeawayTown\LaravelUuid\Classes\Uuid', $uuid);
  }

  public function testGenerationOfVersionOneViaRegex()
  {
    $uuid = Uuid::generate(1);
    $this->assertRegExp('~' . Uuid::REGEX . '~', (string)$uuid);
  }

  public function testGenerationOfVersionThreeViaRegex()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertRegExp('~' . Uuid::REGEX . '~', (string)$uuid);
  }

  public function testGenerationOfVersionFourViaRegex()
  {
    $uuid = Uuid::generate(4);
    $this->assertRegExp('~' . Uuid::REGEX . '~', (string)$uuid);
  }

  public function testGenerationOfVersionFiveViaRegex()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertRegExp('~' . Uuid::REGEX . '~', (string)$uuid);
  }

  public function testVersionOneValidatorString()
  {
    $uuid = Uuid::generate(1);
    $this->assertTrue(Uuid::validate($uuid->string));
  }

  public function testVersionOneValidatorBytes()
  {
    $uuid = Uuid::generate(1);
    $this->assertTrue(Uuid::validate($uuid->bytes));
  }

  public function testVersionOneValidatorUrn()
  {
    $uuid = Uuid::generate(1);
    $this->assertTrue(Uuid::validate($uuid->urn));
  }

  public function testVersionOneGeneratorValidator()
  {
    $this->assertTrue(Uuid::validate(Uuid::generate(1)));
  }

  public function testVersionThreeValidatorString()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertTrue(Uuid::validate($uuid->string));
  }

  public function testVersionThreeValidatorBytes()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertTrue(Uuid::validate($uuid->bytes));
  }

  public function testVersionThreeValidatorUrn()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertTrue(Uuid::validate($uuid->urn));
  }

  public function testVersionThreeGeneratorValidator()
  {
    $this->assertTrue(Uuid::validate(Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS)));
  }

  public function testVersionFourValidatorString()
  {
    $uuid = Uuid::generate(4);
    $this->assertTrue(Uuid::validate($uuid->string));
  }

  public function testVersionFourValidatorBytes()
  {
    $uuid = Uuid::generate(4);
    $this->assertTrue(Uuid::validate($uuid->bytes));
  }

  public function testVersionFourValidatorUrn()
  {
    $uuid = Uuid::generate(4);
    $this->assertTrue(Uuid::validate($uuid->urn));
  }

  public function testVersionFourGeneratorValidator()
  {
    $this->assertTrue(Uuid::validate(Uuid::generate(4)));
  }

  public function testVersionFiveValidatorString()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertTrue(Uuid::validate($uuid->string));
  }

  public function testVersionFiveValidatorBytes()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertTrue(Uuid::validate($uuid->bytes));
  }

  public function testVersionFiveValidatorUrn()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertTrue(Uuid::validate($uuid->urn));
  }

  public function testVersionFiveGeneratorValidator()
  {
    $this->assertTrue(Uuid::validate(Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS)));
  }

  public function testVersionOneCorrectVersionUuid()
  {
    $uuid = Uuid::generate(1);
    $this->assertEquals(1, $uuid->version);
  }

  public function testVersionThreeCorrectVersionUuid()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertEquals(3, $uuid->version);
  }

  public function testVersionFourCorrectVersionUuid()
  {
    $uuid = Uuid::generate(4);
    $this->assertEquals(4, $uuid->version);
  }

  public function testVersionFiveCorrectVersionUuid()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertEquals(5, $uuid->version);
  }

  public function testVersionOneCorrectVariantUuid()
  {
    $uuid = Uuid::generate(1);
    $this->assertEquals(1, $uuid->variant);
  }

  public function testVersionThreeCorrectVariantUuid()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertEquals(1, $uuid->variant);
  }

  public function testVersionFourCorrectVariantUuid()
  {
    $uuid = Uuid::generate(4);
    $this->assertEquals(1, $uuid->variant);
  }

  public function testVersionFiveCorrectVariantUuid()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $this->assertEquals(1, $uuid->variant);
  }

  public function testCorrectNodeOfGeneratedVersionOneUuid()
  {
    $macAdress = Faker\Provider\Internet::macAddress();
    $uuid = Uuid::generate(1, $macAdress);
    $this->assertEquals(strtolower(str_replace(':', '', $macAdress)), $uuid->node);
  }

  public function testNullNodeOfGeneratedVersionThreeUuid()
  {
    $macAdress = Faker\Provider\Internet::macAddress();
    $uuid = Uuid::generate(3, $macAdress, Uuid::NS_DNS);
    $this->assertNull($uuid->node);
    $uuidThree = Uuid::generate(4, $macAdress);
    $this->assertNull($uuidThree->node);
    $uuidThree = Uuid::generate(5, $macAdress, Uuid::NS_DNS);
    $this->assertNull($uuidThree->node);
  }

  public function testNullNodeOfGeneratedVersionFourUuid()
  {
    $macAdress = Faker\Provider\Internet::macAddress();
    $uuid = Uuid::generate(4, $macAdress);
    $this->assertNull($uuid->node);
  }

  public function testNullNodeOfGeneratedVersionFiveUuid()
  {
    $macAdress = Faker\Provider\Internet::macAddress();
    $uuid = Uuid::generate(5, $macAdress, Uuid::NS_DNS);
    $this->assertNull($uuid->node);
  }

  public function testCorrectVersionOfImportedVersionOneUuid()
  {
    $uuid = Uuid::generate(1);
    $imported = Uuid::import((string) $uuid);
    $this->assertEquals($uuid->version, $imported->version);
  }

  public function testCorrectVersionOfImportedVersionThreeUuid()
  {
    $uuid = Uuid::generate(3, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $imported = Uuid::import((string) $uuid);
    $this->assertEquals($uuid->version, $imported->version);
  }

  public function testCorrectVersionOfImportedVersionFourUuid()
  {
    $uuid = Uuid::generate(4);
    $imported = Uuid::import((string) $uuid);
    $this->assertEquals($uuid->version, $imported->version);
  }

  public function testCorrectVersionOfImportedVersionFiveUuid()
  {
    $uuid = Uuid::generate(5, Config::get('uuid.default_node'), Uuid::NS_DNS);
    $imported = Uuid::import((string) $uuid);
    $this->assertEquals($uuid->version, $imported->version);
  }

}
