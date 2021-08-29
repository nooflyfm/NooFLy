# Meet NooFly!

[NooFly](https://noofly.net) is a free and open-source web based finance manager made to calculate own budget. People who want to make a finance service, can set up NooFly as a service and invite other users.

## Server requirements

- Web server: LAMP | LEMP | WAMP

- PHP version: 7.2.5+

- PHP database extention (which DB you'll use)

- Composer

- Git (for cloning from GitHub)

- BCMath PHP Extension

- Ctype PHP Extension

- Fileinfo PHP Extension

- JSON PHP Extension

- Mbstring PHP Extension

- OpenSSL PHP Extension

- PDO PHP Extension

- Tokenizer PHP Extension

- XML PHP Extension

## Installation

1. Install all needable packages

2. Download the script from the SourceForge or clone it from the GitHub to your 
server's host directory

3. Make **'/public'** directory indexing. For this you have to edit your host file of the server

4. Give the grand permissions of this directory (e.g. **'noofly'**) to the server's user group: `sudo chown -R www-data:www-data noofly`

5. Go from terminal to the script directory

6. Run command: `composer install`

7. Rename the **'env.env'** file to the **'.env'**

8. Edit the next variables in the **'.env'** file:

   1. `APP_URL` - The URL of your NooFly copy

   2. `DB_CONNECTION` - Type of your database (default - **'mysql'**)

   3. `DB_HOST` - Database server URL (default - **'localhost'**)

   4. `DB_PORT` - Database server port (default - **'3306'**)

   5. `DB_DATABASE` - Database name

   6. `DB_USERNAME` - Database user

   7. `DB_PASSWORD` - Database user's password

9. From the project directory in terminal run: `php artisan:migrate`

10. Go to your app url and register in the system

11. After registration you can manipulate with next **'.env'** fields:

    1. `USER_REGISTRATION` - Enable/disable user registration (`1`/`0`)

    2. `AS_SERVICE` - Enable/disable administration of the system (`1`/`0`). It needs only if you want to deploy your service and invite other people there.

    3. `ADMIN_USER_ID` - User ID, who can administrate the service.

    4. `DEMO_MODE` - Enable/disable demonstration mode (`1`/`0`). In this mode some functions are disabled.

12. Start using the system!

## NooFly Sponsors

We would like to extend our thanks to the following sponsors for funding NooFly development. If you are interested in becoming a sponsor, please visit the NooFly [Patreon page](https://patreon.com/waxmaxweb).

## License
[GNU General Public License version 3](https://opensource.org/licenses/GPL-3.0)