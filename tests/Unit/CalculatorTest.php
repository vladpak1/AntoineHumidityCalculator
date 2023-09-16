<?php

namespace vladpak1\AirDog\Tests\Unit;

use PHPUnit\Framework\TestCase;
use vladpak1\AntoineHumidityCalculator\Calculator;

class CalculatorTest extends TestCase
{
    public function testInitializationWithDefaultValues()
    {
        $calculator = new Calculator();
        $this->assertNotNull($calculator);
    }

    public function testRelativeHumidityBoundary()
    {
        $calculator = new Calculator();

        $this->expectException(\Exception::class);
        $calculator->calculate(30, 101, 10);

        $this->expectException(\Exception::class);
        $calculator->calculate(30, -1, 10);
    }

    /**
     * @dataProvider dataProviderForCalculate
     */
    public function testCalculate(float $initialTemperature, float $relativeHumidity, float $newTemperature, array $expectedOutput)
    {
        $calculator = new Calculator();

        $output = $calculator->calculate($initialTemperature, $relativeHumidity, $newTemperature);

        $this->assertEquals($expectedOutput['newRelativeHumidity'], $output['newRelativeHumidity']);
        $this->assertEquals($expectedOutput['condensedWaterVolume'], $output['condensedWaterVolume']);
    }

    public static function dataProviderForCalculate(): array
    {
        return [
            // Format: [initialTemperature, relativeHumidity, newTemperature, expectedOutput]
            [30, 70, 10, ['newRelativeHumidity' => 100, 'condensedWaterVolume' => 13.06]],
            [0, 100, 0, ['newRelativeHumidity' => 100, 'condensedWaterVolume' => 0]],
            [0, 0, 0, ['newRelativeHumidity' => 0, 'condensedWaterVolume' => 0]],
            [0, 0, 100, ['newRelativeHumidity' => 0, 'condensedWaterVolume' => 0]],
            [0, 100, 100, ['newRelativeHumidity' => 0.6, 'condensedWaterVolume' => 0]],
        ];
    }
}
