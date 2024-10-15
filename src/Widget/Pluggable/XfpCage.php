<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

class XfpCage extends SfpCage
{
    protected float $width = 18.35;
    protected float $height = 8.5;
    protected float $depth = 78;

    public function getTitle(): string
    {
        return $this->translate('XFP Cage');
    }

    public function getDescription(): string
    {
        return $this->translate('An XFP slot');
    }
}
