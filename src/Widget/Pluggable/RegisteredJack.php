<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

use gipfl\Translation\TranslationHelper;
use IMEdge\Svg\SvgUtils;
use ipl\Html\BaseHtmlElement;
use ipl\Html\Html;
use ipl\Html\HtmlElement;

abstract class RegisteredJack extends BaseHtmlElement implements PluggableInterface
{
    use TranslationHelper;

    protected $tag = 'g';

    // Just to have a default, should be overridden
    protected float $width = 11.68;
    protected float $height = 8.30;

    protected int $contactPositions = 8;
    protected int $contacts = 8;

    // The following values are the same for all Jacks
    protected float $topNotchWidth = 3.25;
    protected float $notchWidth = 6.1;
    protected float $notchHeight = 1.1; // Blind guess

    protected float $contactWidth = 0.5;
    protected float $contactHeight = 3;
    // contact spacing is always 1.02 mm (1/25" center to center)
    protected float $contactSpacing = 1.02;
    protected float $connectorHeight = 6.6;

    protected float $strokeWidth = 0.6;

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

    public function getTitle(): string
    {
        return $this->translate('Registered Jack');
    }

    public function getDescription(): ?string
    {
        return $this->translate('This shows whatever');
    }

    protected function assemble()
    {
        $this->addAttributes([
            'title'       => $this->getTitle(),
            'description' => $this->getDescription(),
        ]);
        $this->add([
            $this->createJack(),
            $this->prepareContacts(),
            // $this->getTrafficImage(),
        ]);
    }

    protected function getTrafficImage(): HtmlElement
    {
        // This isn't useful, just an experiment
        $imgWith = (int) round($this->width * 16);
        $imgHeight = (int) round($this->height * 16);
        $start = 1685344860;
        $end = 1685347260;
        return Html::tag('image', [
            'x' => '0',
            'y' => '2',
            'width' => $this->width,
            'height' => $this->height - $this->notchHeight,
            'href' => "/icingaweb2/metrics/img?file=cd%2Fbf%2Fcdbfad7d333f491e905d3a9847e88693.rrd&width="
                . "$imgWith&height=$imgHeight&start=$start&end=$end&template=interface&format=svg&onlyGraph",
        ]);
    }

    protected function createJack(): HtmlElement
    {
        $fillColor = '#6E6E75'; // #494949, #121212

        return SvgUtils::polygon($this->jackPoints(), [
            'style'   => "fill: var(--color-port, $fillColor); stroke: var(--color-port-border, var(--color-port));",
            'stroke-width' => $this->strokeWidth,
        ]);
    }

    protected function prepareContacts(): array
    {
        $positions = $this->contactPositions;
        $width = $this->contactWidth;
        $halfContact = $width / 2;
        $height = $this->contactHeight - $this->strokeWidth;
        $space = $this->contactSpacing;
        $totalWidth = $positions * $space - $halfContact;
        $offset = ($this->width + $halfContact - $totalWidth) / 2;
        $firstContact = (int) round(($positions - $this->contacts) / 2);
        $lastContact = $positions - $firstContact;
        $lines = [];
        for ($i = 0; $i < $positions; $i++) {
            $style = $i >= $firstContact && $i <= $lastContact
                ? 'fill: var(--color-contacts, #FFCC0D)'
                : 'fill: #ccc';
            $lines[] = SvgUtils::rectangle($width, $height, [
                'style' => $style,
                'x' => SvgUtils::float($offset + $i * $space),
                'y' => SvgUtils::float($this->height - $height - $this->strokeWidth),
                // 'rx' => SvgUtils::float(0.2),
                'stroke-width' => 0,
            ]);
        }

        return $lines;
    }

    /**
     * @param array<array<float, float>> $points
     * @param array<float, float> $center
     * @param float $strokeWidth
     * @return array<array<float, float>>
     */
    protected static function subtractHalfStroke(array $points, array $center, float $strokeWidth): array
    {
        $halfStrokeWidth = $strokeWidth / 2;
        foreach ($points as & $point) {
            foreach ([0, 1] as $i) {
                if ($point[$i] < $center[$i]) {
                    $point[$i] += $halfStrokeWidth;
                } else {
                    $point[$i] -= $halfStrokeWidth;
                }
            }
        }

        return $points;
    }

    protected function jackPoints(): array
    {
        $width = $this->width;
        $height = $this->height;
        $x0 = 0;
        $x1 = ($width - $this->notchWidth) / 2;
        $x2 = ($width - $this->topNotchWidth) / 2;
        $x3 = $x2 + $this->topNotchWidth;
        $x4 = $x1 + $this->notchWidth;
        $x5 = $width;
        $y1 = $height - $this->connectorHeight;
        $y2 = $y1 - $this->notchHeight;
        $y3 = 0;
        $y4 = $height;

        return self::subtractHalfStroke([
            [$x0, $y1],
            [$x1, $y1],
            [$x1, $y2],
            [$x2, $y2],
            [$x2, $y3],
            [$x3, $y3],
            [$x3, $y2],
            [$x4, $y2],
            [$x4, $y1],
            [$x5, $y1],
            [$x5, $y4],
            [$x0, $y4]
        ], [$width / 2, $height / 2], $this->strokeWidth);
    }
}
