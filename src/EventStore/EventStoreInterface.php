<?php

namespace EventStore;

use EventStore\Event\EventCollection;

interface EventStoreInterface
{
    /**
     * @param object $object
     * @return EventStoreInterface
     *
     * Stores event metadata from which an Event object will be created. Event objects are created
     * in getEvents() or getEvent() methods
     */
    public function createMetadata($object): EventStoreInterface;
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
