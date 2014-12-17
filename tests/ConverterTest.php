<?php namespace Arcanedev\Currency\Tests;

use Arcanedev\Currency\Converter;

use Arcanedev\Currency\Services\Entities\RateCollection;

use Mockery as m;

class ConverterTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var Converter */
    private $converter;

    private $provider;

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    public function setUp()
    {
        parent::setUp();

        $providerInterface = 'Arcanedev\\Currency\\Contracts\\CurrencyProvider';
        $this->provider = m::mock($providerInterface, function(\Mockery\MockInterface $mock) {
            $mock->shouldReceive('convertMany')
                 ->andReturn(['EUR' => ['CAD' => 0.8, 'USD' => 0.9]]);
        });


        $this->converter = new Converter($this->provider);
    }

    public function tearDown()
    {
        parent::tearDown();

        m::close();
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
        $this->assertInstanceOf('Arcanedev\\Currency\\Converter', $this->converter);
    }

    /**
     * @test
     */
    public function testCanConvert()
    {
        /** @var RateCollection $rates */
        $rates = $this->converter->convert([
            'EUR' => ['CAD', 'USD']
        ]);

        $this->assertInstanceOf('Arcanedev\\Currency\\Services\\Entities\\RateCollection', $rates);
        $this->assertCount(2, $rates);
        $this->assertEquals(80, $rates->calculate('EUR', 'CAD', 100));
        $this->assertEquals(90, $rates->calculate('EUR', 'USD', 100));
    }
}
