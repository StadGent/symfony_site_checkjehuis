INSERT INTO `subsidies` (`id`, `slug`, `subsidyCategory_id`, `label`, `value`, `multiplier`, `max`) VALUES

(1, 'roof_18', 1, 'dakisolatie > 18cm', '2', 'surface', '1000'),
(2, 'roof_24', 1, 'dakisolatie > 24cm', '5', 'surface', '1000'),
(3, 'roof_30', 1, 'dakisolatie > 30cm', '5', 'surface', '1000'),
(4, 'roof_wind', 1, 'onderdak - aannemer', '8', 'surface', '1000'),

(5, 'facade', 1, 'buitenisolatie gevel', '5', 'surface', '500'),
(6, 'facade_cavity', 1, 'spouwisolatie', '0', 'surface', '500'),
(7, 'floor', 1, 'vloerisolatie', '2', 'surface', '200'),
(8, 'basement', 1, 'kelderisolatie (plafond)', '2', 'surface', '200'),

(9, 'window_1_1', 1, 'schrijnwerk (1.1)', '30', 'surface', '500'),
(10, 'window_0_8', 1, 'schrijnwerk (0.8)', '35', 'surface', '500'),

(11, '', 1, 'ventilatie C', '0', 'none', NULL),
(12, '', 1, 'ventilatie C+', '0', 'none', NULL),
(13, '', 1, 'ventilatie D', '0', 'none', NULL),

(14, '', 1, 'condensatieketel HR+', '0', 'none', NULL),
(15, '', 1, 'condensatieketel HR top', '0', 'none', NULL),

(16, '', 1, 'warmtepomp bodem', '250', 'none', NULL),
(17, '', 1, 'warmtepomp lucht', '250', 'none', NULL),
(18, 'solar_heater', 1, 'warmtepompboiler (of zonneboiler)', '250', 'none', NULL),

(19, 'roof_18', 2, 'dakisolatie > 18cm', '0', 'cost', NULL),
(20, 'roof_24', 2, 'dakisolatie > 24cm', '0', 'cost', NULL),
(21, 'roof_30', 2, 'dakisolatie > 30cm', '0', 'cost', NULL),
(22, 'roof_wind', 2, 'onderdak - aannemer', '30', 'cost', NULL),

(23, '', 2, 'buitenisolatie gevel', '0', 'cost', NULL),
(24, '', 2, 'spouwisolatie', '0', 'cost', NULL),
(25, '', 2, 'vloerisolatie', '0', 'cost', NULL),
(26, '', 2, 'kelderisolatie (plafond)', '0', 'cost', NULL),

(27, '', 2, 'schrijnwerk (1.1)', '30', 'cost', NULL),
(28, '', 2, 'schrijnwerk (0.8)', '30', 'cost', NULL),

(29, '', 2, 'ventilatie C', '0', 'cost', NULL),
(30, '', 2, 'ventilatie C+', '0', 'cost', NULL),
(31, '', 2, 'ventilatie D', '0', 'cost', NULL),

(32, '', 2, 'condensatieketel HR+', '30', 'cost', NULL),
(33, '', 2, 'condensatieketel HR top', '30', 'cost', NULL),

(34, '', 2, 'warmtepomp bodem', '0', 'cost', NULL),
(35, '', 2, 'warmtepomp lucht', '0', 'cost', NULL),
(36, 'solar_heater', 2, 'warmtepompboiler (of zonneboiler)', '0', 'cost', NULL),

(37, 'roof_18', 3, 'dakisolatie > 18cm', '8', 'surface', NULL),
(38, 'roof_24', 3, 'dakisolatie > 24cm', '8', 'surface', NULL),
(39, 'roof_30', 3, 'dakisolatie > 30cm', '8', 'surface', NULL),
(40, 'roof_wind', 3, 'onderdak - aannemer', '0', 'surface', NULL),

(41, 'facade', 3, 'buitenisolatie gevel', '15', 'surface', NULL),
(42, 'facade_cavity', 3, 'spouwisolatie', '6', 'surface', NULL),
(43, 'floor', 3, 'vloerisolatie', '6', 'surface', NULL),
(44, 'basement', 3, 'kelderisolatie (plafond)', '0', 'surface', NULL),

(45, '', 3, 'schrijnwerk (1.1)', '12', 'surface', NULL),
(46, '', 3, 'schrijnwerk (0.8)', '15', 'surface', NULL),

(47, '', 3, 'ventilatie C', '0', 'none', NULL),
(48, '', 3, 'ventilatie C+', '0', 'none', NULL),
(49, '', 3, 'ventilatie D', '0', 'none', NULL),

(50, '', 3, 'condensatieketel HR+', '0', 'none', NULL),
(51, '', 3, 'condensatieketel HR top', '0', 'none', NULL),

(52, '', 3, 'warmtepomp bodem', '1700', 'none', NULL),
(53, '', 3, 'warmtepomp lucht)', '1700', 'none', NULL),
(54, 'solar_heater', 3, 'warmtepompboiler (of zonneboiler)', '0', 'none', NULL),

(55, 'roof_18', 4, 'dakisolatie > 18cm', '0', 'none', NULL),
(56, 'roof_24', 4, 'dakisolatie > 24cm', '0', 'none', NULL),
(57, 'roof_30', 4, 'dakisolatie > 30cm', '0', 'none', NULL),
(58, 'roof_wind', 4, 'onderdak - aannemer', '0', 'none', NULL),

(59, 'facade', 4, 'buitenisolatie gevel', '0', 'none', NULL),
(60, 'facade_cavity', 4, 'spouwisolatie', '0', 'none', NULL),
(61, 'floor', 4, 'vloerisolatie', '0', 'none', NULL),
(62, 'basement', 4, 'kelderisolatie (plafond)', '0', 'none', NULL),

(63, '', 4, 'schrijnwerk (1.1)', '0', 'none', NULL),
(64, '', 4, 'schrijnwerk (0.8)', '0', 'none', NULL),

(65, '', 4, 'ventilatie C', '0', 'none', NULL),
(66, '', 4, 'ventilatie C+', '0', 'none', NULL),
(67, '', 4, 'ventilatie D', '0', 'none', NULL),

(68, '', 4, 'condensatieketel HR+', '0', 'none', NULL),
(69, '', 4, 'condensatieketel HR top', '0', 'none', NULL),

(70, '', 4, 'warmtepomp bodem', '0', 'none', NULL),
(71, '', 4, 'warmtepomp lucht', '0', 'none', NULL),
(72, 'solar_heater', 4, 'warmtepompboiler (of zonneboiler)', '0', 'none', NULL),

(73, 'roof_18', 5, 'dakisolatie > 18cm', '0', 'none', NULL),
(74, 'roof_24', 5, 'dakisolatie > 24cm', '0', 'none', NULL),
(75, 'roof_30', 5, 'dakisolatie > 30cm', '0', 'none', NULL),
(76, 'roof_wind', 5, 'onderdak - aannemer', '0', 'none', NULL),

(77, 'facade', 5, 'buitenisolatie gevel', '0', 'none', NULL),
(78, 'facade_cavity', 5, 'spouwisolatie', '0', 'none', NULL),
(79, 'floor', 5, 'vloerisolatie', '0', 'none', NULL),
(80, 'basement', 5, 'kelderisolatie (plafond)', '0', 'none', NULL),

(81, '', 5, 'schrijnwerk (1.1)', '0', 'none', NULL),
(82, '', 5, 'schrijnwerk (0.8)', '0', 'none', NULL),

(83, '', 5, 'ventilatie C', '0', 'none', NULL),
(84, '', 5, 'ventilatie C+', '0', 'none', NULL),
(85, '', 5, 'ventilatie D', '0', 'none', NULL),

(86, '', 5, 'condensatieketel HR+', '0', 'none', NULL),
(87, '', 5, 'condensatieketel HR top', '0', 'none', NULL),

(88, '', 5, 'warmtepomp bodem', '0', 'none', NULL),
(89, '', 5, 'warmtepomp lucht', '0', 'none', NULL),
(90, 'solar_heater', 5, 'warmtepompboiler (of zonneboiler)', '0', 'none', NULL)

;