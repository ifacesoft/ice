<?php

namespace Ice\Widget;

class Admin_Table extends Table
{
    protected function build(array $input)
    {
        $output = parent::build($input);

        $this->addClasses('table-striped table-bordered table-hover table-condensed');

        return $output;
    }
}