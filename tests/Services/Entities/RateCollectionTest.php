<?php namespace Arcanedev\Currency\Tests\Services\Entities;

use Arcanedev\Currency\Services\Entities\RateCollection;

class RateCollectionTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var RateCollection */
     protected $rates;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    protected function setUp()
    {
        parent::setUp();

        $this->rates = new RateCollection;
    }

    protected function tearDown()
    {
        unset($this->rates);
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
        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\RateCollection', $this->rates);
    }

    /**
     * @test
     */
    public function testCanAddOneRateToCollection()
    {
        $this->assertCount(0, $this->rates);

        $this->rates->add('USD_EUR', 0.8014);
        $this->assertCount(1, $this->rates);

        $this->rates->add('EUR_USD', 1.2478);
        $this->assertCount(2, $this->rates);
    }

    /**
     * @test
     */
    public function testCanAddManyRatesToCollection()
    {
        $this->addManyRates();

        $this->assertCount(4, $this->rates);
    }

    /**
     * @test
     */
    public function testCanCalculateAmount()
    {
        $this->addManyRates();

        $this->assertEquals(801.4, $this->rates->calculate('USD', 'EUR', 1000));
        $this->assertEquals('€ 1.202,10', $this->rates->calculate('USD', 'EUR', 1500, true));
        $this->assertEquals(400.69, $this->rates->calculateFromKey('USD_EUR', 499.99));
        $this->assertEquals('€ 200,34', $this->rates->calculateFromKey('USD_EUR', 249.99, true));

        $this->assertEquals(1125.3, $this->rates->calculate('USD', 'CAD', 1000));
        $this->assertEquals('$ 1,687.95', $this->rates->calculate('USD', 'CAD', 1500, true));
        $this->assertEquals(281.31, $this->rates->calculateFromKey('USD_CAD', 249.99));
        $this->assertEquals('$ 281.31', $this->rates->calculateFromKey('USD_CAD', 249.99, true));

        $this->assertEquals(1247.8, $this->rates->calculate('EUR', 'USD', 1000));
        $this->assertEquals('$ 1,871.70', $this->rates->calculate('EUR', 'USD', 1500, true));
        $this->assertEquals(623.89, $this->rates->calculateFromKey('EUR_USD', 499.99));
        $this->assertEquals('$ 311.94', $this->rates->calculateFromKey('EUR_USD', 249.99, true));

        $this->assertEquals(1404.5, $this->rates->calculate('EUR', 'CAD', 1000));
        $this->assertEquals('$ 2,106.75', $this->rates->calculate('EUR', 'CAD', 1500, true));
        $this->assertEquals(702.24, $this->rates->calculateFromKey('EUR_CAD', 499.99));
        $this->assertEquals('$ 351.11', $this->rates->calculateFromKey('EUR_CAD', 249.99, true));
    }

    /* ------------------------------------------------------------------------------------------------
     |  Other Functions
     | ------------------------------------------------------------------------------------------------
     */
    private function addManyRates()
    {
        $this->rates->addMany([
            'USD' => [
                'EUR'   => 0.8014,
                'CAD'   => 1.1253,
            ],
            'EUR' => [
                'USD'   => 1.2478,
                'CAD'   => 1.4045,
            ],
        ]);
    }
}
