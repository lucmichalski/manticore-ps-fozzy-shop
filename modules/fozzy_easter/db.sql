-- phpMyAdmin SQL Dump
-- version 4.8.5
-- https://www.phpmyadmin.net/
--
-- Хост: localhost
-- Время создания: Июл 30 2019 г., 09:45
-- Версия сервера: 5.5.60-MariaDB
-- Версия PHP: 7.1.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `admin_test`
--

-- --------------------------------------------------------

--
-- Структура таблицы `ps_fozzy_easter_clients`
--

DROP TABLE IF EXISTS `ps_fozzy_easter_clients`;
CREATE TABLE `ps_fozzy_easter_clients` (
  `id_client_s` int(11) NOT NULL,
  `id_client` int(11) DEFAULT NULL,
  `id_product_s` int(11) NOT NULL,
  `cartrule` text,
  `id_product` int(11) DEFAULT NULL,
  `cart_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `ps_fozzy_easter_products`
--

DROP TABLE IF EXISTS `ps_fozzy_easter_products`;
CREATE TABLE `ps_fozzy_easter_products` (
  `id_product_s` int(10) NOT NULL,
  `id_product` int(11) NOT NULL,
  `active` int(1) NOT NULL DEFAULT '1',
  `sex` int(1) NOT NULL DEFAULT '0',
  `new_client` int(1) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL,
  `products_to` text,
  `category_to` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `ps_nv_smileclients`
--
ALTER TABLE `ps_fozzy_easter_clients`
  ADD PRIMARY KEY (`id_client_s`),
  ADD UNIQUE KEY `id_clients` (`id_client_s`),
  ADD KEY `id_client` (`id_client`);

--
-- Индексы таблицы `ps_fozzy_easter_products`
--
ALTER TABLE `ps_fozzy_easter_products`
  ADD PRIMARY KEY (`id_product_s`),
  ADD UNIQUE KEY `id_product_s` (`id_product_s`),
  ADD KEY `id_product` (`id_product`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `ps_nv_smileclients`
--
ALTER TABLE `ps_fozzy_easter_clients`
  MODIFY `id_client_s` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `ps_nv_smileproducts`
--
ALTER TABLE `ps_fozzy_easter_products`
  MODIFY `id_product_s` int(10) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
