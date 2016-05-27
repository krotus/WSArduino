-- phpMyAdmin SQL Dump
-- version 4.5.2
-- http://www.phpmyadmin.net
--
-- Servidor: localhost
-- Tiempo de generación: 20-05-2016 a las 20:11:39
-- Versión del servidor: 10.1.9-MariaDB
-- Versión de PHP: 5.6.15

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;



--
-- Base de datos: `arduino`
--

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `orders`
--

CREATE TABLE `orders` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `description` char(50) NOT NULL,
  `priority` int(11) NOT NULL,
  `date` datetime NOT NULL,
  `quantity` int(11) NOT NULL,
  `id_status_order` int(11) NOT NULL,
  `id_robot` int(11) DEFAULT NULL,
  `id_process` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `orders`
--

INSERT INTO `orders` (`id`, `code`, `description`, `priority`, `date`, `quantity`, `id_status_order`, `id_robot`, `id_process`) VALUES
(1, 99, 'Orde de fabricació prova', 5, '2016-05-13 16:02:10', 4, 1, 1, 1),
(2, 100, 'Ordre de fabricacio prova 1', 6, '2016-05-13 16:02:10', 2, 4, 1, 1),
(3, 101, 'Ordre de fabricació prova 2', 1, '2016-05-13 16:02:10', 5, 2, 5, 3),
(4, 102, 'Ordre de fabricació prova 3', 9, '2016-05-13 16:02:10', 1, 2, 1, 6),
(5, 103, 'Ordre de fabricació prova 4', 7, '2016-05-13 16:02:10', 6, 3, 1, 2),
(6, 104, 'Ordre de fabricació prova 5', 2, '2016-05-13 16:02:10', 10, 1, 1, 1),
(7, 105, 'Ordre de fabricació prova 6', 5, '2016-05-13 16:02:10', 50, 4, NULL, 1),
(8, 106, 'Ordre de fabricació prova 7', 8, '2016-05-13 16:02:10', 4, 3, 5, 5),
(9, 107, 'Ordre de fabricació prova 8', 10, '2016-05-13 16:02:10', 25, 5, 4, 4),
(10, 108, 'Ordre de fabricació prova 9', 1, '2016-05-13 16:02:10', 15, 3, 1, 6),
(11, 109, 'Ordre de fabricació prova 10', 3, '2016-05-13 16:02:10', 6, 2, 1, 4),
(12, 110, 'Ordre de fabricació prova 11', 8, '2016-05-13 16:02:10', 9, 1, NULL, NULL),
(13, 111, 'Ordre de fabricació prova 12', 4, '2016-05-13 16:02:10', 90, 4, 1, 1),
(14, 112, 'Ordre de fabricació prova 13', 2, '2016-05-13 16:02:10', 53, 1, 4, 3),
(15, 113, 'Ordre de fabricació prova 14', 5, '2016-05-13 16:02:10', 50, 2, 1, 1),
(16, 114, 'Ordre de fabricació prova 15', 7, '2016-05-13 16:02:10', 20, 4, 1, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `points`
--

CREATE TABLE `points` (
  `id` int(11) NOT NULL,
  `pos_x` int(11) NOT NULL,
  `pos_y` int(11) NOT NULL,
  `pos_z` int(11) NOT NULL,
  `tweezer` tinyint(1) NOT NULL,
  `id_process` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `points`
--

INSERT INTO `points` (`id`, `pos_x`, `pos_y`, `pos_z`, `tweezer`, `id_process`) VALUES
(1, 10, 20, 30, 0, 1),
(2, 40, 50, 60, 1, 1),
(3, 30, 35, 45, 0, 3),
(4, 70, 80, 90, 1, 3),
(5, 65, 75, 85, 0, 2),
(6, 5, 15, 25, 0, 2),
(7, 15, 25, 35, 1, 2),
(8, 25, 35, 45, 0, 5),
(9, 3, 13, 23, 0, 2),
(10, 60, 50, 80, 1, 4),
(11, 55, 35, 62, 0, 3),
(12, 20, 68, 72, 1, 4);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `processes`
--

CREATE TABLE `processes` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `description` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `processes`
--

INSERT INTO `processes` (`id`, `code`, `description`) VALUES
(1, 100, 'Procés de prova'),
(2, 101, 'Proces de Fabricació'),
(3, 102, 'Proces de Restauració'),
(4, 103, 'Proces de Retorn'),
(5, 104, 'Proces de destrucció'),
(6, 105, 'Proces de Inicialització');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `robots`
--

CREATE TABLE `robots` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` char(50) NOT NULL,
  `ip_address` char(20) DEFAULT NULL,
  `latitude` double NOT NULL,
  `longitude` double NOT NULL,
  `id_current_status` int(11) NOT NULL,
  `ip_cam` char(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `robots`
--

INSERT INTO `robots` (`id`, `code`, `name`, `ip_address`, `latitude`, `longitude`, `id_current_status`) VALUES
(1, 100, 'First Robot', '192.168.1.3', 14.25487, 21.25467, 1),
(2, 101, 'Second Robot', '192.168.1.4', 15, 20, 2),
(3, 102, 'Third Robot', '192.168.1.5', 14, 21, 3),
(4, 103, 'Fourth Robot', '192.168.1.6', 16, 22, 4),
(5, 104, 'Fifth Robot', '192.168.1.7', 15, 22, 1),
(6, 105, 'Sixth Robot', '192.168.1.8', 16, 21, 1);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_order`
--

CREATE TABLE `status_order` (
  `id` int(11) NOT NULL,
  `description` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `status_order`
--

INSERT INTO `status_order` (`id`, `description`) VALUES
(1, 'pending'),
(2, 'initiated'),
(3, 'completed'),
(4, 'uncompleted'),
(5, 'canceled');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `status_robot`
--

CREATE TABLE `status_robot` (
  `id` int(11) NOT NULL,
  `description` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `status_robot`
--

INSERT INTO `status_robot` (`id`, `description`) VALUES
(1, 'online'),
(2, 'offline'),
(3, 'busy'),
(4, 'disconnected');
-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `languages`
--
CREATE TABLE IF NOT EXISTS `languages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` char(3) COLLATE utf8_unicode_ci NOT NULL,
  `description` char(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `languages`
--

INSERT INTO `languages` (`id`, `code`, `description`) VALUES
(1, 'es', 'Spanish'),
(2, 'ca', 'Catalan'),
(3, 'en', 'English');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `tasks`
--

CREATE TABLE `tasks` (
  `id` int(11) NOT NULL,
  `id_team` int(11) DEFAULT NULL,
  `id_order` int(11) NOT NULL,
  `id_worker` int(11) DEFAULT NULL,
  `date_assignation` datetime DEFAULT NULL,
  `date_completion` datetime DEFAULT NULL,
  `justification` char(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `tasks`
--

INSERT INTO `tasks` (`id`, `id_team`, `id_order`, `id_worker`, `date_assignation`, `date_completion`, `justification`) VALUES
(1, 2, 1, 4, '2016-05-13 15:57:37', NULL, NULL),
(2, 2, 2, 4, '2016-05-13 15:57:37', NULL, 'justifica'),
(3, 2, 3, 5, '2016-05-13 15:57:37', NULL, NULL),
(4, 2, 16, 2, '2016-05-13 15:57:37', NULL, 'falta de temps'),
(5, 2, 4, 5, '2016-05-13 15:57:37', NULL, NULL),
(6, 2, 5, 4, '2016-05-13 15:57:37', '2016-05-13 15:57:38', NULL),
(7, NULL, 12, NULL, NULL, NULL, NULL),
(8, 7, 7, 3, '2016-05-13 15:57:37', NULL, 'justificat'),
(9, 2, 8, 4, '2016-05-13 15:57:37', NULL, NULL),
(10, 5, 9, 2, '2016-05-13 15:57:37', NULL, 'no funciona el robot'),
(11, 2, 10, 4, '2016-05-13 15:57:37', '2016-05-20 22:00:00', NULL);

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `teams`
--

CREATE TABLE `teams` (
  `id` int(11) NOT NULL,
  `code` int(11) NOT NULL,
  `name` char(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `teams`
--

INSERT INTO `teams` (`id`, `code`, `name`) VALUES
(2, 100, 'EquipA'),
(3, 101, 'TeamOne'),
(4, 102, 'TeamTwo'),
(5, 103, 'TeamThree'),
(6, 104, 'TeamFour'),
(7, 105, 'TeamFive');

-- --------------------------------------------------------

--
-- Estructura de tabla para la tabla `workers`
--

CREATE TABLE `workers` (
  `id` int(11) NOT NULL,
  `username` char(50) NOT NULL,
  `password` char(40) NOT NULL,
  `NIF` char(9) NOT NULL,
  `name` char(50) NOT NULL,
  `surname` char(50) NOT NULL,
  `mobile` int(9) DEFAULT NULL,
  `telephone` int(9) DEFAULT NULL,
  `category` char(50) NOT NULL,
  `id_team` int(11) DEFAULT NULL,
  `is_admin` tinyint(1) NOT NULL DEFAULT '0',
  `language` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Volcado de datos para la tabla `workers`
--

INSERT INTO `workers` (`id`, `username`, `password`, `NIF`, `name`, `surname`, `mobile`, `telephone`, `category`, `id_team`, `is_admin`, `language`) VALUES
(1, 'admin', 'a4cbb2f3933c5016da7e83fd135ab8a48b67bf61', '00000000T', 'Xavi', 'Martinez', 633720214, 938665411, 'Gerent', 2, 1, 1),
(2, 'cpineda', 'bd9588a2dd400141d174e3cf824ca9e1d0e1cf0f', '00000000T', 'Carlos', 'Pineda', 666666666, 999999999, 'Frontend Designer', 5, 0, 1),
(3, 'mperez', '4c5c7f6d40b54a9937607f90fa9b0a891314d161', '00000000T', 'Marc', 'Perez', 666666666, 999999999, 'Model Builder', 7, 0, 1),
(4, 'asala', 'a9c5b176004f931eaeb3c9e0a36a3e80ec95e17f', '00000000T', 'Andreu', 'Sala', 666666666, 999999999, 'Framework Manager', 2, 0, 1),
(5, 'jpont', '36170a9e4828a4402c3237a116d6ae357794344f', '00000000T', 'Joan', 'Pont', 666666666, 999999999, 'DAO Designer', 2, 0, 1);

--
-- Índices para tablas volcadas
--

--
-- Indices de la tabla `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `id_robot` (`id_robot`),
  ADD KEY `id_status_order` (`id_status_order`),
  ADD KEY `id_process` (`id_process`);

--
-- Indices de la tabla `points`
--
ALTER TABLE `points`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_process` (`id_process`);

--
-- Indices de la tabla `processes`
--
ALTER TABLE `processes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `robots`
--
ALTER TABLE `robots`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_current_status` (`id_current_status`);

--
-- Indices de la tabla `status_order`
--
ALTER TABLE `status_order`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `status_robot`
--
ALTER TABLE `status_robot`
  ADD PRIMARY KEY (`id`);

--
-- Indices de la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_team` (`id_team`),
  ADD KEY `id_order` (`id_order`),
  ADD KEY `id_worker` (`id_worker`),
  ADD KEY `id_team_2` (`id_team`),
  ADD KEY `id_order_2` (`id_order`);

--
-- Indices de la tabla `teams`
--
ALTER TABLE `teams`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indices de la tabla `workers`
--
ALTER TABLE `workers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `id_team` (`id_team`),
  ADD KEY `id_team_2` (`id_team`);

--
-- AUTO_INCREMENT de las tablas volcadas
--

--
-- AUTO_INCREMENT de la tabla `orders`
--
ALTER TABLE `orders`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;
--
-- AUTO_INCREMENT de la tabla `points`
--
ALTER TABLE `points`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
--
-- AUTO_INCREMENT de la tabla `processes`
--
ALTER TABLE `processes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `robots`
--
ALTER TABLE `robots`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
--
-- AUTO_INCREMENT de la tabla `status_order`
--
ALTER TABLE `status_order`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT de la tabla `status_robot`
--
ALTER TABLE `status_robot`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT de la tabla `tasks`
--
ALTER TABLE `tasks`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;
--
-- AUTO_INCREMENT de la tabla `teams`
--
ALTER TABLE `teams`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;
--
-- AUTO_INCREMENT de la tabla `workers`
--
ALTER TABLE `workers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- Restricciones para tablas volcadas
--

--
-- Filtros para la tabla `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `orders_ibfk_1` FOREIGN KEY (`id_status_order`) REFERENCES `status_order` (`id`),
  ADD CONSTRAINT `orders_ibfk_2` FOREIGN KEY (`id_robot`) REFERENCES `robots` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `orders_ibfk_3` FOREIGN KEY (`id_process`) REFERENCES `processes` (`id`) ON DELETE SET NULL;

--
-- Filtros para la tabla `points`
--
ALTER TABLE `points`
  ADD CONSTRAINT `points_ibfk_1` FOREIGN KEY (`id_process`) REFERENCES `processes` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Filtros para la tabla `robots`
--
ALTER TABLE `robots`
  ADD CONSTRAINT `robots_ibfk_1` FOREIGN KEY (`id_current_status`) REFERENCES `status_robot` (`id`);

--
-- Filtros para la tabla `tasks`
--
ALTER TABLE `tasks`
  ADD CONSTRAINT `tasks_ibfk_1` FOREIGN KEY (`id_worker`) REFERENCES `workers` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_2` FOREIGN KEY (`id_team`) REFERENCES `teams` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `tasks_ibfk_3` FOREIGN KEY (`id_order`) REFERENCES `orders` (`id`);

--
-- Filtros para la tabla `workers`
--
ALTER TABLE `workers`
  ADD CONSTRAINT `workers_ibfk_1` FOREIGN KEY (`id_team`) REFERENCES `teams` (`id`) ON DELETE SET NULL;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
