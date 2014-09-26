<?php
namespace LockMan\Operation;

use LockMan\LockableInterface;
use LockMan\LockedOperationInterface;
use LockMan\LockHandlerInterface;

/**
 *
 * @package LockMan\Operation
 */
class LockedOperation implements LockedOperationInterface {

  /**
   * @type LockHandlerInterface
   */
  protected $lockHandler = NULL;

  /**
   * Construct a new instance of this class, injecting the lock handler.
   *
   * @param LockHandlerInterface $lockHandler
   */
  public function __construct(LockHandlerInterface $lockHandler) {
    $this->setLockHandler($lockHandler);
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
    if (!$this->lockHandler->lock($lockable, $locktime)) {
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

    if (!$this->lockHandler->release($lockable)) {
      // Could not release lock.  Throw an exception that includes
      // the result but which indicates that the lock release failed.
      $operation_exception = new \LockMan\Exception\LockReleaseException($result);
    }

    // If an exception was thrown, rethrow now after the lock was released.
    if (isset($operation_exception)) {
      throw $operation_exception;
    }

    return $result;
  }

  /**
   * Set the lock handler to be used during this operation.
   *
   * @param LockHandlerInterface $handler
   * @return mixed
   */
  public function setLockHandler(LockHandlerInterface $handler) {
    $this->lockHandler = $handler;
    return $this;
  }

  /**
   * Get the lock handler that is currently in use.
   *
   * @return mixed
   */
  public function getLockHandler() {
    return $this->lockHandler;
  }

}