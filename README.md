###Установка:

Добавляем в `composer.json`:

```json
{
    "require-dev": {
        "naxel/zfs-mail": "dev-master"
    }
}
```

И обновляем зависимость:

    $ curl -s http://getcomposer.org/installer | php
    $ php composer.phar update


В config\autoload\global.php

указываем SMTP настройки, дефолтные мыло и имя отправителя, а также, если необходимо заголовки:

```php
return array(
//...
    'mail' => array(
        'transport' => array(
            'host' => '127.0.0.1',
            'port' => '2525'
        ),
        'defaultFrom' => array(
            'email' => 'zfstarter@nixsolutions.com',
            'name' => 'Star Sender'
        ),
        'headers' => array(
            'PROJECT' => 'zfstarter',
        ),
    ),
);
```

В config\autoload\application.config.php
включаем модуль
```php
    'modules'  => array(
        //...
        'ZFStarterMail'
    ),
);
```

Также нужно убедиться, что у вас уже создана под него табличка:
```sql
CREATE TABLE `mail_templates` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `alias` varchar(255) NOT NULL,
  `description` varchar(512) DEFAULT NULL,
  `subject` varchar(255) DEFAULT NULL,
  `bodyHtml` text NOT NULL,
  `bodyText` text NOT NULL,
  `fromEmail` varchar(255) DEFAULT NULL,
  `fromName` varchar(255) DEFAULT NULL,
  `signature` enum('true','false') NOT NULL DEFAULT 'true',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `updated` timestamp NULL DEFAULT NULL,
  `creator` int(11) NOT NULL,
  `updater` int(11) DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `mail_templates_unique` (`alias`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8
```

###Отправка мыла:
```php
use ZFStarterMail\Model\Mail;
//...
    $data = array(
        'templateName' => 'nameOfTemplateFromBd',
        'toEmail' => 'to@nixsolutions.com',
        'toName' => 'No-reply',
        'params' => array(
            'firstName' => 'Vasya',
            'lastName' => 'Pupkin',
            'host' => $_SERVER['HTTP_HOST'],
        ),
    );
    Mail::sendMail($this->getServiceLocator(), $data);
```
