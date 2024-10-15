<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

use gipfl\Translation\TranslationHelper;
use IMEdge\Svg\SvgUtils;
use ipl\Html\BaseHtmlElement;
use ipl\Html\HtmlElement;

class SfpCage extends BaseHtmlElement implements PluggableInterface
{
    // Hint: has a notch at bottom side, hinge (gelenk für bügelverschluss / bail latch) ist auch unten
    use TranslationHelper;

    protected $defaultAttributes = [
        'id' => 'tpl-sfp-cage'
    ];
    protected $tag = 'g';

    // https://en.wikipedia.org/wiki/Small_Form-factor_Pluggable -> Mechanical dimensions
    protected float $width = 13.4;
    protected float $height = 8.5;
    protected float $depth = 56.5;

    // CFP-8: 41,5mm, 107,5mm, 9.5mm, power cons. much higher than QSFP-DD, can’t be used on QSFP+/QSFP28 ports

    // Auch interessent:
    // https://www.gigalight.com/downloads/standards/sff-8683.pdf

    public function getHeight(): float
    {
        return $this->height;
    }

    public function getWidth(): float
    {
        return $this->width;
    }

    public function setOffset($x, $y): self
    {
        // TODO: use <symbol /> and x/y?
        $this->setAttributes([
            'transform' => sprintf('translate(%s, %s)', SvgUtils::float($x), SvgUtils::float($y)),
        ]);

        return $this;
    }

    protected function assemble()
    {
        $this->addAttributes([
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
        ]);
        $this->add([
            $this->createSlot(),
        ]);
    }

    public function getTitle(): string
    {
        return $this->translate('SFP Cage');
    }

    public function getDescription(): ?string
    {
        return $this->translate('An SFP slot');
    }

    protected function createSlot(): HtmlElement
    {
        $fillColor = '#6E6E75'; // #494949, #121212
        $borderColor = '#ff0000';
        $borderColor = 'transparent';
        $fillColor = 'transparent';
        return SvgUtils::rectangle($this->width, $this->height, [
            'style'   => "fill: var(--color-container, $fillColor);"
                . " stroke: var(--color-container-border, $borderColor);",
            'stroke-width' => 0.5,
        ]);
    }
}
