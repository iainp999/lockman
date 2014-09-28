<?php
namespace LockMan\Operation;

use LockMan\LockableInterface;
use LockMan\LockedOperationInterface;
use LockMan\LockHandlerInterface;

/**
 * An operation that requires a lock to be acquired on an associated 'LockableInterface'
 * prior to execution.  The lock will be released after execution.
 *
 * The operation is a PHP Callable.
 *
 * @package LockMan\Operation
 */
class LockedOperation implements LockedOperationInterface {

  /**
   * Whether the operation has executed.
   *
   * @type bool
   */
  private $finished = FALSE;

  /**
   * The result of the operation, if it executed.  The initial value of this
   * field can be modified by the caller on construction.
   *
   * @type mixed
   */
  private $result = NULL;

  /**
   * @type LockHandlerInterface
   */
  protected $lockHandler = NULL;

  /**
   * Construct a new instance of this class, injecting the lock handler.
   *
   * As a convenience, it is possible to set a default 'result' value at this
   * point too, since the usual NULL might not be desirable.
   *
   * @param LockHandlerInterface $lockHandler
   * @param $default_result
   */
  public function __construct(LockHandlerInterface $lockHandler, $default_result = NULL) {
    $this->setLockHandler($lockHandler);
    $this->result = $default_result;
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

    try {
      $this->result = $operation();
    }
    catch (\Exception $operation_exception) {
      // Catch all sub-classes of Exception to be rethrown later, after
      // the current lockable is released.
    }

    $this->finished = TRUE;

    if (!$this->releaseLockable($lockable)) {
      // Could not release lock.  Throw an exception that includes
      // the result but which indicates that the lock release failed.
      $operation_exception = new \LockMan\Operation\LockReleaseException($this);
    }

    // If an exception was thrown, rethrow now after the lock was released.
    if (isset($operation_exception)) {
      throw $operation_exception;
    }

    return $this->result;
  }

  /**
   * Release the lock on the LockableInterface.
   *
   * @param LockableInterface $lockable
   * @return bool
   */
  protected function releaseLockable(LockableInterface $lockable) {
    return $this->lockHandler->release($lockable);
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

  /**
   * @return null
   */
  public function getResult() {
    return $this->result;
  }

  /**
   * Check if the operation finished.
   *
   * For example, it could be possible that the operation executed, but a
   * lock was unable to release.  In this case, it's handy to be able to check
   * that the operation completed.
   *
   * @return boolean
   */
  public function isFinished() {
    return $this->finished;
  }

}