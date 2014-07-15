# LockMan

A generic locking library for PHP that uses named locks.

Locking is useful for controlling access to resources in a distributed environment.

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

`
$lockManager = new LockManager();
$lockable = new MyLockable();
if ($lockManager->lock($lockable)) {
  // Some critical section code.
}
...
`

#### Locked Operation

Contrived example, you could, for example, construct many of these objects in a service container.

`
$lockManager = new LockManager();
$lockedOperation = new LockedOperation($lockManager);
$lockable = new MyLockable();
$myOperation = array('SomeClass', 'someMethod');
// Execute the locked operation.
try {
   $myOperation->execute($myOperation, $lockable);
}
catch (Exception $ex) {
   // handle exception.
}
`
