<?php
namespace Packaged\Log\Tests;

use Exception;
use Packaged\Log\BasicGoogleCloudLogger;
use Packaged\Log\ErrorLogLogger;
use Packaged\Log\Log;
use Packaged\Log\StdOutLogger;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class StdOutLoggerTest extends TestCase
{
  private $_tempFile;
  private $_handler;

  public function setUp()
  {
    $this->_tempFile = tempnam(sys_get_temp_dir(), 'packaged-log-');
    $this->_handler = fopen($this->_tempFile, 'wb');
  }

  public function tearDown()
  {
    fclose($this->_handler);
    unlink($this->_tempFile);
  }

  protected function _getLogContents()
  {
    return file_get_contents($this->_tempFile);
  }

  public function assertLastLog($test)
  {
    self::assertStringEndsWith($test . PHP_EOL, $this->_getLogContents());
  }

  private function _getTestLogger($maxLevel = LogLevel::DEBUG)
  {
    $l = new StdOutLogger($maxLevel);
    $l->setHandle($this->_handler);
    return $l;
  }

  public function testErrorLogLogger()
  {
    Log::bind($this->_getTestLogger());

    Log::debug('debug: test');
    self::assertLastLog('debug: test');

    Log::info('info: test');
    self::assertLastLog('info: test');

    Log::notice('notice: test');
    self::assertLastLog('notice: test');

    Log::warning('warning: test');
    self::assertLastLog('warning: test');

    Log::error('error: test');
    self::assertLastLog('error: test');

    Log::critical('critical: test');
    self::assertLastLog('critical: test');

    Log::alert('alert: test');
    self::assertLastLog('alert: test');

    Log::emergency('emergency: test');
    self::assertLastLog('emergency: test');
  }

  public function testLevelLog()
  {
    Log::bind($this->_getTestLogger(LogLevel::INFO));

    Log::info('info: test');
    self::assertLastLog('info: test');

    Log::debug('debug: test');
    self::assertLastLog('info: test');
  }

  public function testExceptionLog()
  {
    Log::bind($this->_getTestLogger());

    $e = new Exception('exception message', 123);
    Log::exception($e);
    self::assertContains('[CRITICAL] exception message ', $this->_getLogContents());
    self::assertContains('"code":123', $this->_getLogContents());
    self::assertContains('"line":90', $this->_getLogContents());
    self::assertContains('StdOutLoggerTest.php', $this->_getLogContents());
  }

  public function testExceptionTraceLog()
  {
    Log::bind($this->_getTestLogger());

    $e = new Exception('exception message', 123);
    Log::exceptionWithTrace($e);
    self::assertContains('[CRITICAL] exception message ', $this->_getLogContents());
    self::assertContains('"code":123', $this->_getLogContents());
    self::assertContains('"line":102', $this->_getLogContents());
    self::assertContains('StdOutLoggerTest.php', $this->_getLogContents());
    self::assertContains('"stack_trace"', $this->_getLogContents());
  }

  public function testContextLog()
  {
    Log::bind($this->_getTestLogger());
    Log::debug('debug: test', ['test1' => 'value1', 'test2' => 'value2']);
    self::assertLastLog('debug: test {"test1":"value1","test2":"value2"}');
  }
}
