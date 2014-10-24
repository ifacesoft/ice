Ice [iceframework.net](http://iceframework.net)
===

Ice is a general purpose PHP-framework.
You may fully rely on Ice while developing complex web-applications.
Ice key features are the built-in cache support of the main components,
flexible configuration and the ability to easily extend existing functionality.

History
-------------------

Development of the project since at December 2013.

Features
-------------------

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
-------------------

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
-------------------

* Receiving data from data providers Redis, Apcu etc.
* Data retrieval data from data sources: MariaDB (Mysqli), etc.
* Retrieval data from data sources is possible as a collection, a model or a simple array
* Filtering and validation of incoming data
* Usage of generic query builder for the preparation and execution of queries to data source
* Renderer of templates via the template engine Smarty (by default - templates for php)
* Performing the action on the route, defined by the requested address (url)
* Override the default settings through creation of the configuration file

Ice project structure
-------------------

      _cache/                 Cache files for separate projects
      _log/                   Log files for separate projects
      _resource/              Resource files (javascript, css, images etc.) for separate projects
      _storage/               File storage
      _vendor/                Vendors (loaded via composer)
      
      Ice/              Ice module (Main required module)
            Config/           Configuration files (php format)
                  Ice/              Configuration files for Ice module
            Resources/        Resource files
                  css/              Css resources
                  Ice/              Resource for Ice module (views, localization files etc.)
                  img/              Image resources 
                  js/               Javascript resources
            Source/           Source files
                  Ice/              Source files for Ice module
                        Action/           Ice actions (Ice:Model_Create, Ice:Resource, Ice:Composer_Update, Ice:Phpdoc_Generate etc.)
                        Core/             Core classes and interfaces
                        Data/
                              Provider/   Implementations of data providers (Ice:Apc, Ice:Redis, Ice:File etc.)
                              Source/     Implementations of data sources (now only Ice:Mysqli)
                        Exception/        Exceptions
                        Form/             Forms
                        Helper            Helpers
                        Query/Translator/ Implementations of query translators (now only Ice:Mysqli)
                        Validator/        Implementations of validators (Ice:Not_Null, Ice:Pattern etc.)
                        View/Render/      Implementations of view renders
                        Bootstrap.php     Bootstrapping class file
                        Core.php          Core trait file
                  Ice.php           Main application class file
            Web/              Web root directory
                  index.php         Directory index file
            app.php           Application run script file
            branch.conf.php   module branch in vcs
            cli               Command line interface
            composer.json     Composer settings
            composer.phar     Composer run script
            
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


Ice Quick Guide
-------------------

      $ curl -s http://getcomposer.org/installer | php
      $ php composer.phar create-project ifacesoft/ice Ice dev-master
      $ ./Ice/cli Ice:Module_Create
      
Good luck! 