<?php

class MyLockable implements \LockMan\LockableInterface {

  /**
   * Get the name used to lock this object.
   *
   * @return string
   */
  public function getLockName() {
    return "my_lock";
  }

} 