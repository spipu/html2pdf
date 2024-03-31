<?php
/**
 * Html2Pdf Library - Debug
 *
 * HTML => PDF converter
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2023 Laurent MINGUET
 */

namespace Spipu\Html2Pdf\Debug;

interface DebugInterface
{
    /**
     * Start the debugger
     *
     * @return DebugInterface
     */
    public function start();

    /**
     * Stop the debugger
     *
     * @return DebugInterface
     */
    public function stop();

    /**
     * add a debug step
     *
     * @param  string  $name step name
     * @param  boolean $level (true=up, false=down, null=nothing to do)
     *
     * @return DebugInterface
     */
    public function addStep($name, $level = null);
}
