<?php
namespace LockMan;

use LockMan\Operation\LockReleaseException;

/**
 * An operation that requires a lock to be acquired before execution.
 *
 * @package LockMan
 */
interface LockedOperationInterface {

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
   * @throws LockReleaseException
   */
  public function execute(callable $operation, LockableInterface $lockable, $locktime = 3600);

  /**
   * Set the LockHandlerInterface instance to control locking for this operation.
   *
   * @param LockHandlerInterface $handler
   * @return mixed
   */
  public function setLockHandler(LockHandlerInterface $handler);

  /**
   * Get a reference to the LockHandlerInterface used to control locking for this operation.
   *
   * @return LockHandlerInterface
   */
  public function getLockHandler();

  /**
   * Get the result of the operation.
   *
   * Callers should also make use of the 'isFinished()' method to check that the operation
   * executed successfully.
   *
   * @return mixed
   */
  public function getResult();

  /**
   * Check if the operation finished.
   *
   * @return boolean
   */
  public function isFinished();
} 