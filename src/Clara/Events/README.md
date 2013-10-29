Clara\Events
============

This is the events/observer code of Clara


Overview
--------

Events in Clara can occur at any time, in any class, and have three defining characteristics:

1. Name (developer-defined string, e.g. 'user.login.success')
2. Subject (object where the event took place)
3. Context (relevant data that's not the subject)

***See `\Clara\Events\Event::__construct` for more information.***

Although events can be created & triggered anywhere, they are only captured when an observer (`\Clara\Events\Observer`) is attached to an observable (`\Clara\Events\Observable`) object. The observable object can then call `fire()` to fire off events to any registered listeners, who will handle the event however they were coded to.



**TODO: finish this**