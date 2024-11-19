<?php
namespace Packaged\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Logger that outputs the messages using error_log()
 */
class StdOutLogger extends ErrorLogLogger
{
  private $_handle;

  public function __construct($maxLevel = LogLevel::DEBUG)
  {
    parent::__construct($maxLevel);
    $this->setHandle(fopen('php://stdout', 'w'));
  }

  public function setHandle($handle)
  {
    $this->_handle = $handle;
  }

  protected function _writeLog($message)
  {
    fwrite($this->_handle, $message);
  }

  protected function _formatLog($level, $message, array $context = null)
  {
    if(!empty($context))
    {
      $message .= ' ' . json_encode($context);
    }
    $ulevel = strtoupper($level);
    return "[{$ulevel}] $message" . PHP_EOL;
  }
}
