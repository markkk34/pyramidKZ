<?php


namespace App\Controllers;

use Psr\Log\LoggerInterface;

class Logger implements LoggerInterface
{

    /**
     * @param string $message
     * @param array $context
     */
    public function emergency($message, array $context = array())
    {
        // TODO: Implement emergency() method.
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function alert($message, array $context = array())
    {
        // TODO: Implement alert() method.
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function critical($message, array $context = array())
    {
        // TODO: Implement critical() method.
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function error($message, array $context = array())
    {
        // TODO: Implement error() method.
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function warning($message, array $context = array())
    {
        if (is_null($message)) {
            echo '<br>#bad_' . __FUNCTION__ . '----' . 'Incorrect INFO Output' . '<br>';
        } elseif (!empty($context)) {
            echo '<br>#' . __FUNCTION__ . '----' . $message . '<br>---------+extra info----' . $context . '<br>';
        } else {
            echo '<br>#' . __FUNCTION__ . '----' . $message . '<br>';
        }
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function notice($message, array $context = array())
    {
        // TODO: Implement notice() method.
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function info($message, array $context = array())
    {
        if (is_null($message)) {
            echo '#bad_' . __FUNCTION__ . '----' . 'Incorrect INFO Output' . '<br>';
        } elseif (!empty($context)) {
            echo '#' . __FUNCTION__ . '----' . $message . '<br>---------+extra info----' . '<br>';
        } else {
            echo '#' . __FUNCTION__ . '----' . $message . '<br>';
        }
    }

    /**
     * @param string $message
     * @param array $context
     */
    public function debug($message, array $context = array())
    {
        // TODO: Implement debug() method.
    }

    /**
     * @param mixed $level
     * @param string $message
     * @param array $context
     */
    public function log($level, $message, array $context = array())
    {
        // TODO: Implement log() method.
    }
}