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
Kwai-api is the REST api for Kwai. It's written in PHP and it tries to follow the [JSONAPI](http://jsonapi.org) standard.

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

    return [
        'database' => [
            'development' => [
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
        'files' => '',
        'oauth2' => [
            'private_key' => 'file:///',
            'public_key' => 'file:///',
            'encryption_key' => '',
            'client' => [
                'name' => '',
                'identifier' => '',
                'secret' => '',
                'redirect' => ''
            ]
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
        ]
    ];

Create a public and private key as explained on [league/oauth2-server](https://oauth2.thephpleague.com/installation/).

When the configuration is finished, run the database migrations from the `src` directory:

    ./vendor/bin/phinx migrate -c phinx.php

On shared hosting:

    php ./vendor/robmorgan/phinx/phinx.php migrate -c phinx.php

Running Tests
=============
Run the following command from the `src\tests` directory:

    ../vendor/bin/phpunit --testdox
