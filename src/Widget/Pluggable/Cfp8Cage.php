<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

class Cfp8Cage extends CfpCage
{
    protected float $width = 40;
    protected float $height = 9.5;
    protected float $depth = 102;

    public function getTitle(): string
    {
        return $this->translate('CFP8 Cage');
    }

    public function getDescription(): ?string
    {
        return $this->translate('A CFP8 slot');
    }
}
