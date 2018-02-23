<?php

namespace EventStore\Event;

use DocBlockReader\Reader;

class MetadataFactory implements \IteratorAggregate
{
    /**
     * @var array $tempMetadata
     */
    private $tempMetadata = [];
    /**
     * @var string $factoryAnnotationName
     */
    private $factoryAnnotationName;
    /**
     * @var string $eventPayloadName
     */
    private $eventPayloadName;
    /**
     * MetadataFactory constructor.
     * @param object $object
     * @param string $factoryAnnotationName
     * @param string $eventPayloadName
     * @throws \Exception
     */
    public function __construct(
        $object,
        string $factoryAnnotationName,
        string $eventPayloadName
    ) {
        $this->factoryAnnotationName = $factoryAnnotationName;
        $this->eventPayloadName = $eventPayloadName;

        $classMetadata = $this->extractFactoryClassMetadata($object);

        foreach ($classMetadata['eventNames'] as $eventStoreName) {
            $this->tempMetadata[$eventStoreName] = [
                'event' => $eventStoreName,
                'object' => $object,
                'payloadName' => $classMetadata['eventPayloadName'],
            ];
        }
    }
    /**
     * @return array
     */
    public function getIterator(): array
    {
        return $this->tempMetadata;
    }
    /**
     * @param string $event
     * @param object $object
     * @param string|null $eventPayloadName
     * @return Metadata
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function create(string $event, $object, string $eventPayloadName = null): Metadata
    {
        return new Metadata($event, $object, $eventPayloadName);
    }
    /**
     * @return array
     * @throws \Exception
     * @throws \ReflectionException
     */
    public function createAll(): array
    {
        $metadataObjects = [];
        foreach ($this->tempMetadata as $key => $metadata) {
            $metadataObjects[] = $this->create(
                $metadata['event'],
                $metadata['object'],
                $metadata['payloadName']
            );
        }

        return $metadataObjects;
    }
    /**
     * @param $object
     * @return array
     * @throws \Exception
     */
    private function extractFactoryClassMetadata($object): array
    {
        $reader = new Reader($object);

        $eventNamesParameter = $reader->getParameter($this->factoryAnnotationName);

        $this->validateEventStoreParameter($eventNamesParameter);

        $unparsedEventNames = explode(',', $eventNamesParameter);

        $eventNames = $this->resolveEventClassMetadata($unparsedEventNames, $object);
        $payloadName = $this->resolveEventPayloadName($reader);

        return [
            'eventNames' => $eventNames,
            'eventPayloadName' => $payloadName,
        ];
    }
    /**
     * @param array|null $parameter
     * @throws \RuntimeException
     */
    private function validateEventStoreParameter($parameter)
    {
        if (!is_string($parameter)) {
            $message = sprintf(
                'Invalid annotations. There is no \'%s\' annotation.',
                $this->factoryAnnotationName
            );

            throw new \RuntimeException($message);
        }
    }
    /**
     * @param array $eventNames
     * @param $object
     * @throws \RuntimeException
     * @return array
     */
    private function resolveEventClassMetadata(array $eventNames, $object): array
    {
        $temp = [];
        foreach ($eventNames as $eventName) {
            if (empty($eventName)) {
                $message = sprintf('You provided no event names for object \'%s\'', get_class($object));
                throw new \RuntimeException($message);
            }

            $temp[] = trim($eventName);
        }

        return $temp;
    }
    /**
     * @param Reader $reader
     * @return string|null
     */
    private function resolveEventPayloadName(Reader $reader): ?string
    {
        return $reader->getParameter($this->eventPayloadName);
    }
}