<?php

use App\Controllers\PyramidController;

class FrontController
{
    /**
     * FrontController constructor.
     * @throws Exception
     */
    public function __construct()
    {
        $pyramid = new PyramidController();
        $pyramid->createPyramid();
    }
}
