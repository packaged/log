<?php
namespace Packaged\Log;

use Psr\Log\LoggerInterface;
use Throwable;

class Log
{
  /**
   * @var LoggerInterface
   */
  protected static $_logger;

  public static function bind(LoggerInterface $logger): LoggerInterface
  {
    self::$_logger = $logger;
    return self::_getLogger();
  }

  public static function unbind(): void
  {
    self::$_logger = null;
  }

  protected static function _getLogger(): LoggerInterface
  {
    return self::$_logger ?: static::_fallbackLogger();
  }

  protected static function _fallbackLogger(): LoggerInterface
  {
    static $failoverLogger;
    if($failoverLogger === null)
    {
      $failoverLogger = new ErrorLogLogger();
    }
    return $failoverLogger;
  }

  public static function emergency($message, array $context = [])
  {
    self::_getLogger()->emergency($message, $context);
  }

  public static function alert($message, array $context = [])
  {
    self::_getLogger()->alert($message, $context);
  }

  public static function critical($message, array $context = [])
  {
    self::_getLogger()->critical($message, $context);
  }

  public static function error($message, array $context = [])
  {
    self::_getLogger()->error($message, $context);
  }

  public static function warning($message, array $context = [])
  {
    self::_getLogger()->warning($message, $context);
  }

  public static function notice($message, array $context = [])
  {
    self::_getLogger()->notice($message, $context);
  }

  public static function info($message, array $context = [])
  {
    self::_getLogger()->info($message, $context);
  }

  public static function debug($message, array $context = [])
  {
    self::_getLogger()->debug($message, $context);
  }

  public static function exception(Throwable $e, array $context = [])
  {
    static::critical(
      $e->getMessage(),
      $context + [
        'code' => $e->getCode(),
        'file' => $e->getFile(),
        'line' => $e->getLine(),
      ]
    );
  }

  public static function exceptionWithTrace(Throwable $e, array $context = [])
  {
    static::critical(
      $e->getMessage(),
      $context + [
        'code'        => $e->getCode(),
        'file'        => $e->getFile(),
        'line'        => $e->getLine(),
        'stack_trace' => $e->getTraceAsString(),
      ]
    );
  }
}
