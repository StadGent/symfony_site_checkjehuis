# Check je huis

Check-je-huis is a symfony application that was originally created for
<https://klimaat.stad.gent/checkjehuis/>. It allows a user to check the
parameters of his house and get recommendations on how to improve it.

## Functionality
Todo!

## Requirements
Minimum PHP 7.2 is required and composer to perform the installation.

## Installation

The following steps can be followed to install the application
1. Clone this repository and go to the directory of the cloned repo
2. `composer install`
3. At the end of `composer install` create a `.env` file based on `.env.dist`.
4. `bin/console doctrine:migrations:migrate`
5. Create the admin user:

    5.1. `bin/console fos:user:create` and answer the prompts
    
    5.2. `bin/console fos:user:promote` and enter the username of the user you
       created in step 5.1 when prompted. Enter `ROLE_ADMIN` when prompted for
       the role.
6. `bin/console cache:clear && bin/console cache:warmup`
7. Install the yarn dependencies: `yarn install`
8. Build the assets: `yarn run encore prod`

After the steps above are taken, make sure the application is reachable through
a URL. Make sure the vhost points to the app's `public` folder.

## Configuration files

The main configuration files reside in `/config`.

The settings in the main files will be overridden with the config of the
environment (e.g.: `config/packages/prod/doctrine.yml`).

The parameters from `.env` will then be replaced in the resulting config.

## Command line tools

Overview of the most used commands:

* `bin/console -e=prod` or `bin/console -env=prod`
    * for production, always explicitly set the environment to prod
* `bin/console list` lists all available commands, optionally filtered by
package
    * e.g.: `bin/console list doctrine:migrations` only lists the doctrine
migration commands
* `bin/console -e=prod cache:clear && bin/console -e=prod cache:warmup` clears
and rebuilds basic bootstrap cache
* `bin/console -e=prod doctrine:migrations:status` checks if the database is
up-to-date
* `bin/console -e=prod doctrine:migrations:migrate` update the database to the
latest version
* `bin/console -e=prod fos:user:change-password` update the password for a user
by username

## Important remarks

* This application is based on Symfony 4. Pull requests are welcome!
* There are integrations with other websites and systems still present in this
codebase. It can't be used as is ATM. We  will look into making this more
generic in the future, but can't guarantee any dates.
