<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

class QsfpCage extends SfpCage
{
    protected float $width = 18.35;
    protected float $height = 8.5;
    protected float $depth = 72.4;

    public function getTitle(): string
    {
        return $this->translate('QSFP Cage');
    }

    public function getDescription(): string
    {
        return $this->translate('A QSFP slot');
    }
}
