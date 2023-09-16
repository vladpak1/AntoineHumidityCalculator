<?php

namespace vladpak1\AntoineHumidityCalculator;

/**
 * Antoine humidity calculator model.
 *
 * This model is based on the Antoine equation, which allows us to
 * calculate the saturation pressure of water vapor at a given temperature.
 *
 * Please note that although this model is quite accurate, it does not take into
 * account many factors, such as the presence of other gases in the air, inhomogeneities,
 * thermal processes, the influence of salts and gradients.
 */
class Calculator
{
    private float $A = 8.07131;

    private float $B = 1730.63;

    private float $C = 233.426;

    /**
     * Class Constructor.
     *
     * Default values are for water at 1 atm.
     * You can find the coefficients for other substances on the Internet.
     *
     * @param float|null $A Antoine coefficient A
     * @param float|null $B Antoine coefficient B
     * @param float|null $C Antoine coefficient C
     */
    public function __construct(float $A = null, float $B = null, float $C = null)
    {
        if ($A !== null) {
            $this->A = $A;
        }

        if ($B !== null) {
            $this->B = $B;
        }

        if ($C !== null) {
            $this->C = $C;
        }
    }

    /**
     * Calculate new relative humidity.
     *
     * @param float $initialTemperature Initial temperature in Celsius
     * @param float $relativeHumidity   Relative humidity in %
     * @param float $newTemperature     New temperature in Celsius
     *
     * @throws \Exception
     * @return array      The array with new relative humidity and condensed water volume.
     */
    public function calculate(float $initialTemperature, float $relativeHumidity, float $newTemperature, int $precision = 2): array
    {
        if ($relativeHumidity > 100 || $relativeHumidity < 0) {
            throw new \Exception('Relative humidity cannot be less than 0 or greater than 100.');
        }

        if ($precision < 0) {
            throw new \Exception('Precision cannot be negative.');
        }

        $initialSaturationPressure = $this->getSaturationPressure($initialTemperature);

        // Calculate partial pressure of water vapor
        $partialPressure       = $initialSaturationPressure * $relativeHumidity / 100.0;
        $newSaturationPressure = $this->getSaturationPressure($newTemperature);

        // Calculate new relative humidity
        $newRelativeHumidity = ($partialPressure / $newSaturationPressure) * 100;

        // Calculate condensed water if new relative humidity exceeds 100%
        $condensedWaterVolume = 0;

        if ($newRelativeHumidity > 100) {
            $condensedWaterVolume = $this->calculateCondensation($initialTemperature, $relativeHumidity, $newTemperature);
            $newRelativeHumidity  = 100;
        }

        return [
            'newRelativeHumidity'  => round($newRelativeHumidity, $precision),
            'condensedWaterVolume' => round($condensedWaterVolume, $precision),
        ];
    }

    /**
     * Calculate condensation.
     * These are very rough calculations, and should not be used for anything other than a rough estimate.
     *
     * NOTE: This method assumes that the new relative humidity exceeds 100%.
     *
     * @param float $initialTemperature Initial temperature in Celsius
     * @param float $relativeHumidity   Relative humidity in %
     * @param float $newTemperature     New temperature in Celsius
     *
     * @return float Condensation in cubic centimeters or milliliters (cm3 or ml).
     */
    public function calculateCondensation(float $initialTemperature, float $relativeHumidity, float $newTemperature, int $precision = 2): float
    {
        // Calculate partial pressure of water vapor for initial temperature and new temperature
        $initialSaturationPressure = $this->getSaturationPressure($initialTemperature);
        $newSaturationPressure     = $this->getSaturationPressure($newTemperature);

        // Calculate the volume of vapor before and after cooling
        $initialVaporVolume        = $initialSaturationPressure * $relativeHumidity / 100.0;
        $maxVaporPressureAtNewTemp = $newSaturationPressure;

        // Calculate the difference between the volumes gives the amount of condensed water (in cubic centimeters or milliliters)
        $condensedWater = $initialVaporVolume - $maxVaporPressureAtNewTemp;

        return round($condensedWater, $precision);
    }

    /**
     * Calculate saturation pressure by Antoine equation.
     *
     * @param float $temperature Temperature in Celsius
     *
     * @return float Saturation pressure in mmHg
     */
    private function getSaturationPressure(float $temperature): float
    {
        // Antoine equation
        return pow(10, $this->A - ($this->B / ($this->C + $temperature)));
    }
}
