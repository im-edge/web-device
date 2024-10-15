<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

// https://en.wikipedia.org/wiki/C_form-factor_pluggable
class CfpCage extends SfpCage
{
    protected float $width = 82;
    protected float $height = 13.6;
    protected float $depth = 144.8;

    public function getTitle(): string
    {
        return $this->translate('CFP Cage');
    }

    public function getDescription(): ?string
    {
        return $this->translate('A CFP slot');
    }
}
