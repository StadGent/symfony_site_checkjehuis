INSERT INTO `config_categories` (`id`, `ordering`, `slug`, `label`, `hasInverseMatrix`, `fromActual`, `percent`) VALUES
(1, 1, 'roof', 'Dakisolatie', 0, 0, 30),
(2, 2, 'facade', 'Gevelisolatie', 0, 0, 15),
(3, 3, 'floor', 'Vloerisolatie', 0, 0, 10),
(4, 4, 'window', 'Ramen', 0, 0, 15),
(5, 5, 'ventilation', 'Ventilatie', 1, 1, 100),
(6, 6, 'heating', 'Verwarming', 1, 1, 100),
(7, 6, 'heating_elec', 'Verwarming (Elektrisch)', 0, 1, 100)
;