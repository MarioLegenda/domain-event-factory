<?php

namespace Test;

use EventStore\Event\Event;
use EventStore\DomainEventFactory;
use Faker\Factory;
use PHPUnit\Framework\TestCase;
use Test\Model\ConfigurableAnnotationNameUser;
use Test\Model\User;

class DomainEventFactoryTest extends TestCase
{
    public function test_event_store_getEvents()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $eventStore = new DomainEventFactory();

        $this->assertObjectStorage($eventStore);

        $eventStore->createMetadata($user1);
        $eventStore->createMetadata($user2);

        static::assertTrue($eventStore->hasEvent('user_created'));
        static::assertTrue($eventStore->hasEvent('user_updated'));
        static::assertFalse($eventStore->hasEvent('invalid_event'));

        $events = $eventStore->getEvents();

        static::assertEquals(2, count($events));

        $eventNames = ['user_created', 'user_updated'];

        foreach ($eventNames as $eventName) {
            static::assertTrue($events->has($eventName));

            $extractedEvents = $events->get($eventName);

            static::assertInternalType('array', $extractedEvents);
            static::assertNotEmpty($extractedEvents);
            static::assertEquals(2, count($extractedEvents));
            static::assertContainsOnlyInstancesOf(Event::class, $extractedEvents);
        }
    }

    public function test_event_store_getEvent()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $eventStore = new DomainEventFactory();

        $this->assertObjectStorage($eventStore);

        $eventStore->createMetadata($user1);
        $eventStore->createMetadata($user2);

        $entered = 0;
        foreach ([$user1, $user2] as $user) {
            $events = $eventStore->getEvent('user_created');

            /** @var Event $event */
            foreach ($events as $event) {
                if ($event->getObjectHash() === spl_object_hash($user)) {
                    ++$entered;

                    $payload = $event->getPayload();

                    static::assertArrayNotHasKey('updatedAt', $payload);
                    static::assertArrayHasKey('createdAt', $payload);

                    static::assertEquals($payload['name'], $user->getName());
                    static::assertEquals($payload['lastname'], $user->getLastname());
                    static::assertEquals($payload['email'], $user->getEmail());
                    static::assertEquals($payload['username'], $user->getUsername());
                }
            }

            $events = $eventStore->getEvent('user_updated');

            /** @var Event $event */
            foreach ($events as $event) {
                if ($event->getObjectHash() === spl_object_hash($user)) {
                    ++$entered;

                    $payload = $event->getPayload();

                    static::assertArrayHasKey('updatedAt', $payload);
                    static::assertArrayHasKey('createdAt', $payload);

                    static::assertEquals($payload['name'], $user->getName());
                    static::assertEquals($payload['lastname'], $user->getLastname());
                    static::assertEquals($payload['email'], $user->getEmail());
                    static::assertEquals($payload['username'], $user->getUsername());
                }
            }
        }

        static::assertEquals(4, $entered);
    }

    public function test_event_store_iteration()
    {
        $user1 = $this->createUser();
        $user2 = $this->createUser();

        $eventStore = new DomainEventFactory();

        $eventStore
            ->createMetadata($user1)
            ->createMetadata($user2);

        $this->assertObjectStorage($eventStore);

        foreach ($eventStore as $events) {
            static::assertInternalType('array', $events);
            static::assertNotEmpty($events);
            static::assertContainsOnlyInstancesOf(Event::class, $events);
        }
    }

	public function test_event_factory_configurable_payload_annotation_name()
	{
        $faker = Factory::create();

        $user1 = $this->createConfigurableAnnotationNameUser();
        $user2 = $this->createConfigurableAnnotationNameUser();

        $eventStore = new DomainEventFactory();

        $this->assertObjectStorage($eventStore);

        $eventStore->createMetadata($user1);
        $eventStore->createMetadata($user2);

        static::assertTrue($eventStore->hasEvent('user_created'));
        static::assertTrue($eventStore->hasEvent('user_updated'));
        static::assertFalse($eventStore->hasEvent('invalid_event'));

        $events = $eventStore->getEvents();

        static::assertEquals(2, count($events));

        $eventNames = ['user_created', 'user_updated'];

        foreach ($eventNames as $eventName) {
            static::assertTrue($events->has($eventName));

            $extractedEvents = $events->get($eventName);

            static::assertInternalType('array', $extractedEvents);
            static::assertNotEmpty($extractedEvents);
            static::assertEquals(2, count($extractedEvents));
            static::assertContainsOnlyInstancesOf(Event::class, $extractedEvents);
        }
	}
    /**
     * @param DomainEventFactory $eventStore
     * @throws \Exception
     */
    private function assertObjectStorage(DomainEventFactory $eventStore)
    {
        $enteredException = false;
        try {
            $eventStore->createMetadata(User::class);
        } catch (\Exception $e) {
            $enteredException = true;
        }

        static::assertTrue($enteredException);
    }
    /**
     * @return User
     */
    private function createUser(): User
    {
        $faker = Factory::create();

        $user = new User();

        $user->setName($faker->name);
        $user->setLastname($faker->lastName);
        $user->setEmail($faker->email);
        $user->setUsername($faker->userName);

        return $user;
    }
    /**
     * @return ConfigurableAnnotationNameUser
     */
    private function createConfigurableAnnotationNameUser(): ConfigurableAnnotationNameUser
    {
        $faker = Factory::create();

        $user = new ConfigurableAnnotationNameUser();

        $user->setName($faker->name);
        $user->setLastname($faker->lastName);
        $user->setEmail($faker->email);
        $user->setUsername($faker->userName);

        return $user;
    }
}
