<?php
/**
 * Html2Pdf Library
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
 * Class Node
 *
 * Represent an DOM node in the document
 */
class Node
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var array
     */
    private $params;

    /**
     * @var array
     */
    private $children;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $name
     * @param array  $params
     * @param array  $children
     */
    public function __construct($name, $params, $children = array())
    {
        $this->name = $name;
        $this->params = $params;
        $this->children = $children;
    }

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * @return array
     */
    public function getParams()
    {
        return $this->params;
    }

    /**
     * @param string $key
     * @param mixed  $default
     *
     * @return null
     */
    public function getParam($key, $default = null)
    {
        if (isset($this->params[$key])) {
            return $this->params[$key];
        }
        return $default;
    }

    /**
     * @param string $key
     * @param mixed  $value
     *
     * @return mixed
     */
    public function setParam($key, $value)
    {
        return $this->params[$key] = $value;
    }

    /**
     * @return Node[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    public function setChildren($children)
    {
        $this->children = $children;
    }

    /**
     * @return int
     */
    public function getLine()
    {
        return $this->line;
    }

    /**
     * @param int $line
     */
    public function setLine($line)
    {
        $this->line = $line;
    }
}
