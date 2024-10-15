<?php

namespace IMEdge\Web\Device\Widget\Pluggable;

// Hint: really? Is RJ-45 a "pluggable", like a Cage?
interface PluggableInterface
{
    public function getHeight(): float;
    public function getWidth(): float;
    public function getTitle(): string;
    public function getDescription(): ?string;
}
