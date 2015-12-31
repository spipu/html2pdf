<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF converter
 * distributed under the LGPL License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2016 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Debug;

/**
 * Class Debug
 */
class Debug implements DebugInterface
{
    /**
     * @var float
     */
    protected $startTime;

    /**
     * @var float
     */
    protected $lastTime;

    /**
     * @var int
     */
    protected $level = 0;

    /**
     * Debug constructor
     *
     * @return Debug
     */
    public function __construct()
    {

    }

    /**
     * display a debug line
     *
     * @param  string $name
     * @param  string $timeTotal
     * @param  string $timeStep
     * @param  string $memoryUsage
     * @param  string $memoryPeak
     *
     * @return void
     */
    protected function displayLine($name, $timeTotal, $timeStep, $memoryUsage, $memoryPeak)
    {
        $txt =
            str_pad($name, 30, ' ', STR_PAD_RIGHT).
            str_pad($timeTotal, 12, ' ', STR_PAD_LEFT).
            str_pad($timeStep, 12, ' ', STR_PAD_LEFT).
            str_pad($memoryUsage, 15, ' ', STR_PAD_LEFT).
            str_pad($memoryPeak, 15, ' ', STR_PAD_LEFT);

        echo '<pre style="padding:0; margin:0">'.$txt.'</pre>';
    }

    /**
     * Start the debugger
     *
     * @return Debug
     */
    public function start()
    {
        $time = microtime(true);

        $this->startTime = $time;
        $this->lastTime = $time;

        $this->displayLine('step', 'time', 'delta', 'memory', 'peak');
        $this->addStep('Init debug');

        return $this;
    }

    /**
     * stop the debugger
     *
     * @return Debug
     */
    public function stop()
    {
        $this->addStep('Before output');
        return $this;
    }

    /**
     * add a debug step
     *
     * @param  string  $name step name
     * @param  boolean $level (true=up, false=down, null=nothing to do)
     *
     * @return Debug
     */
    public function addStep($name, $level = null)
    {
        // if true : UP
        if ($level===true) {
            $this->level++;
        }

        $time  = microtime(true);
        $usage = memory_get_usage();
        $peak  = memory_get_peak_usage();
        $name  =
            str_repeat('  ', $this->level).
            $name.
            ($level===true ? ' Begin' : ($level===false ? ' End' : ''));

        $this->displayLine(
            $name,
            number_format(($time - $this->startTime)*1000, 1, '.', ' ').' ms',
            number_format(($time - $this->lastTime)*1000, 1, '.', ' ').' ms',
            number_format($usage/1024, 1, '.', ' ').' Ko',
            number_format($peak/1024, 1, '.', ' ').' Ko'
        );

        $this->lastTime = $time;

        // it false : DOWN
        if ($level===false) {
            $this->level--;
        }

        return $this;
    }
}
