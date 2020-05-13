<?php

namespace Ice\Widget;

class Layout extends Block_Render
{
    /**
     * Widget config
     *
     * @return array
     */
    protected static function config()
    {
        return [
            'render' => ['template' => true, 'class' => 'Ice:Php', 'layout' => null, 'resource' => null],
            'access' => ['roles' => [], 'request' => null, 'env' => null, 'message' => ''],
            'resource' => ['js' => null, 'css' => null, 'less' => null, 'img' => null],
            'cache' => ['ttl' => -1, 'count' => 1000],
            'input' => [
                'main' => ['default' => Dashboard::class, 'providers' => 'default'],
            ],
            'output' => [],
        ];
    }

    /** Build widget
     *
     * @param array $input
     * @return array
     * @throws \Ice\Core\Exception
     */
    protected function build(array $input)
    {
        $output = parent::build($input);

        $this->widget('staticResources', ['widget' => $this->getWidget('Ice:Resource_Static')]);
        $this->widget('dynamicResources', ['widget' => $this->getWidget(Resource_Dynamic::class)]);
        $this->widget('footerJs', ['widget' => $this->getWidget('Ice:Resource_FooterJs')]);

        return $output;
    }
}
