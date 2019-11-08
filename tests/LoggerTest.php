<?php
namespace Packaged\Log\Tests;

use Exception;
use Packaged\Log\ErrorLogLogger;
use Packaged\Log\Log;
use Psr\Log\LogLevel;

class LoggerTest extends AbstractLoggerTestCase
{
  public function testDefaultLogger()
  {
    Log::unbind();

    Log::info('info: test');
    $this->assertLastLog('info: test');

    Log::debug('debug: test');
    $this->assertLastLog('debug: test');
  }

  public function testErrorLogLogger()
  {
    Log::bind(new ErrorLogLogger());

    Log::debug('debug: test');
    $this->assertLastLog('debug: test');

    Log::info('info: test');
    $this->assertLastLog('info: test');

    Log::notice('notice: test');
    $this->assertLastLog('notice: test');

    Log::warning('warning: test');
    $this->assertLastLog('warning: test');

    Log::error('error: test');
    $this->assertLastLog('error: test');

    Log::critical('critical: test');
    $this->assertLastLog('critical: test');

    Log::alert('alert: test');
    $this->assertLastLog('alert: test');

    Log::emergency('emergency: test');
    $this->assertLastLog('emergency: test');
  }

  public function testLevelLog()
  {
    Log::bind(new ErrorLogLogger(LogLevel::INFO));

    Log::info('info: test');
    $this->assertLastLog('info: test');

    Log::debug('debug: test');
    $this->assertLastLog('info: test');
  }

  public function testExceptionLog()
  {
    Log::bind(new ErrorLogLogger());

    $e = new Exception('exception message', 123);
    Log::exception($e);
    $this->assertLastLog('EXCEPTION (123): exception message');
  }

  public function testExceptionTraceLog()
  {
    Log::bind(new ErrorLogLogger());

    $e = new Exception('exception message', 123);
    Log::exceptionWithTrace($e);
    $this->assertContains('EXCEPTION (123): exception message', $this->_getLogContents());
    $this->assertContains('Packaged\Log\Tests\LoggerTest->testExceptionTraceLog()', $this->_getLogContents());
    $this->assertContains('#0', $this->_getLogContents());
    $this->assertContains('{main}', $this->_getLogContents());
  }
}
