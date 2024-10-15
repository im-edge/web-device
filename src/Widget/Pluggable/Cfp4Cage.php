<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

class Cfp4Cage extends CfpCage
{
    protected float $width = 21.5;
    protected float $height = 9.5;
    protected float $depth = 92;

    public function getTitle(): string
    {
        return $this->translate('CFP4 Cage');
    }

    public function getDescription(): ?string
    {
        return $this->translate('A CFP4 slot');
    }
}
