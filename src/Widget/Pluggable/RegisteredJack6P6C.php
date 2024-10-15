<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

// RJ-25, RJ-12
class RegisteredJack6P6C extends RegisteredJack
{
    // 6P6C: 9.65mm x 6.60mm
    protected float $width  = 9.65;
    protected float $height = 6.60;

    protected int $contactPositions = 6;
    protected int $contacts = 6;
}
