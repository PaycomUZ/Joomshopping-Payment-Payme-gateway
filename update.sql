INSERT INTO #__jshopping_payment_method (`payment_code`, `payment_class`, `payment_publish`, `payment_ordering`, `payment_type`, `price`, `price_type`, `tax_id`, `show_descr_in_email`,`name_en-GB`, `name_de-DE`) VALUES ('pm_payme', 'pm_payme', 1, 0, 2, 0.00, 0, 0, 0, 'pm_payme', 'pm_payme');
UPDATE #__jshopping_payment_method SET `name_ru-RU` = 'Онлайн оплата с помощью сервиса Payme' WHERE `payment_class` = 'pm_payme';
ALTER TABLE #__jshopping_orders ADD `create_time` BIGINT;
ALTER TABLE #__jshopping_orders ADD `perform_time` BIGINT;
ALTER TABLE #__jshopping_orders ADD `cancel_time` BIGINT;