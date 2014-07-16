<?php
namespace LockMan\Manager;

use LockMan\LockableInterface;
use LockMan\LockManagerInterface;

/**
 * Uses the filesystem to manage locks.
 *
 * @package LockMan\Manager
 */
class FileLockManager implements LockManagerInterface {

  protected $directory = '/tmp/';
  protected $suffix = '.lock';
  protected $pattern = "{DIR}{NAME}{SUFFIX}";

  protected $lockFiles = array();

  /**
   * @param $directory
   * @param string $suffix
   */
  function __construct($directory = '/tmp/', $suffix = '.lock') {
    $this->setDirectory($directory);
    $this->setSuffix($suffix);
  }

  /**
   * Set the directory that stores the lock files.
   *
   * @param string $directory
   */
  public function setDirectory($directory) {

    $this->directory = $directory;
  }

  /**
   * Get the directory that stores the lock files.
   *
   * @return string
   */
  public function getDirectory() {
    return $this->directory;
  }

  /**
   * Set the file lock suffix.
   *
   * @param string $suffix
   */
  public function setSuffix($suffix) {
    $this->suffix = $suffix;
  }

  /**
   * Get the file lock suffix.
   *
   * @return string
   */
  public function getSuffix() {
    return $this->suffix;
  }


  /**
   * Lock the supplied Lockable.
   *
   * @param LockableInterface $lockable
   * @param int $timeout
   * @return mixed
   */
  public function lock(LockableInterface $lockable, $timeout = 3600) {
    if (!$this->canLock($lockable)) {
      return FALSE;
    }

    $filename = $this->getFileName($lockable->getLockName());

    // If we already own the lock, no need to go any further.
    if ($this->ownLock($filename)) {
      $this->lockFiles[] = $filename;
      // The lock should only last the length of the request.
      register_shutdown_function(array($this, 'release'));
      return TRUE;
    }

    //TODO if the lock is held, but timed out, delete file.

    if ($this->writeFile($lockable)) {
      $this->lockFiles[] = $filename;
      // The lock should only last the length of the request.
      register_shutdown_function(array($this, 'release'));
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Test if the Lockable can be locked.
   *
   * @param LockableInterface $lockable
   * @return mixed
   */
  public function canLock(LockableInterface $lockable) {
    $filename = $this->getFileName($lockable->getLockName());

    if (!file_exists($filename)) {
      return TRUE;
    }

    if (file_exists($filename)) {
      if ($this->ownLock($filename)) {
        return TRUE;
      }

      //TODO check if lock is timed out, return TRUE if it is.
    }

    return FALSE;
  }

  /**
   * Release a lock.
   *
   * @param LockableInterface|NULL $lockable
   *  The lockable to release.  If NULL, release all locks.
   * @return boolean
   *  TRUE if the lockable was released, FALSE otherwise.
   */
  public function release(LockableInterface $lockable = NULL) {
    if ($lockable == NULL) {
      foreach ($this->lockFiles as $filename) {
        $this->deleteFile($filename);
      }
      return TRUE;
    }

    $filename = $this->getFileName($lockable->getLockName());

    if ($this->ownLock($filename)) {
      return $this->deleteFile($filename);
    }

    return FALSE;
  }

  /**
   * Generate a filename based on the supplied Lockable.
   *
   * @param $lock_name
   * @return mixed
   */
  protected function getFileName($lock_name) {
    $filename = str_replace("{DIR}", $this->getDirectory(), $this->pattern);
    $filename = str_replace("{NAME}", $lock_name, $filename);
    $filename = str_replace("{SUFFIX}", $this->getSuffix(), $filename);

    return $filename;
  }

  /**
   * Check if the current process owns the lock defined in the supplied file.
   *
   * @param $filename
   * @return bool
   */
  protected function ownLock($filename) {
    if (!file_exists($filename)) {
      return FALSE;
    }

    $owner = file_get_contents($filename);
    if (trim($owner) == getmypid()) {
      // This process already owns the lock.
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Write a PID to a file.
   *
   * @param $filename
   * @param $pid
   * @return bool
   */
  protected function writeFile(LockableInterface $lockable) {
    $bytes = file_put_contents($this->getFileName($lockable->getLockName()), getmypid());

    if (!empty($bytes)) {
      return TRUE;
    }

    return FALSE;
  }

  /**
   * Delete a file.
   *
   * @param $filename
   */
  protected function deleteFile($filename) {
    if (!file_exists($filename)) {
      return TRUE;
    }

    return unlink($filename);
  }
}
