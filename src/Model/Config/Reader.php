<?php

namespace App\Model\Config;

use Exception;

class Reader
{
    public function __construct()
    {

    }

    /**
     * @param string $path
     * @return array
     * @throws Exception
     */
    public function readJSON(string $path = ''): array
    {
        if (!file_exists($path)) {
            throw new Exception('Specified path doesnt exist');
        }
        $content = file_get_contents($path);
        if (!$content) {
            throw new Exception('There is file but couldnt be read');
        }
        $decodedContent = json_decode($content, true);
        if (json_last_error() > 0) {
            throw new Exception('There was error while decoding: ' . json_last_error_msg());
        }

        return $decodedContent;
    }
}