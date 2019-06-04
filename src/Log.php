<?php

namespace Sergiq\Laravellog;

use Illuminate\Support\Str;

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
    static public function log(\Throwable $e, string $error_type = self::ERROR)
    {
        $new_line_separator = "::";
        $exception_message  = $e->getMessage();
        $exception_line     = $e->getLine();
        $exception_file     = $e->getFile();
        $exception_code     = $e->getCode();
        $request_path       = request()->path();
        $request_url        = request()->root();
        $user_ip            = request()->ip();
        $params             = request()->all();
        $method             = request()->method();
        $trace_param        = json_encode($e->getTrace());

        if (env('APP_ENV') == 'local'){
            $new_line_separator = "\n";
            $trace_param        = var_export($e->getTraceAsString(), true);
        }

        foreach ($params as $key => $value) {
            if (Str::contains($key, [ 'password', 'key', 'secret' ])){
                $params[ $key ] = '**hidden parameter**';
            }
        }

        $message = 'v.4' . $new_line_separator .
            'Message: ' . $exception_message . $new_line_separator .
            'File: ' . $exception_file . $new_line_separator .
            'Line: ' . $exception_line . $new_line_separator .
            'Code: ' . $exception_code . $new_line_separator .
            'IP: ' . $user_ip . $new_line_separator .
            'Method: ' . $method . $new_line_separator .
            'Request Url: ' . $request_url . '/' . $request_path . $new_line_separator .
            'Referer Url: ' . ( \Request::server('HTTP_REFERER') ? : '<direct-request>' ) . $new_line_separator .
            "Request params: " . json_encode($params) . $new_line_separator .
            "Trace: " . $trace_param;

        \Illuminate\Support\Facades\Log::$error_type($message);
    }
}