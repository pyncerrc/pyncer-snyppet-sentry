<?php
namespace Pyncer\Snyppet\Sentry\Log;

use Psr\Log\LogLevel as PsrLogLevel;
use Pyncer\Log\AbstractLogger;
use Stringable;

use function json_encode;
use function Pyncer\date_time as pyncer_date_time;
use function strval;

use const Pyncer\Snyppet\Sentry\LOGGER_SEVERITY_NONE as PYNCER_SENTRY_LOGGER_SEVERITY_NONE;
use const Pyncer\Snyppet\Sentry\LOGGER_SEVERITY_DEBUG as PYNCER_SENTRY_LOGGER_SEVERITY_DEBUG;
use const Pyncer\Snyppet\Sentry\LOGGER_SEVERITY_INFO as PYNCER_SENTRY_LOGGER_SEVERITY_INFO;
use const Pyncer\Snyppet\Sentry\LOGGER_SEVERITY_WARNING as PYNCER_SENTRY_LOGGER_SEVERITY_WARNING;
use const Pyncer\Snyppet\Sentry\LOGGER_SEVERITY_ERROR as PYNCER_SENTRY_LOGGER_SEVERITY_ERROR;
use const Pyncer\Snyppet\Sentry\LOGGER_SEVERITY_FATAL as PYNCER_SENTRY_LOGGER_SEVERITY_FATAL;

class SentryLogger extends AbstractLogger
{
    public function log(
        mixed $level,
        string|Stringable $message,
        array $context = []
    ): void
    {
        switch ($level) {
            case PsrLogLevel::EMERGENCY:
            case PsrLogLevel::ALERT:
            case PsrLogLevel::CRITICAL:
                if (!PYNCER_SENTRY_LOGGER_SEVERITY_FATAL) {
                    return;
                }
                $level = \Sentry\Severity::fatal();
                break;
            case PsrLogLevel::ERROR:
                if (!PYNCER_SENTRY_LOGGER_SEVERITY_ERROR) {
                    return;
                }
                $level = \Sentry\Severity::error();
                break;
            case PsrLogLevel::WARNING:
                if (!PYNCER_SENTRY_LOGGER_SEVERITY_WARNING) {
                    return;
                }
                $level = \Sentry\Severity::warning();
                break;
            case PsrLogLevel::NOTICE:
            case PsrLogLevel::INFO:
                if (!PYNCER_SENTRY_LOGGER_SEVERITY_INFO) {
                    return;
                }
                $level = \Sentry\Severity::info();
                break;
            case PsrLogLevel::DEBUG:
                if (!PYNCER_SENTRY_LOGGER_SEVERITY_DEBUG) {
                    return;
                }

                $level = \Sentry\Severity::debug();
                break;
            default:
                if (!PYNCER_SENTRY_LOGGER_SEVERITY_NONE) {
                    return;
                }
                $level = null;
                break;
        }

        \Sentry\withScope(function (\Sentry\State\Scope $scope) use($level, $message, $context): void {
            if ($level !== null) {
                $scope->setLevel($level);
            }

            if ($context) {
                $scope->setContext('logger', $context);
            }

            \Sentry\captureMessage($message);
        });
    }
}
