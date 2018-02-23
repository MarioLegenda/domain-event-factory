<?php

namespace DomainEventFactory\Event;

interface EventInterface
{
    /**
     * @return string
     */
    public function getName(): string;
    /**
     * @return array
     */
    public function getPayload(): array;
    /**
     * @return array
     */
    public function toArray(): array;
    /**
     * @return EventObjectInterface
     */
    public function getObject(): EventObjectInterface;
    /**
     * @return string
     */
    public function getObjectHash(): string;
}