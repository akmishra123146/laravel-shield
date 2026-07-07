<?php

namespace CyberSec\Shield\Logging;

use Monolog\Formatter\JsonFormatter;
use Illuminate\Support\Facades\Request;

class SiemFormatter extends JsonFormatter
{
    /**
     * Format a log record into a SIEM-friendly JSON format.
     *
     * @param  \Monolog\LogRecord|array  $record
     * @return string
     */
    public function format($record): string
    {
        // Normalize the record whether it's Monolog 2 or 3
        $normalized = $this->normalize($record);
        
        $siemData = [
            'timestamp' => $normalized['datetime'] ?? now()->toIso8601String(),
            'event' => [
                'type' => 'security',
                'level' => $normalized['level_name'] ?? 'INFO',
                'message' => $normalized['message'] ?? '',
                'context' => $normalized['context'] ?? [],
            ],
            'network' => [
                'client_ip' => Request::ip(),
                'user_agent' => Request::userAgent(),
                'url' => Request::fullUrl(),
                'method' => Request::method(),
            ],
            'app' => [
                'name' => config('app.name'),
                'env' => config('app.env'),
            ]
        ];

        if ($user = Request::user()) {
            $siemData['user'] = [
                'id' => $user->getAuthIdentifier(),
                // Be careful not to log PII unless configured to do so
            ];
        }

        return $this->toJson($siemData, true) . ($this->appendNewline ? "\n" : '');
    }
}
