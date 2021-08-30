<?php
/**
 * @param string $type
 * @param Exception $exception
 */
function logThis(\Exception $exception, string $type = 'error')
{
    $content = json_encode([
        'message' => $exception->getMessage(),
        'line' => $exception->getLine()
    ]);
    switch ($type) {
        case 'info':
            \Log::info($content);
            break;
        case 'debug':
            \Log::debug((string)$content);
            break;
        default:
            \Log::error((string)$content);
            break;
    }
}