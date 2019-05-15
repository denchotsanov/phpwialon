<?php
namespace denchotsanov;
/*
 * Wialon errorCode to textMessage converter
 */

class WialonError
{
    /// PROPERTIES
    /** list of error messages with codes */
    public static $errors = [
        1 => 'Invalid session',
        2 => 'Invalid service',
        3 => 'Invalid result',
        4 => 'Invalid input',
        5 => 'Error performing request',
        6 => 'Unknow error',
        7 => 'Access denied',
        8 => 'Invalid user name or password',
        9 => 'Authorization server is unavailable, please try again later',
        1001 => 'No message for selected interval',
        1002 => 'Item with such unique property already exists',
        1003 => 'Only one request of given time is allowed at the moment'
    ];

    /// METHODS
    /** error message generator */
    public static function error($code = '', $text = '')
    {
        $code = intval($code);

        if (isset(self::$errors[$code])) {
            $text = implode(' ', [self::$errors[$code], $text]);
        }

        $message = sprintf('%d: %s', $code, $text);

        return sprintf('WialonError( %s )', $message);
    }
}
