<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

class Cfp2Cage extends CfpCage
{
    protected float $width = 41.5;
    protected float $height = 12.4;
    protected float $depth = 107.5;

    public function getTitle(): string
    {
        return $this->translate('CFP2 Cage');
    }

    public function getDescription(): ?string
    {
        return $this->translate('A CFP slot');
    }
}
