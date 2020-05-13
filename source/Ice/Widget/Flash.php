<?php

namespace Ice\Widget;

use Ice\WidgetComponent\Flash_Message;

class Flash extends Html_Div
{
    protected static function config()
    {
        $config = parent::config();

        $config['render']['template'] = 'Ice\Widget\Default';

        $config['input'] = ['type', 'message'];

        return $config;
    }

    protected function build(array $input)
    {
        $logger = $this->getLogger();

        $type = $this->get('type');

        $this
            ->message(
                $type,
                [
                    'params' => [$type => $logger->info($this->get('message'), $type)],
                    'encode' => false
                ]
            );
    }

    /**
     * Build a flash message
     *
     * @param  $columnName
     * @param  array $options
     * @return $this
     */
    public function message($columnName, array $options = [])
    {
        return $this->addPart(new Flash_Message($columnName, $options, null, $this));
    }
}