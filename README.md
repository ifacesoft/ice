[Ice](http://iceframework.net)
===

Ice is a general purpose PHP-framework.
You may fully rely on Ice while developing complex web-applications.
Ice key features are the built-in cache support of the main components,
flexible configuration and the ability to easily extend existing functionality.

History
-------------------

Development of the project began in December 2013.

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

Ice is originally written in the programming language PHP 5.5. Basic functionality is written using namespaces.
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

Directory structure
-------------------

      Action/               All User actions
      Config/               Configuration files
      Core/                 Core classes, interfaces
      Data/
            Provider/       Imlementations of data providers (such as Redis, File, Apc etc.)
            Source/         Imlementations of data sources (such as Mysqli, Defined etc.)
      Helper/               Helper classes
      Model/                Models
      Query/
            Translator/     Imlementations translators for translate query
      Resource/             Project resources (css, js, images, etc.)
      Validator/            Validators
      Vendor/               Third-party libraries
      View/
            Render/         Implementations of view renders


Ice is Great!!!
