<?php
namespace Packaged\Log;

use Psr\Log\LoggerInterface;

class Log
{
  /**
   * @var LoggerInterface
   */
  protected static $_logger;

  public static function bind(LoggerInterface $logger): LoggerInterface
  {
    self::$_logger = $logger;
    return self::$_logger;
  }

  public static function emergency($message, array $context = [])
  {
    self::$_logger->emergency($message, $context);
  }

  public static function alert($message, array $context = [])
  {
    self::$_logger->alert($message, $context);
  }

  public static function critical($message, array $context = [])
  {
    self::$_logger->critical($message, $context);
  }

  public static function error($message, array $context = [])
  {
    self::$_logger->error($message, $context);
  }

  public static function warning($message, array $context = [])
  {
    self::$_logger->warning($message, $context);
  }

  public static function notice($message, array $context = [])
  {
    self::$_logger->notice($message, $context);
  }

  public static function info($message, array $context = [])
  {
    self::$_logger->info($message, $context);
  }

  public static function debug($message, array $context = [])
  {
    self::$_logger->debug($message, $context);
  }
}
