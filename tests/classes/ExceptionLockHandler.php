<?php

use LockMan\LockableInterface;

class ExceptionLockHandler implements \LockMan\LockHandlerInterface {
  /**
   * Lock the supplied Lockable.
   *
   * @param \LockMan\LockableInterface $lockable
   * @param int $timeout
   * @return mixed
   */
  public function lock(LockableInterface $lockable, $timeout = 3600) {
    return TRUE;
  }

  /**
   * Test if the Lockable can be locked.
   *
   * @param \LockMan\LockableInterface $lockable
   * @return boolean
   */
  public function canLock(LockableInterface $lockable) {
    return TRUE;
  }

  /**
   * Release a lock.
   *
   * @param \LockMan\LockableInterface|NULL $lockable
   *  The lockable to release.  If NULL, release all locks.
   * @return boolean
   *  TRUE if the lockable was released, FALSE otherwise.
   *
   */
  public function release(LockableInterface $lockable = NULL) {
    return FALSE;
  }

} 