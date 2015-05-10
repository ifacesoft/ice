[![Logo](http://iceframework.net/resource/img/logo/ice1.jpg)](http://iceframework.net) [Ice](http://iceframework.net) (iceframework.net) 
===

[![Build Status](https://scrutinizer-ci.com/g/ifacesoft/Ice/badges/build.png?b=master)](https://scrutinizer-ci.com/g/ifacesoft/Ice/build-status/master)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/ifacesoft/Ice/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/ifacesoft/Ice/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/ifacesoft/Ice/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/ifacesoft/Ice/?branch=master)

[![Latest Stable Version](https://poser.pugx.org/ifacesoft/ice/v/stable.svg)](https://packagist.org/packages/ifacesoft/ice)
[![Total Downloads](https://poser.pugx.org/ifacesoft/ice/downloads.svg)](https://packagist.org/packages/ifacesoft/ice)
[![Latest Unstable Version](https://poser.pugx.org/ifacesoft/ice/v/unstable.svg)](https://packagist.org/packages/ifacesoft/ice)
[![License](https://poser.pugx.org/ifacesoft/ice/license.svg)](https://packagist.org/packages/ifacesoft/ice)

Ice is a general purpose PHP-framework.
You may fully rely on Ice while developing complex web-applications.
Ice key features are the built-in cache support of the main components,
flexible configuration and the ability to easily extend existing functionality.

Alternative repository on Bitbacket: [Ice](https://bitbucket.org/dp_ifacesoft/ice) (https://bitbucket.org/dp_ifacesoft/ice)

Quick Start Guide
=================

Create a composer.json in the directory workspace/MyProject
-----------------------------------------------------------

```shell
mkdir -p workspace/MyProject && cd workspace/MyProject && touch composer.json
```

workspace - your working directory (logs, caches etc.);

MyProject - the source code of your project

an example file:
```json
{
    "name": "vendor/my-project",
    "description": "My project with Ice",
    "type": "project",
    "require": {
        "ifacesoft/ice": "1.0.*"
    },
    "license": "proprietary",
    "authors": [
        {
            "name": "dp",
            "email": "denis.a.shestakov@gmail.com"
        }
    ],
    "minimum-stability": "stable",
    "config": {
        "vendor-dir": "../_vendor"
    },
    "scripts": {
        "post-install-cmd": [
            "Ice\\App::update"
        ],
        "post-update-cmd": [
            "Ice\\App::update"
        ]
    }
}
```

Install the project using composer
----------------------------------

```shell
curl -sS https://getcomposer.org/installer | php && php composer.phar install --prefer-source
```

Configure the web server and /etc/hosts
---------------------------------------

After successful installation, use the generated configuration.

Ready! Your project should be available at http://myproject.local 

The basics
==========

Routes
------

sample /Config/Ice/Core/Route.php:

```php
<?php
return [
    'mp_page' => [
        'route' => '/page/{$page}',
        'params' => [
            'page' => '(\d)'
        ],
        'weight' => 10000,
        'request' => [
            'GET' => [
                'Www:Layout_Main' => [
                    'actions' => [
                        ['Ice:Title' => 'title', ['title' => 'Ice - Open Source PHP Framework ']],
                        'Www:Index' => 'main'
                    ]
                ]
            ]
        ]
    ]
]    
```

Important parts:

* 'mp_page' - Route name, (Uses: Route::getInstance('mp_page')->getUrl(20)) returned '/page/20' etc.)
* 'weight' - Priority of matched routes. Greater weight - greater priority.
* 'request' section - Array of available requuest methods (GET, POST etc.)
* 'request/GET' - Only one item (first) contained layout action class as key and params as value

Actions
-------

```php
namespace Mp\Action;
use Ice\Core\Action;
class Page extends Action
{
    protected static function config()
    {
        return [
            'view' => ['viewRenderClass' => 'Ice:Smarty', 'template' => null, 'layout'],
            'actions' => [],
            'input' => [],
            'output' => [],
            'ttl' => -1,
        ];
    }
    public function run(array $input)
    {
    }
}
```

**2 main methods - config and run**

method config - return array:

* 'view' - Define way of render output data ('viewRenderClass' - render class, 'template' - template for render, layout - template-wrapper of rendered content in emmet style)
* 'actions' - Child actions
* 'input' - Array of input params with their data providers. Also information of validators, defaults end other.
* 'output' - Фdditional sources of output (params and their data providers as well as 'input' section)
* 'ttl' - time stored in cache (now supported only 3600 :) )
* 'accses' - Information to checks permissions to run action (support environment - one of 'production', 'test' or 'development' and request - one of 'cli' or 'ajax')

Models
------
 
Select examples:

```php
// 1.
$page = Page::getModel(20, ['title', 'desc']); // or Page::getModel(20, '*')
// 2.
$page = Page::create(['title' => 'page 20')->find([id, 'desc']);
// 3.
$page = Page::createQueryBuilder()->eq(['desc' => '20th page'])->getSelectQuery()->getModel();
``` 

Insert examples:

```php
// 1. 
Page::create(['title' => 'page 20', 'desc' => '20th page'])->save();
// 2.
Page::createQueryBuilder()->getInsertQuery(['title' => 'page 20', 'desc' => '20th page'])->getQueryResult();
```  

Update examples:

```php
// 1. 
Page::getModel(20, ['title', 'desc'])->set(['title' => 'another title'])->save();
// 2.
Page::createQueryBuilder()->eq(['id' => 20])->getUpdateQuery(['title' => 'another title'])->getQueryResult();
```   

Update examples:
 
```php
// 1. 
Page::getModel(20, '/pk')->remove();
// 2.
Page::createQueryBuilder()->getDeleteQuery(20)->getQueryResult();
```   
 
Documentation
=============

More info on [iceframework.net](http://iceframework.net) such as:

* [Handbook](http://iceframework.net/handbook)
* [Api](http://iceframework.net/resource/api/Ice/1.0/)

Good luck! 
