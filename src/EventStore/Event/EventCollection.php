<?php

namespace EventStore\Event;

use EventStore\Infrastructure\ArrayableInterface;

class EventCollection implements ArrayableInterface, \Countable, \IteratorAggregate
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
    public function toArray(): array
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