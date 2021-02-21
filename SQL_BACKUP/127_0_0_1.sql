-- phpMyAdmin SQL Dump
-- version 5.0.4
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Фев 21 2021 г., 14:43
-- Версия сервера: 5.7.29
-- Версия PHP: 7.1.33

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `social_network`
--
CREATE DATABASE IF NOT EXISTS `social_network` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `social_network`;

-- --------------------------------------------------------

--
-- Структура таблицы `comments`
--

CREATE TABLE `comments` (
  `id` int(11) UNSIGNED NOT NULL,
  `comment` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `posted_at` datetime NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `comments`
--

INSERT INTO `comments` (`id`, `comment`, `user_id`, `posted_at`, `post_id`) VALUES
(1, 'Hello, this is comment', 2, '2021-02-02 22:25:38', 6),
(2, 'And another comment!', 2, '2021-02-02 22:25:46', 6),
(3, 'cool!\r\n', 2, '2021-02-02 22:25:50', 6);

-- --------------------------------------------------------

--
-- Структура таблицы `followers`
--

CREATE TABLE `followers` (
  `id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `follower_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `followers`
--

INSERT INTO `followers` (`id`, `user_id`, `follower_id`) VALUES
(6, 1, 2),
(7, 3, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `login_tokens`
--

CREATE TABLE `login_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `token` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `login_tokens`
--

INSERT INTO `login_tokens` (`id`, `token`, `user_id`) VALUES
(3, '6906814c91ed8f7cde0c75ce5b0f9e253a34c404', 2),
(4, '12252015d1f3be317b0ada68362c734fd68a2724', 2),
(6, '04e8fa469a9b2399cf243eec4404bdc8f5f57241', 2),
(8, '8ed6e17e563f46c247e2371e3913f2c31c2072e7', 1),
(9, '996e231918c832256e581ccd84f14446ecb2f43b', 2),
(10, '303b88df0e111b300a729c1bfbc81b86db3c2b22', 2),
(11, '33dccc4f99da59fffe50c22c71cdaacc88abc080', 1),
(12, 'f0bde6067afa96ca42c57777c09d0c8c127b7d84', 2),
(13, '7ea1e527c04223e2624a3adea0139bd479e5a9a7', 1),
(14, 'ff989fba5f48ac915f3a995dd245678faf28c62c', 2),
(15, '0799d648735a417aae0155bcac739e064162b72e', 3),
(16, 'a0c31008833924ea511b3a7fcd70afc756a66518', 2),
(18, '7057c5342bd65e843c49f25c59ff5d79feab96d4', 2),
(19, '7d6cba797e6abe3fc559c8ab7455a32a12f0e16d', 2);

-- --------------------------------------------------------

--
-- Структура таблицы `password_tokens`
--

CREATE TABLE `password_tokens` (
  `id` int(11) UNSIGNED NOT NULL,
  `token` char(64) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Структура таблицы `posts`
--

CREATE TABLE `posts` (
  `id` int(11) UNSIGNED NOT NULL,
  `body` varchar(256) COLLATE utf8mb4_unicode_ci NOT NULL,
  `posted_at` datetime NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `likes` int(11) UNSIGNED NOT NULL,
  `postimg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `posts`
--

INSERT INTO `posts` (`id`, `body`, `posted_at`, `user_id`, `likes`, `postimg`) VALUES
(1, 'Hello World', '2021-01-25 19:50:10', 2, 1, NULL),
(2, 'Second Post', '2021-01-25 19:52:34', 2, 1, NULL),
(3, 'Third Post!', '2021-01-25 19:55:56', 2, 0, NULL),
(4, 'Test', '2021-01-26 20:15:46', 2, 2, NULL),
(5, 'Hello <b>World</b>', '2021-01-26 20:16:08', 2, 0, NULL),
(6, 'echo $loggedInUserId;', '2021-01-26 21:27:43', 1, 0, NULL),
(7, 'This is a post', '2021-01-26 21:28:01', 1, 0, NULL),
(8, 'Hello guys! This is my first post!', '2021-01-26 21:45:03', 3, 0, NULL),
(9, 'Another test post', '2021-01-26 21:45:40', 3, 0, NULL),
(10, 'NEW TEST POST', '2021-02-01 20:42:18', 2, 1, NULL),
(11, '123', '2021-02-21 13:52:25', 2, 0, NULL),
(12, 'another test post', '2021-02-21 14:10:59', 2, 0, NULL),
(32, '', '2021-02-21 14:39:07', 2, 0, 'https://i.imgur.com/7Lk8kZ9.jpg');

-- --------------------------------------------------------

--
-- Структура таблицы `post_likes`
--

CREATE TABLE `post_likes` (
  `id` int(11) NOT NULL,
  `post_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `post_likes`
--

INSERT INTO `post_likes` (`id`, `post_id`, `user_id`) VALUES
(13, 1, 2),
(14, 4, 1),
(15, 2, 1),
(19, 10, 2),
(23, 4, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `id` int(11) UNSIGNED NOT NULL,
  `username` varchar(32) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(60) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(64) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `verified` tinyint(1) NOT NULL DEFAULT '0',
  `profileimg` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `verified`, `profileimg`) VALUES
(1, 'Verified', '$2y$10$RAQVJfIYIyoattOl4t/5SeM4YsYVhrGB.PP5hTS.B3TUonH7cZStm', 'trezortop92@gmail.com', 0, NULL),
(2, 'TrezorTop', '$2y$10$1ojYzDjwyvlKQafyM9qIWehVoA6AIOB./opR5gN75PWd1xvxsuSrW', 'trezorsix@mail.ru', 0, 'https://i.imgur.com/RPkz3ej.jpg'),
(3, 'Andrey', '$2y$10$gmDkI2UpC2KFG/VgY47Ts.akeYUsfs9dwlCAn1aorMisFF0XlO4BK', 'trezor6@mail.ru', 0, NULL),
(4, 'TEST_USER', '$2y$10$.FKE8Idd693uBB32..Fbaee/K89mKS.IM7IOi6jL/Vo2MrG5lvWpe', 'TEST@mail.com', 0, NULL);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `comments`
--
ALTER TABLE `comments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `post_id` (`post_id`);

--
-- Индексы таблицы `followers`
--
ALTER TABLE `followers`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `password_tokens`
--
ALTER TABLE `password_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`);

--
-- Индексы таблицы `posts`
--
ALTER TABLE `posts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `post_likes`
--
ALTER TABLE `post_likes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `post_id` (`post_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `comments`
--
ALTER TABLE `comments`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `followers`
--
ALTER TABLE `followers`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `login_tokens`
--
ALTER TABLE `login_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT для таблицы `password_tokens`
--
ALTER TABLE `password_tokens`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `posts`
--
ALTER TABLE `posts`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT для таблицы `post_likes`
--
ALTER TABLE `post_likes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Ограничения внешнего ключа сохраненных таблиц
--

--
-- Ограничения внешнего ключа таблицы `comments`
--
ALTER TABLE `comments`
  ADD CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`);

--
-- Ограничения внешнего ключа таблицы `login_tokens`
--
ALTER TABLE `login_tokens`
  ADD CONSTRAINT `login_tokens_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `posts`
--
ALTER TABLE `posts`
  ADD CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Ограничения внешнего ключа таблицы `post_likes`
--
ALTER TABLE `post_likes`
  ADD CONSTRAINT `post_likes_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`),
  ADD CONSTRAINT `post_likes_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
