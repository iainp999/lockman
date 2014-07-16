<?php
use LockMan\Operation\LockedOperation;

require_once __DIR__ . "/classes/Foo.php";
require_once __DIR__ . "/classes/MyLockable.php";

/**
 * LockedOperation tests.
 *
 * @group lock
 */
class LockedOperationTest extends PHPUnit_Framework_TestCase {
  /**
   * @type $lockManager \LockMan\LockManagerInterface
   */
  protected static $lockManager;

  public static function setUpBeforeClass() {
    self::$lockManager = new \LockMan\Manager\SingleProcessLockManager();
    parent::setUpBeforeClass();
  }

  /**
   * Test that a locked operation can execute as expected.
   */
  public function testLockedOperation() {
    $lockedOperation = new LockedOperation(self::$lockManager);
    $lockable = new MyLockable();
    $op = function() {
      return TRUE;
    };
    $result = $lockedOperation->execute($op, $lockable);

    $this->assertTrue(self::$lockManager->canLock($lockable));
    $this->assertTrue($result);
  }

  /**
   *
   */
  public function testLockedOperationException() {
    $lockedOperation = new LockedOperation(self::$lockManager);
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
    $this->assertTrue(self::$lockManager->canLock($lockable));
    $this->assertFalse($result);
  }

  /**
   * Test the locked operation works for method calls.
   */
  public function testLockedMethod() {
    $lockedOperation = new LockedOperation(self::$lockManager);
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
} 