<?php
/**
 * Html2Pdf Library
 *
 * HTML => PDF convertor
 * distributed under the OSL-3.0 License
 *
 * @package   Html2pdf
 * @author    Laurent MINGUET <webmaster@html2pdf.fr>
 * @copyright 2017 Laurent MINGUET
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
     * @var bool
     */
    private $close;

    /**
     * @var bool
     */
    private $autoClose;

    /**
     * @var int
     */
    private $line;

    /**
     * @param string $name
     * @param array  $params
     * @param bool   $close
     * @param bool   $autoClose
     */
    public function __construct($name, $params, $close, $autoClose = false)
    {
        $this->setName($name);
        $this->setParams($params);
        $this->setClose($close);
        $this->setAutoClose($autoClose);
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
        if (array_key_exists($key, $this->params)) {
            return $this->params[$key];
        }
        return $default;
    }

    /**
     * Get a style
     * @param string $key
     * @param string|null $default
     *
     * @return string|null
     */
    public function getStyle($key, $default = null)
    {
        $styles = $this->getParam('style', []);

        if (array_key_exists($key, $styles)) {
            return $styles[$key];
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
     * @param array $params
     */
    public function setParams($params)
    {
        $this->params = $params;
    }

    /**
     * @return bool
     */
    public function isClose()
    {
        return $this->close;
    }

    /**
     * @param bool $close
     */
    public function setClose($close)
    {
        $this->close = (bool) $close;
    }
    /**
     * @return bool
     */
    public function isAutoClose()
    {
        return $this->autoClose;
    }

    /**
     * @param $autoClose
     */
    public function setAutoClose($autoClose)
    {
        $this->autoClose = (bool) $autoClose;
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
