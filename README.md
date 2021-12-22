Kwai-api
========

Kwai-api is part of the kwai system. The ultimate goal of Kwai is to manage a 
(sports)club. The focus is currently on judo sport (Kwai means club in 
Japanese), but in the future it may be possible to support other sports.

The frontend of kwai can be found in the
[kwai-vite](https://github.com/fbraem/kwai-vite) repository.

> Kwai is a greenfield project. As long as there is no official release,
> everything can change. Although there is still a lot to do, kwai is already
> used in production for our club but that is no guarantee that it will work 
> for you...

[Jetbrains](https://www.jetbrains.com/?from=kwai-api) allows Kwai-api to use
phpstorm for development!

<img alt="jetbrains" src="jetbrains.png" width="150px" />

API
===
Kwai-api is the REST api for Kwai. It's written in PHP.
The [JSONAPI](http://jsonapi.org) standard is followed as best as 
possible. A separate PHP module [kwai/jsonapi](https://github.com/fbraem/kwai-jsonapi)
is written for this.

Currently the following api's are already available:

- news
- pages
- trainings

TODO
====

There is still a lot to do:

- tournament management
- teams
- member follow-up system
- events
- ...

Installation
============
Clone this repository.

Deployer
--------

> To be able to deploy on a (shared) host SSH must be setup. The article
> ["Deploying a Symfony application with Deployer"](https://dev.to/andersbjorkland/deploying-a-symfony-application-with-deployer-afe)
> can help.

Install [deployer](https://deployer.org/) (version 6.x)
and create a deployer configuration file. The `hosts.yml` file can be used as an
example. This file can look like this:

````yaml
kwai:
  stage: production
  user: 
  deploy_path: ~/{{application}}_pro
  public_path: /www
  http_user: 
````

`application` contains the value 'kwai_api'.

Run deployer from the folder where hosts.yml is located:

````shell
dep deploy production
````

When the deploy is successful, the deploy_path will contain a `shared` and a
`releases` folder and a symbolic link `current`. The symbolic link will point
to the latest deployed application code. The shared folder contains folders
and files that will be shared between different releases. In this folder the 
config folder is used to store `config.php`. Use `config.dist.php` to create
a config.php for this installation.

In the public_path, the api php entry files will be copied into the api folder.
These files will be overwritten on each deploy. In the public path an autoload
PHP script will be created. This autoload script will load the
vendor/autoload.php file.

The database migration is currently not executed after a deploy. When a 
migration is needed, go to the src folder in the current folder of the 
deploy_path and run it manually:

````shell
../vendor/bin/phinx migrate -c ./phinx.php
````

Manual
------
The recommended way of installing kwai-api is using deployer, but it is also
possible to install it manually.

Run `composer install` in the folder where the repository is cloned. Copy all 
folders api, src, config and vendor to the host. Change the config.dist.php
file in the config folder to config.php and change the configuration.

To run a migration of the database, you need access to your host and run it
manually from the src folder on the host:

````shell
../vendor/bin/phinx migrate -c ./phinx.php
````

Development
============

Clone this repository in a folder. The easiest way to setup a development
environment is to use [vagrant](https://www.vagrantup.com/). 

Copy `kwai.dist.yaml` to `kwai.development.yaml` and fill in the properties.
Run `vagrant up` from the repository folder and everything will be 
installed automatically.

Running Tests
=============

Kwai uses [pest](https://pestphp.com/) to run tests:

    vagrant up
    vagrant ssh KWAI_API
    cd /vagrant
    ./vendor/bin/pest -c ./tests/phpunit.xml

> TIP: use an IDE like [PHPStorm](https://www.jetbrains.com/phpstorm/?from=kwai-api) 
> to develop, test and run kwai-api.
