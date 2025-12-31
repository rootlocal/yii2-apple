# Яблоки

## Установка, конфиги

~~~shell
./init
composer update
~~~

common/config/ main-local.php:

~~~php
'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=mysql.lan;dbname=yii2_apple',
            'username' => 'admin',
            'password' => 'admin',
            'charset' => 'utf8',
        ],
~~~

~~~shell
./yii migrate
 ./yii serve -t @backend/web 0.0.0.0
 ~~~

## пароли

~~~
admin:admin
manager:manager
customer:customer
~~~