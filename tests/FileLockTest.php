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
   * @type $lockManager \LockMan\Manager\FileLockManager
   */
  protected $lockManager = NULL;

  protected $lockable = NULL;

  public function setUp() {
    $this->lockManager = new \LockMan\Manager\FileLockManager('/tmp/');
    $this->lockable = new MyLockable();
    parent::setUp();
  }

  public function testLock() {
    $this->assertTrue($this->lockManager->lock($this->lockable));
  }

  /**
   * @depends testLock
   */
  public function testCanLock() {
    $this->assertTrue($this->lockManager->canLock($this->lockable));
  }

  /**
   * @depends testLock
   */
  public function testCanRelease() {
    $this->assertTrue($this->lockManager->release($this->lockable));
  }
}
