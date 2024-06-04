Start Dash Program is based on Laravel 10

## About Start Dash Program

Start Dash Program is the customized Laravel Project which has prepared environments, components designed for the specific DB schemes, source code easy to be diverted.  
You can develop API and Web application instantly with Start Dash Program.

Install tools below on your local PC before starting.

- [Git](https://git-scm.com/)
- [Docker](https://www.docker.com/)

We recommend [VS Code](https://code.visualstudio.com/) as source code editor.

### Additional Features not in laravel default
 - Artisan Command (Repository, Usecase, DTO)
 - Exception Handler
 - Pint Config
 - Override Soft Deletes Trait


## 1. How to setup developing environment


#### 1-1. VSCode

Plug-ins for PHP debug are recommended by ".vscode/extensions.json" when you open the project with VSCode.  
Install them if you use VSCode.


#### 1-2. Create .env file for Docker

```
$ cp .env.example .env
```


#### 1-4. Start up Docker

Build images, create and start containers.
```
$ docker-compose up -d
```
When needed to rebuild.
```
$ docker-compose build --no-cache
```


#### 1-5. Laravel container

Connect to the Laravel container.  
Need to create vendor directory and node_modules directory.
```
$ docker-compose exec laravel bash
(laravel)$ composer install
(laravel)$ php artisan key:generate
(laravel)$ php artisan config:clear
(laravel)$ exit
$ docker-compose stop
$ docker-compose up
```
([Composer](https://github.com/composer/composer) and [npm](https://www.npmjs.com/) are pre-installed in the container.)


#### 1-6. MySQL container

Connect to the MySQL container on another console tab.
```
$ docker-compose exec mysql bash
(mysql)$ mysql -u root -h 127.0.0.1 -p
Enter password: secret
mysql> use (your DB name)
mysql> ※ Execute any SQL.
mysql> exit
(mysql)$ exit
```


#### 1-7. Docker commands for frequent use.

Start up containers.
```
$ docker-compose up -d
```
Stop containers.
```
$ docker-compose stop
```
Delete stopped containers.
```
$ docker-compose rm
```
Stop and delete containers.
```
$ docker-compose down
```


## 2. How to commit
Create a branch from develop branch following [A successful Git branching model](https://nvie.com/posts/a-successful-git-branching-model/).

Before you commit on Git, analyze and fix source code by [laravel/pint](https://readouble.com/laravel/10.x/ja/pint.html).
```
$ php vendor/bin/pint
```
