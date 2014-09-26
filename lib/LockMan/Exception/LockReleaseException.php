<?php

namespace LockMan\Exception;

/**
 * A lock could not be released during execution of a LockedOperationInterface.
 *
 * If the operation succeeded then the exception includes the result of the operation.
 *
 * @package LockMan\Exception
 */
class LockReleaseException extends \Exception {

  private $result = NULL;

  function __construct($result) {
    $this->result = $result;
  }

  /**
   * @return null
   */
  public function getResult() {
    return $this->result;
  }

}