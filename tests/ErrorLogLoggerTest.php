<?php
namespace Packaged\Log\Tests;

use Exception;
use Packaged\Log\ErrorLogLogger;
use Packaged\Log\Log;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class ErrorLogLoggerTest extends TestCase
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
    self::assertStringEndsWith($test . PHP_EOL, $this->_getLogContents());
  }

  public function testDefaultLogger()
  {
    Log::unbind();

    Log::info('info: test');
    self::assertLastLog('info: test');

    Log::debug('debug: test');
    self::assertLastLog('debug: test');
  }

  public function testErrorLogLogger()
  {
    Log::bind(new ErrorLogLogger());

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
    Log::bind(new ErrorLogLogger(LogLevel::INFO));

    Log::info('info: test');
    self::assertLastLog('info: test');

    Log::debug('debug: test');
    self::assertLastLog('info: test');
  }

  public function testExceptionLog()
  {
    Log::bind(new ErrorLogLogger());

    $e = new Exception('exception message', 123);
    Log::exception($e);
    self::assertContains('[critical] exception message ', $this->_getLogContents());
    self::assertContains('"code":123', $this->_getLogContents());
    self::assertContains('"line":90', $this->_getLogContents());
    self::assertContains('ErrorLogLoggerTest.php', $this->_getLogContents());
  }

  public function testExceptionTraceLog()
  {
    Log::bind(new ErrorLogLogger());

    $e = new Exception('exception message', 123);
    Log::exceptionWithTrace($e, ['extra' => 'additional']);
    self::assertContains('[critical] exception message ', $this->_getLogContents());
    self::assertContains('"code":123', $this->_getLogContents());
    self::assertContains('"line":102', $this->_getLogContents());
    self::assertContains('"extra":"additional"', $this->_getLogContents());
    self::assertContains('ErrorLogLoggerTest.php', $this->_getLogContents());
    self::assertContains('"stack_trace"', $this->_getLogContents());
  }

  public function testContextLog()
  {
    Log::bind(new ErrorLogLogger());
    Log::debug('debug: test', ['test1' => 'value1', 'test2' => 'value2']);
    self::assertLastLog('debug: test {"test1":"value1","test2":"value2"}');
  }
}
