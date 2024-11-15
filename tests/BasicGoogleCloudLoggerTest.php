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
    Log::exceptionWithTrace($e);
    self::assertContains(',"textPayload":"exception message"', $this->_getLogContents());
    self::assertContains(',"severity":"critical"', $this->_getLogContents());
    self::assertContains(
      '"jsonPayload":{"code":123,"file":"\/Users\/tom.kay\/code\/packaged\/log\/tests\/BasicGoogleCloudLoggerTest.php","line":100,"stack_trace":"#0 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php(1154): Packaged\\\\Log\\\\Tests\\\\BasicGoogleCloudLoggerTest->testExceptionTraceLog()\n#1 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php(842): PHPUnit\\\\Framework\\\\TestCase->runTest()\n#2 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestResult.php(693): PHPUnit\\\\Framework\\\\TestCase->runBare()\n#3 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php(796): PHPUnit\\\\Framework\\\\TestResult->run(Object(Packaged\\\\Log\\\\Tests\\\\BasicGoogleCloudLoggerTest))\n#4 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php(746): PHPUnit\\\\Framework\\\\TestCase->run(Object(PHPUnit\\\\Framework\\\\TestResult))\n#5 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php(746): PHPUnit\\\\Framework\\\\TestSuite->run(Object(PHPUnit\\\\Framework\\\\TestResult))\n#6 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/TextUI\/TestRunner.php(652): PHPUnit\\\\Framework\\\\TestSuite->run(Object(PHPUnit\\\\Framework\\\\TestResult))\n#7 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/TextUI\/Command.php(206): PHPUnit\\\\TextUI\\\\TestRunner->doRun(Object(PHPUnit\\\\Framework\\\\TestSuite), Array, true)\n#8 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/TextUI\/Command.php(162): PHPUnit\\\\TextUI\\\\Command->run(Array, true)\n#9 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/phpunit(61): PHPUnit\\\\TextUI\\\\Command::main()\n#10 {main}"}}',
      $this->_getLogContents()
    );
    self::assertContains('"stack_trace":', $this->_getLogContents());
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
