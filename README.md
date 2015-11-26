Geo IP component for Yii2 framework
=================

## Описание

Компонент для работы с базой [ipgeobase.ru](http://ipgeobase.ru)

## Установка

Устанавливать можно через композер [composer](http://getcomposer.org/download/). 

Командой:

```
$ php composer.phar require conquer/select2 "*"
```
или добавить

```
"conquer/select2": "*"
```

в секцию ```require``` файла проекта `composer.json`.

Обязательно запустить миграцию командой:

```
$ yii migrate --migrationPath=@conquer/geoip/migrations
```

## Настройка

Обязательно требуется указать компоненту "geoip".
Дополнительно можно настроить имена таблиц:

```php
 'components' => [
        'geoip' => [
            'class' => 'conquer\geoip\Geoip',
            'cityTable' => 'geoip.city',
            'districtTable' => 'geoip.district',
            'rangeTable' => 'geoip.range',
            'regionTable' => 'geoip.region',
        ],
    ],
```

В конфигурации консольного приложения компоненту необходимо добавить в автозагрузку.

```php
'bootstrap' => ['log', 'geoip'],
```

Для обновления базы данных требуется запустить команду

```
./yii geoip
```

Желательно добавить в крон на раз в сутки.

## Использование

```php
$range = Range::findByIp(\Yii::$app->request->userIP);

echo $range->city->city_name;

```

## License

**conquer/geoip** is released under the MIT License. See the bundled `LICENSE` for details.