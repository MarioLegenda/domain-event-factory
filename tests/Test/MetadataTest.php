<?php

namespace Test;

use EventStore\Event\Metadata;
use EventStore\Event\MetadataFactory;
use PHPUnit\Framework\TestCase;
use Faker\Factory;
use Test\Model\User;

class MetadataTest extends TestCase
{
    public function test_metadata()
    {
        $faker = Factory::create();

        $user = new User();

        $user->setName($faker->name);
        $user->setLastname($faker->lastName);
        $user->setEmail($faker->email);
        $user->setUsername($faker->userName);

        $metadataFactory = new MetadataFactory(
            $user,
            'DomainEventFactory',
            'EventPayloadName'
        );

        $metadataObjects = $metadataFactory->createAll();

        static::assertEquals(2, count($metadataObjects));
        static::assertContainsOnlyInstancesOf(Metadata::class, $metadataObjects);

        $events = ['user_created', 'user_updated'];

        /** @var Metadata $metadataObject */
        foreach ($metadataObjects as $metadataObject) {
            static::assertContains($metadataObject->getName(), $events);
        }
    }
}