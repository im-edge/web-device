<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

// called RJ-22, RJ-10, RJ-9 - but has no real RJ norm
class RegisteredJack4P4C extends RegisteredJack
{
    protected float $width  = 7.70;
    protected int $contactPositions = 4;
    protected int $contacts = 4;
}
