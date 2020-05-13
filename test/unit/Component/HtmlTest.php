<?php

namespace Tests\Ifacesoft\Ice;

use Ifacesoft\Ice\Core\Component\Html_Metadata_Head;
use Ifacesoft\Ice\Core\Component\Html_Metadata_Meta;
use Ifacesoft\Ice\Core\Component\Html_Metadata_Title;
use Ifacesoft\Ice\Core\Component\Html_RootElement_Html;
use Ifacesoft\Ice\Core\Component\Html_Sections_Body;
use Ifacesoft\Ice\Core\Component\Html_Sections_H1;
use PHPUnit\Framework\TestCase;

class Component_HtmlTest extends TestCase
{
    private function getLayoutComponent()
    {
        return Html_RootElement_Html::create([
            'components' => [
                [
                    Html_Metadata_Head::class,
                    [
                        'components' => [
                            [
                                Html_Metadata_Meta::class,
                                [
                                    'name' => 'charset',
                                    'attributes' => ['charset' => 'utf-8']
                                ]
                            ],
                            [
                                Html_Metadata_Title::class,
                                [
                                    'value' => 'Тайтл'
                                ]
                            ]
                        ]
                    ]
                ],
                [
                    Html_Sections_Body::class, ['name' => 'body']
                ]
            ]
        ]);
    }

    /**
     * @throws \Error
     */
    public function testRootElementHtml()
    {
        $component = Html_RootElement_Html::create();

        $this->assertInstanceOf(Html_RootElement_Html::class, $component);

        $content = $component->render();

        $this->assertEquals('<html></html>', $content);
    }

    /**
     * @throws \Error
     */
    public function testSectionsH1()
    {
        $body = $this
            ->getLayoutComponent()
            ->getComponent('body');

        $content = $body
            ->addComponent([Html_Sections_H1::class, ['name' => 'header', 'value' => 'Заголовок H1']])
            ->render();

        $this->assertEquals('<body id="body_0"><h1 id="header_453160402">Заголовок H1</h1></body>', $content);
    }
}