<?php
//защита от прямого доступа
defined('_JEXEC') or die();

//определяем константы для русского языка
define('_JSHOP_CFG_PAYME_MERCHANT_ID', 'Merchand ID');
define('_JSHOP_CFG_PAYME_SECRET_KEY', 'Секретный ключ');
define('_JSHOP_CFG_PAYME_SECRET_KEY', 'Статус заказа для подтвержденных транзакций');
define('_JSHOP_PAYME_METHOD_ID', 'ID метода оплаты');
define('_JSHOP_PAYME_DB_HOST', 'Хост базы данных (обычно localhost)');
define('_JSHOP_PAYME_DB_NAME', 'Имя базы данных');
define('_JSHOP_PAYME_DB_SUFFICS', 'Префикс таблиц базы данных');
define('_JSHOP_PAYME_DB_USER', 'Имя пользователя базы данных');
define('_JSHOP_PAYME_DB_PASS', 'Пароль пользователя базы данных');
define('_JSHOP_PAYME_PAID', 'Статус заказа ОПЛАЧЕН');
define('_JSHOP_PAYME_PENDING', 'Статус заказа В ОЖИДАНИИ');
define('_JSHOP_PAYME_CANCELED', 'Статус заказа ОТМЕНЕН');
define('_JSHOP_PAYME_CANCELED', 'Статус заказа ОТМЕНЕН');
define('_JSHOP_PAYME_REFUNDED', 'Статус заказа ВОЗВРАЩЕН');
define('_JSHOP_PAYME_CONFIRMED', 'Статус заказа ПОДТВЕРЖДЕН');
define('_JSHOP_ERROR_TRANS', 'Статус заказа ПОДТВЕРЖДЕН');