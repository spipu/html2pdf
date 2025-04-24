<?php
namespace Tests\Tag\Html;

use PHPUnit\Framework\TestCase;
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Tag\Html\Button;

class ButtonTest extends TestCase
{
    private $html2pdf;
    private $button;

    protected function setUp(): void
    {
        $this->html2pdf = new Html2Pdf();
        $this->button = new Button();
    }

    public function testButtonName()
    {
        $this->assertEquals('button', $this->button->getName());
    }

    public function testButtonProperties()
    {
        $properties = [
            'type' => 'submit',
            'class' => 'btn-primary',
            'background' => '#007bff',
            'color' => '#ffffff'
        ];
        
        $result = $this->button->open($properties);
        $this->assertTrue($result);
    }
}
