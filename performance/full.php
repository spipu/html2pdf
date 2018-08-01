<?php

$steps = [1, 5, 10, 25, 50, 75, 100, 250, 500, 750, 1000];
$nbTry = 10;

echo "\n";
echo "Steps: ".implode(', ', $steps)."\n";
echo "Try by Steps: $nbTry\n";
echo "\n";

$results = [];
foreach ($steps as $step) {
    $times    = [];
    $memories = [];
    for ($try = 0; $try < $nbTry; $try++) {
        $shell = "php ./step.php $step";

        $output = explode('|', trim(exec($shell)));

        $times[] = (int) $output[0];
        $memories[] = (int) $output[1];
    }
    $result = [
        'step'   => $step,
        'time'   => floor(array_sum($times) / count($times)),
        'memory' => floor(array_sum($memories) / count($memories)),
    ];

    $results[] = $result;
    echo implode('|', $result)."\n";
}