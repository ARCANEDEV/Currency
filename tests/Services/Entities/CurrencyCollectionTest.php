<?php namespace Arcanedev\Currency\Tests\Services\Entities;

use Arcanedev\Currency\Services\Entities\CurrencyCollection;

class CurrencyCollectionTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var CurrencyCollection */
    protected $currencies;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    protected function setUp()
    {
        parent::setUp();

        $this->currencies = new CurrencyCollection;
    }

    protected function tearDown()
    {
        unset($this->currencies);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function testCanBeInstantiate()
    {
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\CurrencyCollection', $this->currencies);

        // Loaded From config file
        $this->assertCount(164, $this->currencies);
    }

    public function testIfCurrencyExistsInCollection()
    {
        $this->assertTrue($this->currencies->has('USD'));
        $this->assertTrue($this->currencies->has('EUR'));
        $this->assertTrue($this->currencies->has('CAD'));
        $this->assertTrue($this->currencies->has('EUR'));

        $this->assertFalse($this->currencies->has('LOL'));
    }

    public function testCanGetCurrencyFromCollection()
    {
        $currency = $this->currencies->get('USD');
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\Currency', $currency);
        $this->assertEquals('USD', $currency->getIso());
        $this->assertEquals('United States Dollar', $currency->getName());
    }
}
