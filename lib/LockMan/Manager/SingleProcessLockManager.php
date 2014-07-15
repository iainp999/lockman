<?php
namespace LockMan\Manager;

use LockMan\LockManagerInterface;
use LockMan\LockableInterface;

/**
 * A lock manager that operates within the context of a single process.
 *
 * Mainly used for testing, but maybe there's another use-case.
 *
 * @package LockMan\Manager
 */
class SingleProcessLockManager implements LockManagerInterface {

  protected $locks = array();

  /**
   * @param LockableInterface $lockable
   * @param int $timeout
   * @return mixed
   */
  public function lock(LockableInterface $lockable, $timeout = 3600) {
    if (!$this->canLock($lockable)) {
      return FALSE;
    }
    $this->locks[$lockable->getLockName()] = time() + $timeout;
    return TRUE;
  }

  /**
   * @param LockableInterface $lockable
   * @return mixed
   */
  public function canLock(LockableInterface $lockable) {
    if (isset($this->locks[$lockable->getLockName()]) && !$this->isExpired($lockable)) {
      return FALSE;
    }
    return TRUE;
  }

  /**
   * @param LockableInterface $lockable
   * @param bool $reset
   * @return bool
   */
  protected function isExpired(LockableInterface $lockable, $reset = FALSE) {
    if (!isset($this->locks[$lockable->getLockName()])) {
      return TRUE;
    }
    elseif ($this->locks[$lockable->getLockName()] < time()) {
      if ($reset) {
        $this->release($lockable);
      }
      return TRUE;
    }
    return FALSE;
  }

  /**
   * @param LockableInterface $lockable
   * @return mixed
   */
  public function release(LockableInterface $lockable = NULL) {
    if ($lockable == NULL) {
      $this->locks = array();
      return TRUE;
    }
    unset($this->locks[$lockable->getLockName()]);
    return TRUE;
  }

} 