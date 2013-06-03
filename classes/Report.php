<?php

namespace updatesystem;

class Report
{

    static $log;
    static $mute = false;

    /**
     *
     * @param bool $state
     * @return bool Returns state
     */
    static function mute($state = null)
    {
        if( !isset($state) ) return self::$mute;
        self::$mute = $state;
        return self::$mute;
    }

    /**
     *
     * @param bool $bool
     * @return string Show a nice version of a boring true or false.
     */
    static function boolAnswer($bool)
    {
        if( $bool === true )
        {
            self::state("YES. OK!");
            return;
        }
        self::state("NO!");
    }

    /**
     * Pass it to the stdOut
     * @param string $text
     */
    static function state($text)
    {
        if( !self::$mute )
        {
            self::$log[] = $text;
            echo "<li>".$text."</li>";
        }
    }

}

?>
