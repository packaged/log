<?php

namespace Packaged\Log\Tests;

use PHPUnit\Framework\TestCase;

abstract class AbstractLoggerTestCase extends TestCase
{
  private $_tempFile;

  public function setUp()
  {
    $this->_tempFile = tempnam(sys_get_temp_dir(), 'packaged-log-');
    ini_set('error_log', $this->_tempFile);
  }

  public function tearDown()
  {
    unlink($this->_tempFile);
  }

  protected function _getLogContents()
  {
    return file_get_contents($this->_tempFile);
  }

  public function assertLastLog($test)
  {
    $this->assertStringEndsWith($test . PHP_EOL, $this->_getLogContents());
  }
}
