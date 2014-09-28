<?php
use LockMan\Operation\LockedOperation;

require_once __DIR__ . "/classes/Foo.php";
require_once __DIR__ . "/classes/MyLockable.php";
require_once __DIR__ . "/classes/ExceptionLockHandler.php";

/**
 * LockedOperation tests.
 *
 * @group lock
 */
class LockedOperationTest extends PHPUnit_Framework_TestCase {
  /**
   * @type $lockHandler \LockMan\LockHandlerInterface
   */
  protected static $lockHandler;

  public static function setUpBeforeClass() {
    self::$lockHandler = new \LockMan\Handler\SingleProcessLockHandler();
    parent::setUpBeforeClass();
  }

  /**
   * Test that a locked operation can execute as expected.
   */
  public function testLockedOperation() {
    $lockedOperation = new LockedOperation(self::$lockHandler);
    $lockable = new MyLockable();
    $op = function() {
      return TRUE;
    };
    $result = $lockedOperation->execute($op, $lockable);

    $this->assertTrue(self::$lockHandler->canLock($lockable));
    $this->assertTrue($result);
  }

  /**
   *
   */
  public function testLockedOperationException() {
    $lockedOperation = new LockedOperation(self::$lockHandler);
    $lockable = new MyLockable();
    $op = function() {
      throw new Exception("Something went wrong.");
    };

    $result = FALSE;

    try {
      $result = $lockedOperation->execute($op, $lockable);
    }
    catch (Exception $ex) {
      // We should get an exception here.
    }

    $this->assertTrue(isset($ex));
    $this->assertTrue(self::$lockHandler->canLock($lockable));
    $this->assertFalse($result);
  }

  /**
   * Test the locked operation works for method calls.
   */
  public function testLockedMethod() {
    $lockedOperation = new LockedOperation(self::$lockHandler);
    $lockable = new MyLockable();
    $foo = new Foo();

    $result = FALSE;

    try {
      $result = $lockedOperation->execute(array($foo, 'bar'), $lockable);
    }
    catch (Exception $ex) {
      // this shouldn't happen.
    }

    $this->assertTrue($result);
    $this->assertFalse(isset($ex));
  }

  /**
   * Test the case where a lock could not be released.
   *
   * Check that the result was returned and that
   */
  public function testException() {
    $handler = new ExceptionLockHandler();
    $lockedOperation = new LockedOperation($handler);
    $lockable = new MyLockable();
    $op = function() {
      return TRUE;
    };

    $result = FALSE;
    $threw = FALSE;

    try {
      $lockedOperation->execute($op, $lockable);
    }
    catch(\LockMan\Operation\LockReleaseException $e) {
      $threw = TRUE;
      $operation = $e->getLockedOperation();
    }

    $this->assertTrue($threw);
    $this->assertTrue($operation->getResult());
    $this->assertTrue($operation->isFinished());
  }

  public function testModifiedResult() {
    $lockedOperation = new LockedOperation(self::$lockHandler, 'changed_result');
    $op = function() {
      //TODO think about this use case.
    };

    $result = $lockedOperation->execute($op, new MyLockable());
  }
}
