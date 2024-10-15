<?php

namespace IMEdge\Web\Device\Widget\Gradient;

use IMEdge\Svg\SvgUtils;
use ipl\Html\BaseHtmlElement;
use ipl\Html\Html;

class LinearGradient1 extends BaseHtmlElement
{
    protected $tag = 'linearGradient';
    protected $defaultAttributes = [
        'id' => 'linearGradient1',
        'x1' => '0',
        'y1' => '0',
        'x2' => '1',
        'y2' => '0',
    ];
    /** @var float */
    protected $alpha = 1;

    public function setAlpha(float $alpha): self
    {
        $this->alpha = $alpha;
        return $this;
    }

    protected function assemble()
    {
        $alpha = SvgUtils::float($this->alpha);
        $this->add([
            Html::tag('stop', [
                'offset' => '0',
                'style' => "stop-color: var(--main-color-dark, hsl(220,40%,75%,$alpha))",
            ]),
            Html::tag('stop', [
                'offset' => '1',
                'style' => "stop-color: var(--main-color-dark, hsl(220,40%,30%,$alpha))",
            ]),
        ]);
    }
}
