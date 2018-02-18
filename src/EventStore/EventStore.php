<?php

namespace EventStore;

use EventStore\Event\Event;
use EventStore\Event\EventCollection;
use EventStore\Event\Metadata;
use EventStore\Event\MetadataCollection;
use EventStore\Event\MetadataFactory;

class EventStore implements EventStoreInterface, \Countable, \IteratorAggregate
{
    /**
     * @var EventCollection $events
     */
    private $events = [];
    /**
     * @var MetadataCollection $metadata
     */
    private $metadata = [];
    /**
     * @var EventStore $instance
     */
    private static $instance;
    /**
     * @return EventStore
     */
    public static function construct()
    {
        static::$instance = (static::$instance instanceof static) ? static::$instance : new static();
        
        return static::$instance;
    }

    public function __construct()
    {
        $this->events = new EventCollection();
        $this->metadata = new MetadataCollection();
    }
    /**
     * @inheritdoc
     */
    public function store($object): EventStoreInterface
    {
        $this->validateStoreType($object);

        $this->createMetadata($object);

        return $this;
    }
    /**
     * @inheritdoc
     */
    public function getEvents(): EventCollection
    {
        if ($this->events->isEmpty()) {
            /** @var array $metadata */
            foreach ($this->metadata as $metadata) {
                /** @var Metadata $m */
                foreach ($metadata as $m) {
                    $this->events->add($m->getName(), $this->getEvent($m->getName()));
                }
            }
        }

        $this->metadata = null;

        return $this->events;
    }
    /**
     * @inheritdoc
     */
    public function getEvent(string $eventName): array
    {
        if (!$this->events->has($eventName)) {
            if (!$this->metadata->has($eventName)) {
                $message = sprintf('Invalid event. Event \'%s\' does not exist', $eventName);
                throw new \RuntimeException($message);
            }

            $events = Event::createFromMetadata($this->metadata->get($eventName));

            $this->events->add($eventName, $events);

            return $this->events->get($eventName);
        }

        if ($this->events->has($eventName)) {
            return $this->events->get($eventName);
        }

        $message = sprintf('Invalid event. Event \'%s\' does not exist', $eventName);
        throw new \RuntimeException($message);
    }
    /**
     * @inheritdoc
     */
    public function hasEvent(string $eventName): bool
    {
        if ($this->events->isEmpty()) {
            $this->getEvents();
        }

        return $this->events->has($eventName);
    }
    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->events);
    }
    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->getEvents()->asArray());
    }
    /**
     * @param object $object
     */
    private function validateStoreType($object)
    {
        if (!is_object($object)) {
            $type = (string) gettype($object);
            $message = sprintf('Event store can only be populated with objects. \'%s\' given', $type);

            throw new \RuntimeException($message);
        }
    }
    /**
     * @param object $object
     * @throws \Exception
     */
    private function createMetadata($object)
    {
        $metadataFactory = new MetadataFactory($object);
        $metadata = $metadataFactory->createAll();

        $this->metadata->merge($metadata);
    }
}