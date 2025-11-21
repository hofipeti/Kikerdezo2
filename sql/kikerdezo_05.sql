-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Gép: mysql:3306
-- Létrehozás ideje: 2025. Nov 20. 11:07
-- Kiszolgáló verziója: 8.0.44
-- PHP verzió: 8.3.27

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Adatbázis: `kikerdezo`
--

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feladat`
--

CREATE TABLE `feladat` (
  `feladat_id` int NOT NULL,
  `user_fk` int NOT NULL,
  `start_at` datetime DEFAULT CURRENT_TIMESTAMP,
  `end_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `feladat_tipus`
--

CREATE TABLE `feladat_tipus` (
  `feladat_tipus_fk` int NOT NULL,
  `kod` varchar(1) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `megnevezes` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

--
-- A tábla adatainak kiíratása `feladat_tipus`
--

INSERT INTO `feladat_tipus` (`feladat_tipus_fk`, `kod`, `megnevezes`) VALUES
(1, 'G', 'Gyakorlás'),
(2, 'V', 'Vizsga');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `kerdes`
--

CREATE TABLE `kerdes` (
  `kerdes_id` int NOT NULL,
  `feladat_fk` int NOT NULL,
  `szo_fk` binary(16) NOT NULL,
  `valasz` text CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `start_at` datetime NOT NULL,
  `end_at` datetime NOT NULL,
  `helyes` bit(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `nyelv`
--

CREATE TABLE `nyelv` (
  `nyelv_id` int NOT NULL,
  `kod` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `megnevezes` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

--
-- A tábla adatainak kiíratása `nyelv`
--

INSERT INTO `nyelv` (`nyelv_id`, `kod`, `megnevezes`) VALUES
(1, 'hu', 'magyar'),
(2, 'en', 'english');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szo`
--

CREATE TABLE `szo` (
  `szo_id` binary(16) NOT NULL,
  `szo` text CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `nyelv_fk` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

--
-- A tábla adatainak kiíratása `szo`
--

INSERT INTO `szo` (`szo_id`, `szo`, `nyelv_fk`) VALUES
(0x781e6bc6c2d011f0a9d24ee63b3592fe, 'autó', 1),
(0x781e6bc6c2d011f0a9d24ee63b3592fe, 'car', 2),
(0xb3964fe31ae14bd1991b9d36b9bd5657, 'key', 1),
(0xb3964fe31ae14bd1991b9d36b9bd5657, 'kulcs', 2),
(0xe477a4fa9dd74505939c3e0b6082ebd4, 'kutya', 1),
(0xe477a4fa9dd74505939c3e0b6082ebd4, 'dog', 2),
(0xfa7ae5ed76a346c5b6cfb99a77d9dc2d, 'alma', 1),
(0xfa7ae5ed76a346c5b6cfb99a77d9dc2d, 'apple', 2);

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szolista`
--

CREATE TABLE `szolista` (
  `szolista_fk` int NOT NULL,
  `feladat_fk` int NOT NULL,
  `szo_fk` binary(16) NOT NULL,
  `nyelv` varchar(2) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szotar`
--

CREATE TABLE `szotar` (
  `szotar_id` int NOT NULL,
  `user_fk` int NOT NULL,
  `nyelv1_fk` int NOT NULL,
  `nyelv2_fk` int NOT NULL,
  `megnevezes` varchar(200) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

--
-- A tábla adatainak kiíratása `szotar`
--

INSERT INTO `szotar` (`szotar_id`, `user_fk`, `nyelv1_fk`, `nyelv2_fk`, `megnevezes`, `created_at`) VALUES
(4, 1, 1, 2, 'Mai', '2025-11-19 22:26:07');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `szotar_szo`
--

CREATE TABLE `szotar_szo` (
  `szotar_szo_id` int NOT NULL,
  `szotar_fk` int NOT NULL,
  `szo_fk` binary(16) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

--
-- A tábla adatainak kiíratása `szotar_szo`
--

INSERT INTO `szotar_szo` (`szotar_szo_id`, `szotar_fk`, `szo_fk`, `created_at`) VALUES
(1, 1, 0x781e6bc6c2d011f0a9d24ee63b3592fe, '2025-11-19 22:41:32'),
(2, 4, 0x3c54666daf2b49c2a1182a791c684a34, '2025-11-19 23:11:34'),
(3, 4, 0xb3964fe31ae14bd1991b9d36b9bd5657, '2025-11-19 23:11:59'),
(4, 4, 0xfa7ae5ed76a346c5b6cfb99a77d9dc2d, '2025-11-19 23:13:04'),
(5, 4, 0xe477a4fa9dd74505939c3e0b6082ebd4, '2025-11-19 23:23:51');

-- --------------------------------------------------------

--
-- Tábla szerkezet ehhez a táblához `user`
--

CREATE TABLE `user` (
  `user_id` int NOT NULL,
  `login` varchar(20) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `nev` varchar(50) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL,
  `password` char(64) CHARACTER SET utf8mb3 COLLATE utf8mb3_hungarian_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_hungarian_ci;

--
-- A tábla adatainak kiíratása `user`
--

INSERT INTO `user` (`user_id`, `login`, `nev`, `password`) VALUES
(1, 'malacka', 'Kis Malac', '5994471abb01112afcc18159f6cc74b4f511b99806da59b3caf5a9c173cacfc5');

--
-- Indexek a kiírt táblákhoz
--

--
-- A tábla indexei `feladat`
--
ALTER TABLE `feladat`
  ADD PRIMARY KEY (`feladat_id`),
  ADD KEY `idx_feladat_user` (`user_fk`);

--
-- A tábla indexei `kerdes`
--
ALTER TABLE `kerdes`
  ADD PRIMARY KEY (`kerdes_id`),
  ADD KEY `ixd_kerdes_feladat` (`feladat_fk`);

--
-- A tábla indexei `nyelv`
--
ALTER TABLE `nyelv`
  ADD PRIMARY KEY (`nyelv_id`),
  ADD KEY `ixd_nyelvkod` (`kod`);

--
-- A tábla indexei `szo`
--
ALTER TABLE `szo`
  ADD UNIQUE KEY `idx_szo` (`szo_id`,`nyelv_fk`) USING BTREE;

--
-- A tábla indexei `szolista`
--
ALTER TABLE `szolista`
  ADD PRIMARY KEY (`szolista_fk`),
  ADD UNIQUE KEY `idx_szolista_szo_nyelv` (`szolista_fk`,`szo_fk`,`nyelv`),
  ADD KEY `idf_szolita_feladat` (`feladat_fk`) USING BTREE;

--
-- A tábla indexei `szotar`
--
ALTER TABLE `szotar`
  ADD PRIMARY KEY (`szotar_id`),
  ADD UNIQUE KEY `idx_szotar_user_fk` (`user_fk`,`megnevezes`) USING BTREE;

--
-- A tábla indexei `szotar_szo`
--
ALTER TABLE `szotar_szo`
  ADD PRIMARY KEY (`szotar_szo_id`),
  ADD UNIQUE KEY `idx_szotar_szo` (`szotar_fk`,`szo_fk`) USING BTREE;

--
-- A tábla indexei `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `idx_login` (`login`);

--
-- A kiírt táblák AUTO_INCREMENT értéke
--

--
-- AUTO_INCREMENT a táblához `feladat`
--
ALTER TABLE `feladat`
  MODIFY `feladat_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `kerdes`
--
ALTER TABLE `kerdes`
  MODIFY `kerdes_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `nyelv`
--
ALTER TABLE `nyelv`
  MODIFY `nyelv_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT a táblához `szolista`
--
ALTER TABLE `szolista`
  MODIFY `szolista_fk` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT a táblához `szotar`
--
ALTER TABLE `szotar`
  MODIFY `szotar_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT a táblához `szotar_szo`
--
ALTER TABLE `szotar_szo`
  MODIFY `szotar_szo_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT a táblához `user`
--
ALTER TABLE `user`
  MODIFY `user_id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
