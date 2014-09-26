<?php
namespace LockMan\Handler;

use LockMan\LockableInterface;
use LockMan\LockHandlerInterface;

/**
 * Lock handler that uses the Drupal locking API.
 *
 * @package LockMan\Handler
 */
class DrupalLockHandler implements LockHandlerInterface {

  /**
   * Attempt to lock the supplied Lockable.
   *
   * @param LockableInterface $lockable
   * @param int $timeout
   * @return boolean
   */
  public function lock(LockableInterface $lockable, $timeout = 3600) {
    return lock_acquire($lockable->getLockName(), $timeout);

  }

  /**
   * Check if the supplied Lockable can be locked.
   *
   * @param LockableInterface $lockable
   * @return boolean
   */
  public function canLock(LockableInterface $lockable) {
    return lock_may_be_available($lockable->getLockName());
  }

  /**
   * Release the supplied Lockable, or all locks.
   *
   * @param LockableInterface $lockable
   * @return mixed
   */
  public function release(LockableInterface $lockable = NULL) {
    if ($lockable == NULL) {
      lock_release_all();
      return;
    }

    lock_release($lockable->getLockName());
  }

}
