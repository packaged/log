<?php
namespace Packaged\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Logger that outputs the messages using error_log()
 */
class BasicGoogleCloudLogger extends ErrorLogLogger
{
  private $_handle;

  public function __construct($maxLevel = LogLevel::DEBUG)
  {
    parent::__construct($maxLevel);
    $this->_handle = fopen('php://stderr', 'wb');
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
    return json_encode(array_filter([
        'timestamp'   => (new \DateTime())->format(DATE_RFC3339_EXTENDED),
        'severity'    => $level,
        'textPayload' => $message,
        'jsonPayload' => $context,
      ])) . PHP_EOL;
  }
}
