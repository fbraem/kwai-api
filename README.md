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
Clone this repository. There are two ways of installing kwai-api: using
Deployer or do a manual install.

Deployer
--------

> This information is based on the article
> ["Deploying a Symfony application with Deployer"](https://dev.to/andersbjorkland/deploying-a-symfony-application-with-deployer-afe).

### ssh
Whenever deployer connects to the server, it will prompt for a password. And when you are using deploy tools it 
can be entirely cumbersome. This can be avoided by setting up a public and private key.

On your local environment use **ssh-keygen** to generate a key. Navigate to the ~/.ssh folder (
if you don't have that folder, create it with `mkdir ~/.ssh`) and run ssh-keygen. This will start
the key-generation. It will prompt you for a name for the keys. Either enter one or accept the 
default (id_rsa). Then it will prompt you for a passphrase if you would like to add one to 
the keys. If not, it's just as simple as pressing enter twice.

````shell
cd ~/.ssh
ssh-keygen
````

Next we are going to add the public key to the remote server. First we will copy the contents 
of the key. Cat the file (with the extension .pub) you have named when running ssh-keygen. Copy the text string.

Login to your remote server. Go to the ~/.ssh folder. Create a file to store the public key.
````shell
cd ~/.ssh
touch authorized_keys
nano authorized_keys
````
Paste the contents of the public key file into this file and save it.

You can now exit the connection to the remote server.

Next up we are going to configure an SSH connection to the remote server, so we don't have to 
type host, username, and password each time. Create a config file in the ~/.ssh folder.

````shell
cd ~/.ssh
touch config
nano config
````
The config file can look like this when the host is ssh.example.com:
````
Host example
HostName ssh.example.com
User example.com
IdentityFile /c/Users/example/.ssh/example
````
The IdentityFile should be the name you have used in ssh-keygen.
This example can be tested as follows: `ssh example`.

### Install Deployer
Install [deployer](https://deployer.org/) (version 6.x)
and create a deployer configuration file. The `hosts.yml` file can be used as an
example. This file can look like this:

> Currently, Deployer 6.x is used.

### Deployer Configuration

````yaml
kwai:
  stage: production
  user: 
  deploy_path: ~/{{application}}_pro
  public_path: /www
  http_user: 
````

`application` contains the value 'kwai_api'.

### Deploy
Run deployer from the folder where hosts.yml is located:

````shell
dep deploy production
````

When deploy is successful, the deploy_path will contain a `shared` and a
`releases` folder and a symbolic link `current`. The symbolic link will point
to the latest deployed application code. The shared folder contains folders
and files that will be shared between different releases. In this folder the 
config folder is used to store `.kwai`. Use `.kwai.dist` to create
a .kwai file for this installation.

In the public_path, the api php entry files will be copied into the api folder.
These files will be overwritten on each deploy. In the public path an autoload
PHP script will be created. This autoload script will load the
vendor/autoload.php file.

The database migration is currently not executed after a deployment. When a 
migration is needed, go to the src folder in the current folder of the 
deploy_path on the (shared) host and run it manually:

````shell
../vendor/bin/phinx migrate -c ./phinx.php
````

> **Remark:** The initial user/password to login will be printed.

Manual
------
The recommended way of installing kwai-api is using deployer, but it is also
possible to install it manually.

Run `composer install` in the folder where the repository is cloned. Copy all 
folders api, src, config and vendor to the host. Rename the config.dist.php
file in the config folder into config.php and change the configuration.

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
Create a `.kwai` file in the config folder (use the `.kwai.dist` file as example).
Run `vagrant up` from the repository folder and everything will be 
installed automatically.

Running Tests
=============

Copy `phpunit.xml.dist` to `phpunit.xml`.

Add the user/password used for logging in to `.kwai`

    # Test user
    KWAI_TEST_USER=
    KWAI_TEST_PASSWORD=

Kwai uses [pest](https://pestphp.com/) to run tests:

    vagrant up
    vagrant ssh KWAI_API
    cd /vagrant
    ./vendor/bin/pest -c ./tests/phpunit.xml

Credits
=======
+ [PHPStorm](https://www.jetbrains.com/phpstorm/?from=kwai-api)
