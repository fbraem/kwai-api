Kwai-api
========

Kwai-api is a part of the kwai system. The ultimate goal of Kwai is to manage a (sports)club. The focus is currently on judo sport (Kwai means club in Japanese), but in the future it may be possible to support
other sports.

The frontend of kwai can be found in the [kwai-ui](https://github.com/fbraem/kwai-ui) repository.

> Kwai is a greenfield project. As long as there is no official release,
> everything can change. Although there is still a lot to do, kwai is already used in production for
> our club but that is no guarantee that it will work for you...

[Jetbrains](https://www.jetbrains.com/?from=kwai-api) allows Kwai-api to use phpstorm for development!

<img alt="jetbrains" src="jetbrains.png" width="200px" />

API
===
Kwai-api is the REST api for Kwai. It's written in PHP.
The [JSONAPI](http://jsonapi.org) standard will be followed as best as 
possible: Links are not used and URL's are not always as defined.

Currently the following api's are already available:

- news
- pages
- members
- teams
- trainings

TODO
====

There is still a lot to do:

- tournament management
- member follow-up system
- events
- ...

Kwai is currently more CRUD then domain oriented. This api must evolve from an anemic model to real DDD.

Installation
============

Clone this repository and run `composer install` in the `src` as current directory. When all goes well, create a `config.php` in the `api` directory. This PHP file must return an array with some configuration:

> For now we are using two types of database code: the CakePHP ORM and the
> repository pattern with [Latitude](https://latitude.shadowhand.com/). That's
> why there are two DSN properties in the configuration. In the long run
> the repository code will be the only code left.

    return [
        'database' => [
            'development' => [
                'cake_dsn' => 'mysql://',
                'dsn' => 'mysql:',
                'adapter' => 'mysql',
                'host' => '',
                'user' => '',
                'pass' => '',
                'name' => '',
                'charset' => 'utf8',
                'prefix' => ''
            ]
        ],
        'default_database' => 'development',
        'logger' => [
            'kwai' => [
                'file' => '/var/tmp/kwai.log',
                'level' => Logger::DEBUG
            ],
            'database' => [
                'file' => '/var/tmp/kwai_db.log',
                'level' => Logger::DEBUG
            ]
        ],
        'files' => [
            'local => '/var/www...',
            'url => ''
        ],
        'mail' => [
            'host' => '',
            'user' => '',
            'pass' => '',
            'port' => 2525,
            'from' => [ ],
            'subject' => ''
        ],
        'website' => [
            'url' => '',
            'email' => ''
        ],
        'cors' => [
            'origin' => []
        ]
    ];

When the configuration is finished, run the database migrations from the `src` directory:

    ./vendor/bin/phinx migrate -c phinx.php

On shared hosting:

    php ./vendor/robmorgan/phinx/phinx.php migrate -c phinx.php

Running Tests
=============

A testing environment is easily set up with [vagrant](https://www.vagrantup.com).
Copy `kwai.dest.yaml` to `kwai.development.yaml` and fill in the properties.
Kwai uses [pest](https://pestphp.com/) to run tests:

    vagrant up
    vagrant ssh KWAI_API
    cd /vagrant/src
    ./vendor/bin/pest
