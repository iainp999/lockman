# LockMan

A generic locking library for PHP that uses named locks.

Locking is useful for controlling access to resources in a multi-process or distributed environment.

The idea is that the actual locking mechanism can be implemented in any way you like, in order to fit your technology
stack.  You would just need to implement `LockManagerInterface` for a particular technology (there is an example provided
for the Drupal CMF).

You may have some offline processing involving multiple processes and shared resources, where guarantees about exclusive
access to resources are required.  In this case, you may want to look into using a library such as this, with an
implementation of `LockManagerInterface` that is appropriate for you.

Some examples are provided.

### Example Usage

In this case, we assume that there is a class called `LockManager` which implements `LockManagerInterface`.

#### General

```
$lockManager = new LockManager();
$lockable = new MyLockable();
if ($lockManager->lock($lockable)) {
  // Some critical section code.
}
...
```

#### Locked Operation

Contrived example, you could, for example, construct many of these objects in a service container.

```
$lockManager = new LockManager();
$lockedOperation = new LockedOperation($lockManager);
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
class that implements `LockManagerInterface` could be defined in configuration and automatically injected into a
lock manager 'service'.
