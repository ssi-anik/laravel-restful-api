### Laravel Restful API
I was searching for basic auth based RESTful application on Laravel or Lumen. I was trying to learn React & Redux. Couldn't find any.  Was lazy enough to build a new one. As nothing did help me, have to write one for myself. 

#### Installation
This is a dockerized application. Do the following

Not Mandatory but good to have: 
* `docker` & `docker-compose` installed in your PC.

To do:
* Clone this repository.
* `cd` into the cloned repository.
* Copy `docker-compose.yml.example` to `docker-compose.yml`.
* Run `docker-compose up -d --build`.
* Change necessary ports & credentials.
* Copy `.env.example` to `.env`.
* Change necessary values.
* Either ssh into your *container* or run from your local machine `composer install`.
* Either ssh into your *container* or run from your local machine `php artisan key:generate`.
* Either ssh into your *container* or run from your local machine `php artisan migrate --seed`.
* Open your browser & hit to `http://127.0.0.1:PORT_NUMBER`.
