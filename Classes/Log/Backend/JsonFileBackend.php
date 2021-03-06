<?php
namespace PunktDe\JsonLogBackend\Log\Backend;

/*
 * This file is part of the PunktDe.JsonLogBackend package.
 *
 * This package is open source software. For the full copyright and license
 * information, please view the LICENSE file which was distributed with this
 * source code.
 */

use Neos\Flow\Log\Backend\FileBackend;

class JsonFileBackend extends FileBackend
{
    /**
     * @param string $message The message to log
     * @param integer $severity One of the LOG_* constants
     * @param mixed $additionalData A variable containing more information about the event to be logged
     * @param string $packageKey Key of the package triggering the log (determined automatically if not specified)
     * @param string $className Name of the class triggering the log (determined automatically if not specified)
     * @param string $methodName Name of the method triggering the log (determined automatically if not specified)
     * @return void
     */
    public function append($message, $severity = LOG_INFO, $additionalData = null, $packageKey = null, $className = null, $methodName = null)
    {

        if ($severity > $this->severityThreshold) {
            return;
        }

        if (function_exists('posix_getpid')) {
            $processId = posix_getpid();
        } else {
            $processId = 0;
        }

        $remoteIp = ($this->logIpAddress === true) ? (isset($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : '') : '';
        $severityLabel = (isset($this->severityLabels[$severity])) ? strtolower(trim($this->severityLabels[$severity])) : 'unknown';

        $logEntryData = [
            'timestamp' => date(\DateTime::ATOM),
            'processId' => $processId,
            'severity' => $severityLabel,
            'remoteIp' => $remoteIp,
            'message' => $message,
            'origin' => [
                'packageKey' => $packageKey,
                'className' => $className,
                'methodName' => $methodName
            ],
            'additionalData' => $additionalData
        ];

        $output = json_encode($logEntryData);

        if ($this->fileHandle !== false) {
            fputs($this->fileHandle, $output . PHP_EOL);
        }
    }
}
