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
namespace Spipu\Html2Pdf\Parsing;

/**
 * Class Token
 *
 * Represents a token in the HTML flow
 */
class Token
{
    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $data;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $type
     * @param string $data
     * @param int    $line
     */
    public function __construct($type, $data, $line = -1)
    {
        $this->type = $type;
        $this->data = $data;
        $this->line = $line;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * @return string
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }
}
