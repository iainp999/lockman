<?php
namespace LockMan\Operation;

use LockMan\LockableInterface;
use LockMan\LockedOperationInterface;
use LockMan\LockManagerInterface;

/**
 *
 * @package LockMan\Operation
 */
class LockedOperation implements LockedOperationInterface {

  /**
   * @type LockManagerInterface
   */
  protected $lockManager = NULL;

  /**
   * Construct a new instance of this class, injecting the lock manager.
   *
   * @param LockManagerInterface $lockManager
   */
  public function __construct(LockManagerInterface $lockManager) {
    $this->setLockManager($lockManager);
  }

  /**
   * Executes an operation within the context of the supplied lockable object.
   *
   * This function ensures that the lockable is locked before calling the supplied callable.  The lock
   * is only guaranteed for the time specified.
   *
   * @param callable $operation
   * @param LockableInterface $lockable
   * @param int $locktime
   * @return mixed
   *  Should return the result of the callable.
   */
  public function execute(callable $operation, LockableInterface $lockable, $locktime = 3600) {
    if (!$this->lockManager->lock($lockable, $locktime)) {
      throw new \InvalidArgumentException();
    }

    $result = NULL;

    try {
      $result = $operation();
    }
    catch (\Exception $operation_exception) {
      // Catch all sub-classes of Exception to be rethrown after
      // the current lockable is released.
    }

    if (!$this->lockManager->release($lockable)) {
      // could not release lock.
      //TODO determine how to report this since the operation may have still succeeded.
    }

    // If an exception was thrown, rethrow now after the lock was released.
    if (isset($operation_exception)) {
      throw $operation_exception;
    }

    return $result;
  }

  /**
   * Set the lock manager to be used during this operation.
   *
   * @param LockManagerInterface $manager
   * @return mixed
   */
  public function setLockManager(LockManagerInterface $manager) {
    $this->lockManager = $manager;
    return $this;
  }

  /**
   * Get the lock manager that is currently in use.
   *
   * @return mixed
   */
  public function getLockManager() {
    return $this->lockManager;
  }

}