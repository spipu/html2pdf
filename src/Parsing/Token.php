<?php
/**
 * Html2Pdf Library - parsing Html class
 *
 * HTML => PDF convertor
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
    private $type;
    private $data;
    private $offset;

    /**
     * @param string $type
     * @param string $data
     * @param int    $offset
     */
    public function __construct($type, $data, $offset = 0)
    {
        $this->type = $type;
        $this->data = $data;
        $this->offset = $offset;
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
    public function getOffset()
    {
        return $this->offset;
    }
}
