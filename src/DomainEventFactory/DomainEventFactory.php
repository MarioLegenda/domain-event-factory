<?php

namespace DomainEventFactory;

use DomainEventFactory\Event\Event;
use DomainEventFactory\Event\EventCollection;
use DomainEventFactory\Event\EventObjectInterface;
use DomainEventFactory\Event\Metadata;
use DomainEventFactory\Event\MetadataCollection;
use DomainEventFactory\Event\MetadataFactory;

class DomainEventFactory implements EventFactoryInterface, \Countable, \IteratorAggregate
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
     * @var DomainEventFactory $instance
     */
    private static $instance;
    /**
     * @return DomainEventFactory
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
    public function createMetadata(EventObjectInterface $object): EventFactoryInterface
    {
        $this->validateStoreType($object);

        $this->realCreateMetadata($object);

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

            $this->metadata->remove($eventName);
            
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
        return new \ArrayIterator($this->getEvents()->toArray());
    }
    /**
     * @param EventObjectInterface $object
     */
    private function validateStoreType(EventObjectInterface $object)
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
    private function realCreateMetadata($object)
    {
        $metadataFactory = new MetadataFactory(
            $object,
            'DomainEventFactory',
            'EventPayloadName'
        );
        $metadata = $metadataFactory->createAll();

        $this->metadata->merge($metadata);
    }
}