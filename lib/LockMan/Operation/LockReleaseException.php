<?php

namespace LockMan\Operation;

use LockMan\LockedOperationInterface;

/**
 * A lock could not be released during execution of a LockedOperationInterface.
 *
 * If the operation succeeded then the exception includes the result of the operation.
 *
 * @package LockMan\Exception
 */
class LockReleaseException extends \Exception {

  private $lockedOperation = NULL;

  /**
   * Construct a new LockReleaseException.
   *
   * @param LockedOperationInterface $operation
   */
  function __construct(LockedOperationInterface $operation) {
    $this->lockedOperation = $operation;
  }

  /**
   * Get the associated locked operation.
   *
   * @return LockedOperationInterface
   */
  public function getLockedOperation() {
    return $this->lockedOperation;
  }

}