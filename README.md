### Introduction

This tool makes handling events from domain object easy with annotations.

### Requirements

`PHP 7.1` (Created on 7.2)

### Installing

`composer require "mario-legenda/event-store"`

### Usage

There are two type of important annotations:

1. `EventStore`

The annotation goes right above the class name and contains 
names of events that you want to collect

2. `EventPayload`

The annotation goes right above the property name and contains
the event name(s) that this payload is associated with

### Examples

```
/**
* @EventStore user_created, user_updated
*
* IMPORTANT: 
* There has to be a single space between the @EventStore 
* annotation and the list of event(s)
*/

use EventStore\EventStore;

class User 
{
    /**
    * @EventPayload user_created, user_updated
    */
    private $name;
    /**
    * @EventPayload user_created, user_updated
    */
    private $lastname;
    /**
    * @EventPayload user_created, user_updated
    */
    private $createdAt;
    /**
    * @EventPayload user_updated
    */
    private $updatedAt;
    
    public function __construct(
        string $name,
        string $lastname
    ) {
        $this->name = $name;
        $this->lastname = $lastname;
        $this->createdAt = (new \DateTime())->format('Y-m-d');
    }
}

$eventStore = new EventStore();

// You can also use a singleton instance EventStore::construct()

$user1 = new User('Name', 'Lastname');
$user2 = new User('Name', 'Lastname');

$eventStore->store($user1);
$eventStore->store($user2);

// $eventStore also supports Fluent Interface so you can use...

$eventStore
    ->store($user1)
    ->store($user2);
    
$events = $eventStore->getEvents();

```

`$events` variable is an instance of `EventStore\Event\EventCollection` object
which is a collection (array) of `Event` objects sorted by event name.

For example, the example above stored two user object. Therefor,
it will contain an array with two keys, `user_created` and `user_updated`.

Each key will contain all `Event` objects associated with this event name.

```php


/** @var array $event */
foreach ($events as $event) {
    /** @var Event $event */
    foreach ($event as $e) {
        $payload = $e->getPayload();
        // or $e->toArray()
        // or json_encode($event);
    }
}

IMPORTANT:

Observe that $event is an array. This array contains all events associated with 
an event name. In our example above, since we stored two User objects,
it will contain a key user_created with two Event objects in it.

```

If you wish to see more methods that this tool supports, take a look
at `EventStore\Event\EventCollection`, `EventStore\Event\Event` and 
`EventStore\EventStore` objects. All methods that you can use are public
and self explanatory.



