<?php 

namespace Spipu\Html2Pdf;

/**
 * Class SvgDrawer
 */
class SvgDrawer
{
    /**
     * @var bool
     */
    private $isDrawing = false;

    /**
     * @var array
     */
    private $coordinates;

    /**
     * @var MyPdf
     */
    private $pdf;

    /**
     * @var CssConverter
     */
    private $cssConverter;

    /**
     * @param $pdf
     * @param $cssConverter
     */
    public function __construct($pdf, $cssConverter)
    {
        $this->pdf = $pdf;
        $this->cssConverter = $cssConverter;
    }

    public function isInDraw()
    {
        return $this->isDrawing;
    }

    /**
     * @param array $coords Coordinates as array with x, y, w, h keys
     */
    public function startDrawing($coords)
    {
        $this->isDrawing = true;
        $this->coordinates = $coords;
    }

    /**
     * Stop drawing mode
     */
    public function stopDrawing()
    {
        $this->isDrawing = false;
    }

    /**
     * @param $params
     * @param $styles
     */
    public function ellipse($params, $styles)
    {
        $style = $this->pdf->svgSetStyle($styles);
        $cx = isset($params['cx']) ? $this->cssConverter->ConvertToMM($params['cx'], $this->coordinates['w']) : 0.;
        $cy = isset($params['cy']) ? $this->cssConverter->ConvertToMM($params['cy'], $this->coordinates['h']) : 0.;
        $rx = isset($params['ry']) ? $this->cssConverter->ConvertToMM($params['rx'], $this->coordinates['w']) : 0.;
        $ry = isset($params['rx']) ? $this->cssConverter->ConvertToMM($params['ry'], $this->coordinates['h']) : 0.;
        $this->pdf->svgEllipse($cx, $cy, $rx, $ry, $style);
    }

    /**
     * @param $params
     * @param $styles
     */
    public function circle($params, $styles)
    {
        $style = $this->pdf->svgSetStyle($styles);
        $cx = isset($params['cx']) ? $this->cssConverter->ConvertToMM($params['cx'], $this->coordinates['w']) : 0.;
        $cy = isset($params['cy']) ? $this->cssConverter->ConvertToMM($params['cy'], $this->coordinates['h']) : 0.;
        $r  = isset($params['r'])  ? $this->cssConverter->ConvertToMM($params['r'],  $this->coordinates['w']) : 0.;
        $this->pdf->svgEllipse($cx, $cy, $r, $r, $style);
    }
}
