<?php

namespace IMEdge\Web\Device\Widget\Port;

use gipfl\Translation\TranslationHelper;
use IMEdge\Svg\SvgUtils;
use ipl\Html\BaseHtmlElement;
use ipl\Html\HtmlElement;

class SfpTransceiver extends BaseHtmlElement
{
    // Hint: has a notch at bottom side, hinge (gelenk für bügelverschluss / bail latch) ist auch unten
    use TranslationHelper;

    protected $defaultAttributes = [
        'id' => 'tpl-sfp-transceiver'
    ];
    protected $tag = 'g';

    // https://en.wikipedia.org/wiki/Small_Form-factor_Pluggable -> Mechanical dimensions
    protected float $width = 13.4;
    protected float $height = 8.5;
    // protected $depth = 56.5;

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
            'title' => $this->translate('SFP Transceiver'),
            'description' => $this->translate('An SFP Transceiver')
        ]);
        $this->add([
            $this->createSlot(),
            // TODO: re-check direction, it is standardized
            $this->createConnector('tx', $this->width / 2 - 6.25 / 2, $this->height / 2),
            $this->createConnector('rx', $this->width / 2 + 6.25 / 2, $this->height / 2),
        ]);
    }
// --color-sfp-tx-port: #EBEC3D
// --color-sfp-rx-port:#4EE654
// --color-sfp-rx-inner:red
/*

--color-sfp-tx-port: #EBEC3D
--color-sfp-rx-port:#4EE654
 */
    protected function createConnector(string $name, $x, $y)
    {
        $prefix = "--color-sfp-$name";
        return [
            SvgUtils::circle($x, $y, 2.5, [
                'style' => "fill: var($prefix-port, #87CCFF); stroke: none;",
                // 'title' => 'RX, -0.25 dbm'
            ]),
            // Hint: it would be better to cut this from the outer circle
            SvgUtils::circle($x, $y, 1.25, [ // Ferrule -> 1.25
                'style' => "fill: var($prefix-ferrule, #494949); stroke: none;",
            ]),
            SvgUtils::circle($x, $y, 0.85, [ // 0.6 - 0.85
                'style' => "fill: var($prefix-inner, #FAF8DA); stroke: none;",
            ]),
        ];
    }

    protected function createSlot(): HtmlElement
    {
        $borderColor = 'blue';
        $borderColor = 'transparent';
        $fillColor = 'transparent';
        return SvgUtils::rectangle($this->width, $this->height, [
            'style'   => "fill: var(--color-container, $fillColor);"
            . " stroke: var(--color-container-border, $borderColor);",
            'stroke-width' => 0.5,
        ]);
    }
}
