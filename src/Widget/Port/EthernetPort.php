<?php

namespace IMEdge\Web\Device\Widget\Port;

use IMEdge\Svg\SvgUtils;
use IMEdge\Web\Device\Widget\Pluggable\RegisteredJack;
use IMEdge\Web\Device\Widget\Pluggable\RegisteredJack8P8C;
use ipl\Html\BaseHtmlElement;
use ipl\Html\HtmlElement;

class EthernetPort extends BaseHtmlElement
{
    // https://de.wikipedia.org/wiki/LWL-Steckverbinder
    // SFP: https://lightemsystems.com/wp-content/uploads/2020/11/LX-MC-Search-icon-04-min.jpg
    // https://de.wikipedia.org/wiki/Ger%C3%A4testecker

    protected $tag = 'g';

    protected $defaultAttributes = [
        'id' => 'tpl-rj45'
    ];
    protected float $lightWidth = 2.4;
    protected float $lightHeight = 1.6;
    protected float $lightOffsetX = -0.4;
    protected float $lightOffsetY = -0.4;
    protected bool $showLeds = true;

    public function getFullHeight(): float
    {
        return 12; // TODO: config
    }

    public function getFullWidth(): float
    {
        return 14; // TODO: config
    }

    protected function assemble()
    {
        $x = 1;
        $y = 2;

        $this->add(SvgUtils::rectangle($this->getFullWidth(), $this->getFullHeight(), [
            'x' => SvgUtils::float(0),
            'y' => SvgUtils::float(0),
            'style' => 'fill: var(--color-port-frame, #cccccc22)',
            'stroke-width' => 0,
        ]));
        $jack = (new RegisteredJack8P8C())->setOffset($x, $y);
        $this->add($jack);
        if ($this->showLeds) {
            $this->showLeds($jack, $x, $y);
        }
    }

    protected function showLeds(RegisteredJack $jack, $x, $y)
    {
        $colorOk = '#44BB77';
        $colorWarning = '#FFAA44';
        $this->add([
            // Upper left LED
            $this->createLed(
                'left',
                $colorOk,
                $x + $this->lightOffsetX,
                $y + $this->lightOffsetY
            ),
            // Upper right LED
            /*$right = */$this->createLed(
                'right',
                $colorWarning,
                $x - $this->lightOffsetX + $jack->getWidth() - $this->lightWidth,
                $y + $this->lightOffsetY
            ),
        ]);
        /*
        $right->setVoid(false);
        $right->add(
            Html::tag('animate', [
                'attributeName' => 'fill',
                'values' => '#ffff00;#ff0000',
                'begin' => '0s',
                'dur' => '1s',
                'calcMode'    => 'discrete',
                'repeatCount' => 'indefinite',
            ])
        );
        */
    }

    protected function createLed($position, $defaultColor, $x, $y): HtmlElement
    {
        $colorVarName = 'color-port-led-' . $position;
        $color = "var(--$colorVarName, $defaultColor)";
        return SvgUtils::rectangle($this->lightWidth, $this->lightHeight, [
            'x' => SvgUtils::float($x),
            'y' => SvgUtils::float($y),
            'rx' => SvgUtils::float($this->lightHeight / 4),
            'style' => "--led-color: $color; fill: var(--led-color);"
                . " stroke: var(--color-port-border, #333);"
                . " stroke-width: 0.1; filter: drop-shadow(0 0 0.5pt $color);",
            'stroke-width' => 0,
            'class' => 'port-led led-position-' . $position
        ]);
    }
}
