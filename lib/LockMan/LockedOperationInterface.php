<?php
namespace LockMan;

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
   */
  public function execute(callable $operation, LockableInterface $lockable, $locktime = 3600);

  /**
   * @param LockManagerInterface $manager
   * @return mixed
   */
  public function setLockManager(LockManagerInterface $manager);

  /**
   * @return mixed
   */
  public function getLockManager();
} 