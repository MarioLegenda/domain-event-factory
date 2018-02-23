<?php

namespace DomainEventFactory\Event;

use DocBlockReader\Reader;

class Metadata
{
    /**
     * @var string $name
     */
    private $name;
    /**
     * @var EventObjectInterface $object
     */
    private $object;
    /**
     * @var array $metadata
     */
    private $metadata = [];
    /**
     * @var string $payloadName
     */
    private $payloadName;
    /**
     * Metadata constructor.
     * @param string $name
     * @param EventObjectInterface $object
     * @param string|null $eventPayloadName
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function __construct(
        string $name,
        EventObjectInterface $object,
        string $eventPayloadName = null
    ) {
        $this->name = $name;
        $this->object = $object;
        $this->payloadName = (is_string($eventPayloadName)) ? $eventPayloadName : 'EventPayload';

        $properties = (new \ReflectionClass($object))->getProperties();

        $this->resolveMetadata($properties, $object);
    }
    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }
    /**
     * @return EventObjectInterface
     */
    public function getObject(): EventObjectInterface
    {
        return $this->object;
    }
    /**
     * @return array
     */
    public function getMetadata(): array
    {
        $values = [];
        foreach ($this->metadata as $propertyName => $m) {
            $values[$propertyName] = $m['value'];
        }

        return $values;
    }
    /**
     * @param string $propertyName
     * @return bool
     */
    public function hasProperty(string $propertyName): bool
    {
        return array_key_exists($propertyName, $this->metadata);
    }
    /**
     * @param string $propertyName
     * @return bool|null
     */
    public function getValueForProperty(string $propertyName)
    {
        if (!$this->hasProperty($propertyName)) {
            return null;
        }

        return $this->metadata[$propertyName];
    }
    /**
     * @param string $propertyName
     * @return array|null
     */
    public function getMetadataForProperty(string $propertyName): ?array
    {
        if (!$this->hasProperty($propertyName)) {
            return null;
        }

        return [
            $propertyName => $this->getValueForProperty($propertyName),
        ];
    }
    /**
     * @param array $reflectionProperties
     * @param EventObjectInterface $object
     * @throws \Exception
     */
    private function resolveMetadata(
        array $reflectionProperties,
        EventObjectInterface $object
    ) {
        $metadata = [];
        /** @var \ReflectionProperty $property */
        foreach ($reflectionProperties as $property) {
            $property->setAccessible(true);
            $reader = new Reader($object, $property->getName(), 'property');

            if ($reader->getParameter($this->payloadName) !== null) {
                $event = $this->resolveEvent($reader->getParameter($this->payloadName));

                if ($event === null) {
                    continue;
                }

                $metadata[$property->getName()] = [
                    'value' => $property->getValue($object),
                    'event' => $event,
                ];
            }
        }

        $this->metadata = $metadata;
    }
    /**
     * @param string $events
     * @return string
     */
    private function resolveEvent(string $events): ?string
    {
        $eventNames = explode(',', $events);

        foreach ($eventNames as $eventName) {
            if (empty($eventName)) {
                $message = sprintf('You provided no event names for object \'%s\'', get_class($object));
                throw new \RuntimeException($message);
            }

            $eventName = trim($eventName);
            if ($eventName === $this->getName()) {
                return $eventName;
            }
        }

        return null;
    }
}