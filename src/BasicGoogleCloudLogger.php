<?php
namespace Packaged\Log;

use Psr\Log\AbstractLogger;
use Psr\Log\LogLevel;

/**
 * Logger that outputs the messages using error_log()
 */
class BasicGoogleCloudLogger extends StdOutLogger
{
  protected function _formatLog($level, $message, array $context = null)
  {
    return json_encode(
        array_filter(
          array_merge($context, [
            'timestamp'   => (new \DateTime())->format(DATE_RFC3339_EXTENDED),
            'severity'    => strtoupper($level),
            'textPayload' => $message,
          ])
        )
      ) . PHP_EOL;
  }
}
