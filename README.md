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


History
=======

Development of the project since at December 2013.


Features
========

* Easy to learn and use
* Incredibly fast even at default settings
* Easily expandable
* The project is far from the monstrosity
* Ability to use third-party libraries
* Built-in support for JQuery, Bootstrap
* Provides high application security
* Includes a convenient tools for profiling and debugging
* Distributed under the copyleft license


Architecture
============

Ice is originally written in the programming language PHP 5.4. Basic functionality is written using namespaces.
All classes, with rare exceptions, are loaded using the integrated automatic class loading mechanism.
By default web applications made with the Ice framework use a Model-View-Action approach.
Framework supports modular structure. Thus, the functional expansion can be achieved by adding new modules to your web project.
Directory hierarhy is designed so that you could easily find a component or project resource.
Almost all the key components are cached using a particular data provider.
All data structures are stored in the configuration files of model schemas.
Model data fields are based on the model mapping configuration file.
This ensures adherence to a naming model fields.


Abilities
=========

* Receiving data from data providers Redis, Apcu etc.
* Data retrieval data from data sources: MariaDB (Mysqli), etc.
* Retrieval data from data sources is possible as a collection, a model or a simple array
* Filtering and validation of incoming data
* Usage of generic query builder for the preparation and execution of queries to data source
* Renderer of templates via the template engine Smarty (by default - templates for php)
* Performing the action on the route, defined by the requested address (url)
* Override the default settings through creation of the configuration file


Project structure
=================

      _cache/                 Cache files for separate projects
      _log/                   Log files for separate projects
      _resource/              Resource files (javascript, css, images etc.) for separate projects
      _storage/               File storage
      _vendor/                Vendors (loaded via composer)

      MyProject/        Your module
            Config /          Configuration files (php format)
                  Ice/              Overridden configuration files for Ice module
                  Mp/               Configuration files for your module (MyProject)
            Resource/         Resource files
                  Ice/              Overridden resource files for Ice module
                  Mp/               Resource for your module (MyProject) (views, localization files etc.)
            Source/           Source files
                  Ice/              Overridden source files for Ice module (not recommended)
                  Mp/               Source files for your module (MyProject)
                        Action/           Action classes for your module (MyProject)
                        Model/            Model classes for your module (MyProject)
                        ...               Other implementations of core ice classes and interfaces
            Web/              Web root directory
                  index.php         Directory index file


Quick Start Guide
=================

For Linux:
----------

Composer install via shell:

      $ curl -s http://getcomposer.org/installer | php
      $ php composer.phar create-project ifacesoft/ice Ice dev-master
      $ ./Ice/cli Ice:Module_Create

Zip archive install:

      1. Download and unpack .zip
      2. $ ./Ice/cli Ice:Composer_Update
      3. $ ./Ice/cli Ice:Module_Create

For Windows:
------------
Composer install via command line (require php extensions: openssl):

      >set PATH=%PATH%;C:\php;C:\Program Files\Mercurial;C:\Program Files (x86)\Git\bin;C:\Program Files (x86)\Subversion\bin
      >php -r "readfile('https://getcomposer.org/installer');" | php
      >php composer.phar create-project ifacesoft/ice Ice dev-master
      >php .\Ice\app.php Ice:Module_Create
      >mkdir .\_log\{$YOUR MODULE NAME}


Documentation
=============

More info on [iceframework.net](http://iceframework.net) such as:

* [Handbook](http://iceframework.net/handbook)
* [Cookbook](http://iceframework.net/cookbook)
* [FAQ](http://iceframework.net/faq)
* [Api](http://iceframework.net/resource/api/Ice/0.0/)

Good luck! 
