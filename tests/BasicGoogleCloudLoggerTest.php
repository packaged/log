<?php
namespace Packaged\Log\Tests;

use Exception;
use Packaged\Log\BasicGoogleCloudLogger;
use Packaged\Log\ErrorLogLogger;
use Packaged\Log\Log;
use PHPUnit\Framework\TestCase;
use Psr\Log\LogLevel;

class BasicGoogleCloudLoggerTest extends TestCase
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
    $l = new BasicGoogleCloudLogger($maxLevel);
    $l->setHandle($this->_handler);
    return $l;
  }

  public function testLogger()
  {
    Log::bind($this->_getTestLogger());

    Log::debug('debug: test');
    self::assertLastLog('","severity":"debug","textPayload":"debug: test"}');

    Log::info('info: test');
    self::assertLastLog('","severity":"info","textPayload":"info: test"}');

    Log::notice('notice: test');
    self::assertLastLog('","severity":"notice","textPayload":"notice: test"}');

    Log::warning('warning: test');
    self::assertLastLog('","severity":"warning","textPayload":"warning: test"}');

    Log::error('error: test');
    self::assertLastLog('","severity":"error","textPayload":"error: test"}');

    Log::critical('critical: test');
    self::assertLastLog('","severity":"critical","textPayload":"critical: test"}');

    Log::alert('alert: test');
    self::assertLastLog('","severity":"alert","textPayload":"alert: test"}');

    Log::emergency('emergency: test');
    self::assertLastLog('","severity":"emergency","textPayload":"emergency: test"}');
  }

  public function testLevelLog()
  {
    Log::bind($this->_getTestLogger(LogLevel::INFO));

    Log::info('info: test');
    self::assertLastLog('","severity":"info","textPayload":"info: test"}');

    Log::debug('debug: test');
    self::assertLastLog('","severity":"info","textPayload":"info: test"}');
  }

  public function testExceptionLog()
  {
    Log::bind($this->_getTestLogger());

    $e = new Exception('exception message', 123);
    Log::exception($e);
    self::assertContains(',"textPayload":"exception message"', $this->_getLogContents());
    self::assertContains(',"severity":"critical"', $this->_getLogContents());
    self::assertNotContains('"stace_trace":', $this->_getLogContents());
  }

  public function testExceptionTraceLog()
  {
    Log::bind($this->_getTestLogger());

    $e = new Exception('exception message', 123);
    Log::exceptionWithTrace($e, ['extra' => 'additional']);
    self::assertContains('"textPayload":"exception message"', $this->_getLogContents());
    self::assertContains('"severity":"critical"', $this->_getLogContents());
    self::assertContains('"code":123', $this->_getLogContents());
    self::assertContains('"line":100', $this->_getLogContents());
    self::assertContains('"extra":"additional"', $this->_getLogContents());
    self::assertContains('BasicGoogleCloudLoggerTest.php', $this->_getLogContents());
    self::assertContains('"stack_trace"', $this->_getLogContents());
  }

  public function testContextLog()
  {
    Log::bind($this->_getTestLogger());
    Log::debug('debug: test', ['test1' => 'value1', 'test2' => 'value2']);
    self::assertLastLog(
      '","severity":"debug","textPayload":"debug: test","jsonPayload":{"test1":"value1","test2":"value2"}}'
    );
  }
}
