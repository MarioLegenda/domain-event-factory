<?php

namespace EventStore\Event;

class EventCollection implements \Countable, \IteratorAggregate
{
    /**
     * @var array $events
     */
    private $events = [];
    /**
     * @param array $events
     * @param string $name
     * @return EventCollection
     */
    public function add(string $name, array $events): EventCollection
    {
        if (!$this->has($name)) {
            $this->events[$name] = [];

            $this->events[$name] = array_merge($this->events[$name], $events);
        }

        return $this;
    }
    /**
     * @param string $name
     * @param Event $event
     * @return EventCollection
     */
    public function addSingle(string $name, Event $event): EventCollection
    {
        $this->add($name, [$event]);

        return $this;
    }
    /**
     * @param string $name
     * @return bool
     */
    public function has(string $name): bool
    {
        return array_key_exists($name, $this->events);
    }
    /**
     * @param string $name
     * @return array|null
     */
    public function get(string $name): ?array
    {
        if (!$this->has($name)) {
            return null;
        }

        return $this->events[$name];
    }
    /**
     * @return bool
     */
    public function isEmpty(): bool
    {
        return empty($this->events);
    }
    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->events);
    }
    /**
     * @return array
     */
    public function asArray(): array
    {
        return $this->events;
    }
    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->asArray());
    }
}