# Antoine Humidity Calculator

This package provides a simple calculator model for determining the saturation pressure of water vapor at given temperatures using the Antoine equation.

## Installation using Composer

```bash
composer require vladpak1/antoine-humidity-calculator
```

## Description

The Antoine equation is a mathematical relationship used to give vapor pressures for pure components. This calculator is specifically based on that equation to determine relative humidity and potential water condensation at different temperatures.


**Note**: This model offers reasonable accuracy but may not be suitable for highly precise applications, as it doesn't consider:

- Presence of other gases in the air
- Inhomogeneities and thermal processes
- Influence of salts
- Gradients, etc.

## Usage Example

```php
use vladpak1\antoineHumidityCalculator\AntoineHumidityCalculator;use vladpak1\AntoineHumidityCalculator\Calculator;

// Load composer
require_once __DIR__ . '/vendor/autoload.php';

$calculator = new Calculator();

// Calculate relative humidity after changing the temperature.
$result = $calculator->calculate(25.0, 60.0, 30.0);

echo "New Relative Humidity: " . $result['newRelativeHumidity'] . "%\n";
echo "Condensed Water Volume: " . $result['condensedWaterVolume'] . " ml\n";
```
