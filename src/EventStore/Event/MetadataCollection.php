<?php

namespace EventStore\Event;

class MetadataCollection implements \IteratorAggregate, \Countable
{
    /**
     * @var array $metadata
     */
    private $metadata = [];
    /**
     * @return int
     */
    public function count(): int
    {
        return count($this->metadata);
    }
    /**
     * @return \ArrayIterator
     */
    public function getIterator(): \ArrayIterator
    {
        return new \ArrayIterator($this->metadata);
    }
    /**
     * @param string $metadataName
     * @param Metadata $metadata
     * @return MetadataCollection
     */
    public function add(string $metadataName, Metadata $metadata): MetadataCollection
    {
        $this->metadata[$metadataName][] = $metadata;

        return $this;
    }
    /**
     * @param string $metadataName
     * @return Metadata[]|null
     */
    public function get(string $metadataName): ?array
    {
        if (!$this->has($metadataName)) {
            return null;
        }

        return $this->metadata[$metadataName];
    }
    /**
     * @param string $metadataName
     * @return bool
     */
    public function has(string $metadataName): bool
    {
        return array_key_exists($metadataName, $this->metadata);
    }
    /**
     * @param array $metadataObjects
     */
    public function merge(array $metadataObjects)
    {
        /** @var Metadata $m */
        foreach ($metadataObjects as $m) {
            $this->add($m->getName(), $m);
        }
    }
}