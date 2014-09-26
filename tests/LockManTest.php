<?php

require_once __DIR__ . "/classes/MyLockable.php";

/**
 * Tests for the LockHandler.
 *
 * @group lock
 */
class LockManTest extends PHPUnit_Framework_TestCase {

  /**
   * @type $lockHandler \LockMan\Handler\SingleProcessLockHandler
   */
  protected static $lockHandler = NULL;

  /**
   * Create the LockManager fixture.
   */
  public static function setUpBeforeClass() {
    self::$lockHandler = new \LockMan\Handler\SingleProcessLockHandler();
    parent::setUpBeforeClass();
  }

  /**
   * Test that a lock can be created.
   */
  public function testCanCreateLock() {
    $lockable = new MyLockable();

    $this->assertTrue(self::$lockHandler->lock($lockable));
    $this->assertFalse(self::$lockHandler->lock($lockable));
  }

  /**
   * @depends testCanCreateLock
   */
  public function testCanReleaseLock() {
    $lockable = new MyLockable();

    $this->assertTrue(self::$lockHandler->release($lockable));
    $this->assertTrue(self::$lockHandler->lock($lockable));
    $this->assertTrue(self::$lockHandler->release($lockable));
  }

  /**
   * @depends testCanReleaseLock
   */
  public function testCanCheckLock() {
    $lockable = new MyLockable();
    $this->assertTrue(self::$lockHandler->canLock($lockable));
    $this->assertTrue(self::$lockHandler->lock($lockable));
    $this->assertFalse(self::$lockHandler->canLock($lockable));
  }
} 