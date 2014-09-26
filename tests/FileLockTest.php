<?php

require_once __DIR__ . "/classes/MyLockable.php";

/**
 * File locking tests.
 *
 * Depends on the filesystem, so use the group annotation to exclude from
 * regular unit tests.
 *
 * @group integration
 *
 */
class FileLockTest extends PHPUnit_Framework_TestCase {

  /**
   * @type $lockHandler \LockMan\Handler\FileLockHandler
   */
  protected $lockHandler = NULL;

  protected $lockable = NULL;

  public function setUp() {
    $this->lockHandler = new \LockMan\Handler\FileLockHandler('/tmp/');
    $this->lockable = new MyLockable();
    parent::setUp();
  }

  public function testLock() {
    $this->assertTrue($this->lockHandler->lock($this->lockable));
  }

  /**
   * @depends testLock
   */
  public function testCanLock() {
    $this->assertTrue($this->lockHandler->canLock($this->lockable));
  }

  /**
   * @depends testLock
   */
  public function testCanRelease() {
    $this->assertTrue($this->lockHandler->release($this->lockable));
  }
}
