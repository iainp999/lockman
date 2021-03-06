# LockMan

A generic locking library for PHP that uses named locks.

Locking is useful for controlling access to resources in a multi-process or distributed environment.

The idea is that the actual locking mechanism can be implemented in any way you like, in order to fit your technology
stack.  You would just need to implement `LockHandlerInterface` for a particular technology (there are example lock handlers provided for the local filesystem and the Drupal locking API).

You may have some offline processing involving multiple processes and shared resources, where guarantees about exclusive
access to resources are required.  In this case, you may want to look into using a library such as this, with an
implementation of `LockHandlerInterface` that is appropriate for you.

See the examples of `LockHandlerInterface` implementations in `lib/LockMan/Handler`.

### Example Usage

In this case, we assume that there is a class called `LockHandler` which implements `LockHandlerInterface`.

#### General

N.B. in reality, I would recommend using some configurable service/DI container for creating your objects.

```
$lockHandler = new LockHandler();
$lockable = new MyLockable();
if ($lockHandler->lock($lockable)) {
  // Some critical section code.
}
...
```

#### Locked Operation

You don't have to use this class, it's simply provided as a convenience for operations that require a lock to be acquired prior to their execution.  The lock is released when the operation finishes; if an Exception occurs then the lock is released before the Exception is rethrown.  An operation is any PHP callable.

The current implementation does not support the acquisition of multiple locks prior to execution.  This may be supported in a future version.

What follows is a contrived example, you could, for example, construct many of these objects in a service container ('contrived' in that you probably wouldn't create all of these objects inline, as in this example).

```
$lockHandler = new LockHandler();
$lockedOperation = new LockedOperation($lockHandler);
$lockable = new MyLockable();
$myOperation = array('SomeClass', 'someMethod');
// Execute the locked operation.
try {
   $lockedOperation->execute($myOperation, $lockable);
}
catch (Exception $ex) {
   // handle exception.
}
```

One important thing to note here is that the $lockedOperation object can be re-used once created.  The actual lock is
defined by the instance of `LockableInterface` that is injected when the operation is executed.

The instance could be used, for example, inside a service container (such as the Symfony component) where the
class that implements `LockHandlerInterface` could be defined in configuration and automatically injected into a
lock handler 'service'.

## Badges

Travis CI : [![Build Status](https://travis-ci.org/iainp999/lockman.svg?branch=master)](https://travis-ci.org/iainp999/lockman)
Coveralls : [![Coverage Status](https://coveralls.io/repos/iainp999/lockman/badge.png?branch=master)](https://coveralls.io/r/iainp999/lockman?branch=master)
