# MyMangaDex - Online Save

Store MyMangaDex saves to be able to sync between devices or browsers.

## Requirements

You need the same requirements as [Lumen](https://lumen.laravel.com/):

* [Composer](http://getcomposer.org/)
* PHP >= 7.1.3
* OpenSSL PHP Extension
* PDO PHP Extension
* mbstring PHP Extension

## Installation

Install all composer dependencies:

```bash
composer update
```

Then you need to set the variables in the ``.env`` file, the required one are ``APP_KEY`` and the ``DB_`` parameters.  
Create the database with the same name you wrote in the ``.env`` file.  
With a database ready, you can create the tables by running:

```bash
php artisan db:create
```

You're ready to go ! You can update the API url in the MyMangaDex options to use the one you just hosted.

### Without console

If you don't have a console, download the dependencies locally and upload the ``/vendor`` folder.

To create the database schema:

* Set a value for ``INSTALL_TOKEN`` in the ``.env`` file
* Uncomment the ``/install`` route in ``routes/web.php``
* Uncomment ``$app->register(App\Providers\AppServiceProvider::class);`` line in ``boostrap/app.php``
* Send a request to ``GET /install`` with a ``X-Auth-Token`` with the value previously set for ``INSTALL_TOKEN``
* After the migration is done you can comment or delete the line you modified, and even delete the ``app/Http/Controllers/InstallController.php`` file

## Authentification

There is two authentification methods.  
The first one, ``Token Auth``, is a single header ``X-Auth-Token`` that contain the token of the user that will be used.  
The second one, ``Credentials Auth``, is two headers ``X-Auth-Name`` and ``X-Auth-Pass`` that contain the username and password of the user.

## Endpoints

| Path | Method | Auth | Description |
|---|---|---|---|
| /user | ``POST`` | None | Register an User. |
| /user/self | ``GET`` | Token | Get informations about the user. |
| /user/self | ``POST`` | Credentials | Update the user. |
| /user/self | ``DELETE`` | Credentials | Delete the User and every titles. |
| /user/self/token | ``GET`` | Credentials | Return the token of the user. |
| /user/self/token/refresh | ``GET`` | Credentials | Generate a new token for the user. |
| /user/self/title | ``GET`` | Token | List of all titles of the user. |
| /user/self/title | ``POST`` | Token | Update all titles of the user. |
| /user/self/title/{mangaDexId} | ``GET`` | Token | Get all informations about a specific title of the user. |
| /user/self/title/{mangaDexId} | ``POST`` | Token | Update last opened chapter of the title {mangaDexId} to the content of the passed object.<br>Also look at the ``options.saveAllOpened`` and ``options.maxChapterSaved`` options to add the chapter to the chapters list and delete old ones if needed. |
| /user/self/options | ``GET`` | Token | Show the options saved online. |
| /user/self/options | ``POST`` | Token | Update options saved online. |
| /user/self/history | ``GET`` | Token | Show the list and all titles in the history of the user. |
| /user/self/history | ``POST`` | Token | Delete all of the current history and update it with a new one. |
| /user/self/history | ``DELETE`` | Token | Delete all of the current history of the user. |
| /user/self/export | ``GET`` | Token | Export all online user data, options, titles with chapters and history. |
| /user/self/import | ``POST`` | Token | Replace all online user data. |

## Data

### User

The minimal data is used to make each accounts unique and safe.

| Name | Value |
|---|---|
| username | The username you will use to login. |
| password | The password of your account. |
| token | Unique token used to authentificate. |
| options | Your MyMangaDex options. |
| titles | The list of saved titles, see [Title](#Title) to find the value for each. |
| last_sync | Date of the last synchronization, when your data has been imported on a new device. |
| last_update | Date of the last update, when the data saved online has been updated. |
| creation_date | Date of the account creation. |

### Title

| Name | Value |
|---|---|
| user | The id of the [User](#User) the title belongs to. |
| mal_id | The MyAnimeList id. |
| md_id | The MangaDex id. |
| last | The last opened chapter. |
| chapters | The list of saved [Chapter](#Chapter) for the title, empty if the option is disabled. |

### Chapter

| Name | Value |
|---|---|
| title_id | ID of the title which this chapter depend on. |
| value | The chapter number. |

### HistoryEntry

| Name | Value |
|---|---|
| user | The id of the [User](#User) the history entry belongs to. |
| md_id | The MangaDex id. |

### HistoryTitle

| Name | Value |
|---|---|
| user | The id of the [User](#User) the history entry belongs to. |
| name | The name of the title. |
| md_id | The MangaDex id. |
| progress   | The progress (last chapter opened) on MangaDex. |
| chapter_id | The chapter id on MangaDex. |

## Usage

An user can register in the options page inside MyMangaDex, sending a ``POST`` request to ``/user`` with at least the username and password, as options are optional.  
Updating informations is done by sending a ``POST`` request to ``/user/self`` with the fields updated as you wish.  
Informations about the user are obtained by sending a ``GET`` request to ``/user/self``, and you can delete all data (titles included) by sending a ``DELETE`` request to ``/user/self``.  

When opening a title page or when reading a chapter, a request could be sent, to update the last open and the optional generated chapters list. The update is done by sending a ``POST`` request to ``/user/self/title/{mangaDexId}`` and a row is created if it does not already exist.  

When Syncing, a ``GET`` request is sent to ``/user/self/export``, and the local storage is updated to reflect the online save.  
On the opposite, when saving online, a ``POST`` request is sent to ``/user/self/import`` and all titles are updated with the received data.

If options are saved online, a ``POST`` request is sent to ``/user/self/options`` with the updated options when saving them, and a ``GET`` request is sent when you import your online options.

## Test server

If you which to test it locally, you can run this command to have a temporary server ready at [localhost:8000](localhost:8000):

```bash
php -S localhost:8000 -t .
```

You can then use ``localhost:8000`` as the API url in your MyMangaDex options.

## Tests

There is more than 70 tests, you can run them by executing ``phpunit`` in the ``vendor/bin`` folder.

> This will modify the database, don't execute test with real data already in the database
