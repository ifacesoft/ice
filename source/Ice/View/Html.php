<?php

namespace Ice\View;

use Ice\Core\View;

class Html extends View
{
    public function __construct()
    {
        $this->setRaw('');
    }
}