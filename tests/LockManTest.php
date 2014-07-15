<?php

require_once __DIR__ . "/classes/MyLockable.php";

/**
 * Tests for the LockManager.
 */
class LockManTest extends PHPUnit_Framework_TestCase {

  /**
   * @type $lockManager \LockMan\Manager\SingleProcessLockManager
   */
  protected static $lockManager = NULL;

  /**
   * Create the LockManager fixture.
   */
  public static function setUpBeforeClass() {
    self::$lockManager = new \LockMan\Manager\SingleProcessLockManager();
    parent::setUpBeforeClass();
  }

  /**
   * Test that a lock can be created.
   */
  public function testCanCreateLock() {
    $lockable = new MyLockable();

    $this->assertTrue(self::$lockManager->lock($lockable));
    $this->assertFalse(self::$lockManager->lock($lockable));
  }

  /**
   * @depends testCanCreateLock
   */
  public function testCanReleaseLock() {
    $lockable = new MyLockable();

    $this->assertTrue(self::$lockManager->release($lockable));
    $this->assertTrue(self::$lockManager->lock($lockable));
    $this->assertTrue(self::$lockManager->release());
  }

  /**
   * @depends testCanReleaseLock
   */
  public function testCanCheckLock() {
    $lockable = new MyLockable();
    $this->assertTrue(self::$lockManager->canLock($lockable));
    $this->assertTrue(self::$lockManager->lock($lockable));
    $this->assertFalse(self::$lockManager->canLock($lockable));
  }
} 