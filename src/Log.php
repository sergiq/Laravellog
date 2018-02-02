<?php

namespace Sergiq\Laravellog;

/**
 * Class Log
 *
 * @package SergiQuinonoero\Laravel
 */
class Log
{
    const DEBUG = 'debug';

    const INFO = 'info';

    const NOTICE = 'notice';

    const WARNING = 'warning';

    const ERROR = 'error';

    const CRITICAL = 'critical';

    const ALERT = 'alert';

    const EMERGENCY = 'emergency';

    static function sayHi()
    {
        return 'Hi!';
    }

    /**
     * @param \Throwable $e
     * @param string     $error_type
     * @param int        $stack_trace_length
     *
     */
    static public function log(\Throwable $e, string $error_type = self::ERROR, int $stack_trace_length = 20)
    {
        $trace = $e->getTrace();
        if (count($trace) > $stack_trace_length){
            $trace   = array_slice($trace, 0, $stack_trace_length);
            $trace[] = '...';
        }

        $request_params     = json_encode(request()->all());
        $trace_param        = json_encode($trace);
        $new_line_separator = "::";

        if (env('APP_ENV') == 'local'){
            $new_line_separator = "\n";
            $request_params     = var_export(request()->all(), true);
            $trace_param        = var_export($e->getTraceAsString(), true);
        }

        \Illuminate\Support\Facades\Log::$error_type(
            $new_line_separator . 'Message: ' . $e->getMessage() . $new_line_separator .
            'File: ' . str_replace(base_path(),"",$e->getFile()) . $new_line_separator .
            'Line: ' . $e->getLine() . $new_line_separator .
            'Code: ' . $e->getCode() . $new_line_separator .
            'Request Url: ' . request()->fullUrl() . $new_line_separator .
            'Referer Url: ' . \URL::previous() . $new_line_separator .
            "Request params: " . $request_params . $new_line_separator .
            "Trace: " . $trace_param
        );
    }
}
