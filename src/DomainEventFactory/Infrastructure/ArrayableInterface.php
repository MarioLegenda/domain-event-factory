<?php

namespace DomainEventFactory\Infrastructure;

interface ArrayableInterface
{
    /**
     * @return array
     */
    public function toArray(): array;
}