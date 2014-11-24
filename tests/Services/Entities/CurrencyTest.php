<?php namespace Arcanedev\Currency\Tests\Services\Entities;

use Arcanedev\Currency\Services\Entities\Currency;

class CurrencyTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Currency */
    protected $currency;

    /* ------------------------------------------------------------------------------------------------
     |  Main Function
     | ------------------------------------------------------------------------------------------------
     */
    protected function setUp()
    {
        parent::setUp();

        $this->currency = new Currency;
    }

    protected function tearDown()
    {
        unset($this->currency);
    }

    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * @test
     */
    public function testCanBeInstantiate()
    {
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\Currency', $this->currency);
    }

    /**
     * @test
     */
    public function testCanLoadCurrencyFromIso()
    {
        $this->currency->load('USD');
        $this->assertEquals('USD', $this->currency->getIso());
        $this->assertEquals('United States Dollar', $this->currency->getName());

        $this->currency->load('EUR');
        $this->assertEquals('EUR', $this->currency->getIso());
        $this->assertEquals('Euro', $this->currency->getName());

        // Static Function
        $this->currency = Currency::make('CAD');
        $this->assertEquals('CAD', $this->currency->getIso());
        $this->assertEquals('Canadian Dollar', $this->currency->getName());
    }

    /**
     * @test
     * @expectedException \Arcanedev\Currency\Services\Exceptions\InvalidTypeException
     */
    public function testMustThrowInvalidTypeException()
    {
        $this->currency->load(true);
    }

    /**
     * @test
     * @expectedException \Arcanedev\Currency\Services\Exceptions\InvalidIsoException
     */
    public function testMustThrowInvalidIsoExceptionOne()
    {
        $this->currency->load('U.S.D.');
    }

    /**
     * @test
     * @expectedException \Arcanedev\Currency\Services\Exceptions\InvalidIsoException
     */
    public function testMustThrowInvalidIsoExceptionTwo()
    {
        $this->currency->load('  EURO  ');
    }

    /**
     * @test
     * @expectedException \Arcanedev\Currency\Services\Exceptions\NotFoundIsoException
     */
    public function testMustThrowNotFoundIsoException()
    {
        $this->currency->load('LOL');
    }

    public function testCanFormatCurrency()
    {
        $this->assertEquals('100.00', $this->currency->load('USD')->format(100));
        $this->assertEquals('5,000.00', $this->currency->load('USD')->format(5000));
        $this->assertEquals('100,00', $this->currency->load('EUR')->format(100));
        $this->assertEquals('2.000,00', $this->currency->load('EUR')->format(2000));
    }

    public function testCanFormatCurrencyWithSymbol()
    {
        $this->assertEquals('$ 100.00', $this->currency->load('USD')->format(100, true));
        $this->assertEquals('$ 5,000.00', $this->currency->load('USD')->format(5000, true));
        $this->assertEquals('€ 100,00', $this->currency->load('EUR')->format(100, true));
        $this->assertEquals('€ 2.000,00', $this->currency->load('EUR')->format(2000, true));
    }

    public function testCanToggleSymbolPosition()
    {
        $this->currency->load('USD');
        $this->assertTrue($this->currency->isSymbolFirst());
        $this->assertEquals('$ 100.00', $this->currency->format(100, true));

        $this->assertFalse($this->currency->setSymbolLast()->isSymbolFirst());
        $this->assertEquals('100.00 $', $this->currency->format(100, true));
    }
}
