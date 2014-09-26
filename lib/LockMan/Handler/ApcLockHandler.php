<?php
namespace LockMan\Handler;

use LockMan\LockableInterface;
use LockMan\LockHandlerInterface;

/**
 * Lock handler that uses APC.
 *
 * @package LockMan\Handler
 */
class ApcLockHandler implements LockHandlerInterface {
  /**
   * Lock the supplied Lockable.
   *
   * @param LockableInterface $lockable
   * @param int $timeout
   * @return mixed
   */
  public function lock(LockableInterface $lockable, $timeout = 3600) {
    return apc_add($lockable->getLockName(), TRUE, $timeout);
  }

  /**
   * Test if the Lockable can be locked.
   *
   * @param LockableInterface $lockable
   * @return mixed
   */
  public function canLock(LockableInterface $lockable) {
    if (apc_fetch($lockable->getLockName())) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Release a lock.
   *
   * @param LockableInterface|NULL $lockable
   *  The lockable to release.  If NULL, release all locks.
   * @return boolean
   *  TRUE if the lockable was released, FALSE otherwise.
   */
  public function release(LockableInterface $lockable = NULL) {
    return apc_delete($lockable->getLockName());
  }

} 