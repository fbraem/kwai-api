Kwai-api
========

Kwai-api is a part of the kwai system. The ultimate goal of Kwai is to manage a (sports)club. The focus is currently on judo sport (Kwai means club in Japanese), but in the future it may be possible to support
other sports.

The frontend of kwai can be found in the [kwai-ui](https://github.com/fbraem/kwai-ui) repository.

API
===
Kwai-api is the REST api for Kwai. It's written in PHP and it tries to follow the [JSONAPI](http://jsonapi.org) standard.

Currently the following api's are already available:

- news
- pages
- members
- teams
- trainings

Although there is still a lot to do, kwai is already used in production for our club.

TODO
====

There is still a lot to do:

- tournament management
- member follow-up system
- events
- ...

Kwai is currently more CRUD then domain oriented. This api must evolve from an anemic model to real DDD.
