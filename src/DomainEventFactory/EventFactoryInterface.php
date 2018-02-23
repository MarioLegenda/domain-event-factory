<?php

namespace DomainEventFactory;

use DomainEventFactory\Event\EventCollection;
use DomainEventFactory\Event\EventObjectInterface;

interface EventFactoryInterface
{
    /**
     * @param EventObjectInterface $object
     * @return EventFactoryInterface
     *
     * Stores event metadata from which an Event object will be created. Event objects are created
     * in getEvents() or getEvent() methods
     */
    public function createMetadata(EventObjectInterface $object): EventFactoryInterface;
    /**
     * @return EventCollection
     */
    public function getEvents(): EventCollection;
    /**
     * @param string $eventName
     * @return array
     */
    public function getEvent(string $eventName): array;
    /**
     * @param string $eventName
     * @return bool
     */
    public function hasEvent(string $eventName): bool;
}
