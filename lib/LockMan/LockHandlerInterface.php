<?php
namespace LockMan;
use LockMan\Exception\LockReleaseException;

/**
 * Handles locking.
 *
 * @package LockMan
 */
interface LockHandlerInterface {

  /**
   * Lock the supplied Lockable.
   *
   * @param LockableInterface $lockable
   * @param int $timeout
   * @return mixed
   */
  public function lock(LockableInterface $lockable, $timeout = 3600);

  /**
   * Test if the Lockable can be locked.
   *
   * @param LockableInterface $lockable
   * @return mixed
   */
  public function canLock(LockableInterface $lockable);

  /**
   * Release a lock.
   *
   * @param LockableInterface|NULL $lockable
   *  The lockable to release.  If NULL, release all locks.
   * @return boolean
   *  TRUE if the lockable was released, FALSE otherwise.
   *
   */
  public function release(LockableInterface $lockable = NULL);

} 