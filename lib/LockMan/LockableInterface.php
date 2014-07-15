<?php
namespace LockMan;

/**
 * An object that can be locked.
 *
 * The lock, in this case, is identified by the string supplied by the Lockable.  So the
 * Lockable supplies the name which is used to lock it, via the getLockName() method.
 *
 * @package LockMan
 */
interface LockableInterface {

  /**
   * Get the name used to lock this object.
   *
   * @return string
   */
  public function getLockName();

} 