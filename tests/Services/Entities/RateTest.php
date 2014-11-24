<?php namespace Arcanedev\Currency\Tests\Services\Entities;

use Arcanedev\Currency\Services\Entities\Currency;
use Arcanedev\Currency\Services\Entities\Rate;

class RateTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Rate */
    protected $rate;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    protected function setUp()
    {
        parent::setUp();

        $this->rate = new Rate;
    }

    protected function tearDown()
    {
        unset($this->rate);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function testCanBeInstantiate()
    {
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\Rate', $this->rate);
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\Currency', $this->rate->getFrom());
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\Currency', $this->rate->getTo());
    }

    public function testCanSetAndGetCurrenciesOne()
    {
        $this->assertEquals('USD', $this->rate->setIsoFrom('USD')->getFrom()->getIso());
        $this->assertEquals('EUR', $this->rate->setIsoTo('EUR')->getTo()->getIso());
    }

    public function testCanSetAndGetCurrenciesTwo()
    {
        $from = Currency::make('USD');
        $this->assertEquals($from->getIso(), $this->rate->setFrom($from)->getFrom()->getIso());

        $to = Currency::make('EUR');
        $this->assertEquals($to->getIso(), $this->rate->setTo($to)->getTo()->getIso());
    }

    public function testCanCheckCurrencies()
    {
        $this->rate->setIsoFrom('USD');
        $this->rate->setIsoTo('EUR');

        $this->assertTrue($this->rate->isDifferentCurrencies());
        $this->assertFalse($this->rate->isSameCurrencies());

        $this->rate->setIsoTo('USD');
        $this->assertFalse($this->rate->isDifferentCurrencies());
        $this->assertTrue($this->rate->isSameCurrencies());
    }

    public function testCanCalculateAndFormatCurrency()
    {
        $this->rate->setIsoFrom('USD');
        $this->rate->setIsoTo('EUR');
        $this->rate->setExchangeRate(0.8051);

        $this->assertEquals('0.81', $this->rate->calculate());
        $this->assertEquals('241.53', $this->rate->calculate(300));
        $this->assertEquals('€ 0,81', $this->rate->calculate(1, true));
        $this->assertEquals('€ 805,09', $this->rate->calculate(999.99, true));
    }
}
