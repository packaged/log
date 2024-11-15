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
    self::assertLastLog(
      '[critical] exception message {"code":123,"file":"\/Users\/tom.kay\/code\/packaged\/log\/tests\/ErrorLogLoggerTest.php","line":90}'
    );
  }

  public function testExceptionTraceLog()
  {
    Log::bind(new ErrorLogLogger());

    $e = new Exception('exception message', 123);
    Log::exceptionWithTrace($e);
    self::assertLastLog(
      '[critical] exception message {"code":123,"file":"\/Users\/tom.kay\/code\/packaged\/log\/tests\/ErrorLogLoggerTest.php","line":101,"stack_trace":"#0 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php(1154): Packaged\\\\Log\\\\Tests\\\\ErrorLogLoggerTest->testExceptionTraceLog()\n#1 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php(842): PHPUnit\\\\Framework\\\\TestCase->runTest()\n#2 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestResult.php(693): PHPUnit\\\\Framework\\\\TestCase->runBare()\n#3 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestCase.php(796): PHPUnit\\\\Framework\\\\TestResult->run(Object(Packaged\\\\Log\\\\Tests\\\\ErrorLogLoggerTest))\n#4 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php(746): PHPUnit\\\\Framework\\\\TestCase->run(Object(PHPUnit\\\\Framework\\\\TestResult))\n#5 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/Framework\/TestSuite.php(746): PHPUnit\\\\Framework\\\\TestSuite->run(Object(PHPUnit\\\\Framework\\\\TestResult))\n#6 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/TextUI\/TestRunner.php(652): PHPUnit\\\\Framework\\\\TestSuite->run(Object(PHPUnit\\\\Framework\\\\TestResult))\n#7 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/TextUI\/Command.php(206): PHPUnit\\\\TextUI\\\\TestRunner->doRun(Object(PHPUnit\\\\Framework\\\\TestSuite), Array, true)\n#8 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/src\/TextUI\/Command.php(162): PHPUnit\\\\TextUI\\\\Command->run(Array, true)\n#9 \/Users\/tom.kay\/code\/packaged\/log\/vendor\/phpunit\/phpunit\/phpunit(61): PHPUnit\\\\TextUI\\\\Command::main()\n#10 {main}"}'
    );
  }

  public function testContextLog()
  {
    Log::bind(new ErrorLogLogger());
    Log::debug('debug: test', ['test1' => 'value1', 'test2' => 'value2']);
    self::assertLastLog('debug: test {"test1":"value1","test2":"value2"}');
  }
}
