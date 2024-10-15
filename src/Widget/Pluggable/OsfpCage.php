<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

class OsfpCage extends SfpCage
{
    protected float $width = 22.58;
    protected float $height = 13;
    protected float $depth = 107.8;

    public function getTitle(): string
    {
        return $this->translate('OSFP Cage');
    }

    public function getDescription(): ?string
    {
        return $this->translate('An OSFP slot');
    }
}
