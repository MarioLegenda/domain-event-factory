<?php

namespace EventStore\Infrastructure;

interface ArrayableInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
}