Kwai
====

Kwai is a club/content management system. At the moment it's main focus is a
judo club (kwai means club), but in the future it may be possible to support
other sports.

High level
==========

- club website
    - news
    - events
    - information
- membership management
- team management
- team membership management
- training calendar
- absence / presence registration
- tournament management
- ...

Kwai is already used in production but it is still a work in progress and there
is still a lot of work to do:

- Contains some club specific data which needs to be standardized... It's usable
but you will need to do some work to adopt it to your club ...
- Kwai is currently more CRUD then domain oriented. On the server, Kwai must
evolve from an anemic model to real DDD.
- New technology is used, and that has some consequences. Reasons to refactor
code:
  + New insights in the technology.
  + New releases of code (Vue 3 for example), or totally new packages
   (tailwindcss for example).
  + New insights in patterns.
