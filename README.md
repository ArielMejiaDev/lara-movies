# 🍿 Laramovies
A JsonApi created with Laravel

## About Laramovies

A project created to show my skills creating a Json Api with Laravel 8.

## 📺 Screencast

I added a screencast showing all features of the project: <a href="https://www.loom.com/share/5904eecdd4d6418f9f88bb922caa60a8" target="_blank">Here</a>

## 🚀 Start

You can download the project with git by making a clone on the project:

```bash
git clone https://github.com/ArielMejiaDev/lara-movies
```

Alternatively you can download using the zip button in the repo.

### 📋 Pre-required

You must have:

    * A development environment (valet, homestead, sails, laragon, etc).
    * Git installed in your OS (MacOS include it)
    * Composer installed a link [here](https://getcomposer.org/)
    * PHP 7.4 at least.
    * not required, but would help to have a IDE (VSCode or PHPStorm).
    * some email client service like [Mailtrap](https://mailtrap.io)
    
### 🔧 Installation 

In your terminal go to the directory of the project:

```bash
cd lara-movies
```

Then generate a project key:

```php
php artisan key:generate
```

Create a database

You can create a sqlite database on development/local environment, in the root directory of the project:

```bash
cd database
touch database.sqlite
```

Edit the .env file

You would need to add some env configurations to make the project run, you would change the configurations as your own services and needs, here an example (the tokens on mail service are just an example, those are not really useful I add an example):

```env
APP_NAME="Laramovies"
APP_ENV=local
APP_KEY={the one that generates the command php artisan key:generate }
APP_DEBUG=true
APP_URL=http://lara-movie.test

DB_CONNECTION=sqlite
#DB_HOST=127.0.0.1
#DB_PORT=3306
#DB_DATABASE=lara_movie
#DB_USERNAME=root
#DB_PASSWORD=

MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=273b5ab0b87b
MAIL_PASSWORD=a5622da6eea5
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS='app@lara-movie.com'
MAIL_FROM_NAME="${APP_NAME}"
```

Download all project dependencies:

```php
composer install
```

It would take a while (it depends on your connection).

Migrate the database, I also add seeders to test with fake data.

```php
php artisan migrate:fresh --seed
```

Lastly the project use Passport to authenticate via JWT, so you need to run the command:

```php
php artisan passport:install
```

## ⚙️ Executing tests 

The project become with 101 tests, in the root project directory, you can execute on terminal the command:

```php
php artisan test
```

## 📦 Deployment 

I would recomend different services like [Forge](https://forge.laravel.com).

But here I will add a guide to make a [deployment on Heroku for free](https://devcenter.heroku.com/articles/getting-started-with-laravel).

## 🛠️ Built with 

* [PHP](https://www.php.net/).
* [Laravel 8](https://laravel.com).


## 📌 Version 

The current version of this project is 0.0.1

## ✒️ Author 

[Ariel Mejia Dev](https://github.com/ArielMejiaDev).

---
⌨️ con ❤️ por [Ariel Mejia Dev](https://github.com/ArielMejiaDev) 😊
