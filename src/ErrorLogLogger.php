<?php
namespace Packaged\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Logger that outputs the messages using error_log()
 */
class ErrorLogLogger extends AbstractLogger
{
  /**
   * Max log level to output. Defaults to DEBUG (output everything)
   *
   * @var int
   */
  private $_maxLevel = 7;

  // Log levels in priority order
  private $_levels = [
    LogLevel::EMERGENCY => 0,
    LogLevel::ALERT     => 1,
    LogLevel::CRITICAL  => 2,
    LogLevel::ERROR     => 3,
    LogLevel::WARNING   => 4,
    LogLevel::NOTICE    => 5,
    LogLevel::INFO      => 6,
    LogLevel::DEBUG     => 7,
  ];

  public function __construct($maxLevel = LogLevel::DEBUG)
  {
    $this->setMaxLogLevel($maxLevel);
  }

  protected function _writeLog($message)
  {
    error_log($message);
  }

  /**
   * @param string $level One of the Psr\Log\LogLevel constants
   */
  public function setMaxLogLevel($level)
  {
    $this->_maxLevel = $this->_levelToNum($level);
  }

  public function log($level, $message, array $context = null)
  {
    if($this->_levelToNum($level) <= $this->_maxLevel)
    {
      $this->_writeLog($this->_formatLog($level, $message, $context));
    }
  }

  protected function _formatLog($level, $message, array $context = null)
  {
    if(!empty($context))
    {
      $message .= ' ' . json_encode($context);
    }
    return "[$level] $message";
  }

  private function _levelToNum($level)
  {
    return $this->_levels[$level] ?? end($this->_levels);
  }
}
