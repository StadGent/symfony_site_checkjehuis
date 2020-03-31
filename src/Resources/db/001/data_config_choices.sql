INSERT INTO `config_choices` (`id`, `ordering`, `category_id`, `label`, `default`, `defaultUpToYear`, `possibleCurrent`, `possibleUpgrade`, `relatedCost_id`, `costFactor`, `co2Factor`) VALUES
(1, 1, 1, 'Niet geïsoleerd', 1, '1970', 1, 0, NULL, 1, 0),
(2, 2, 1, 'Slecht geïsoleerd: 6 cm of R=1,6', 1, '2000', 1, 0, NULL, 1, 0),
(3, 3, 1, 'Slecht geïsoleerd: 10 cm of R=2,6', 1, NULL, 1, 0, NULL, 1, 0),
(4, 4, 1, 'Matig geïsoleerd: 12 cm of R=3.2', 0, NULL, 1, 0, NULL, 1, 0),
(5, 5, 1, 'Goed geïsoleerd: 18 cm of R=4.2', 0, NULL, 1, 1, 1, 1, 0.201),
(6, 6, 1, 'Goed geïsoleerd: 24 cm of R=6.3', 0, NULL, 1, 1, 2, 1, 0.201),
(7, 7, 1, 'Perfect geïsoleerd: 30 cm of R=7.9', 0, NULL, 1, 1, 3, 1, 0.201),

(8, 1, 2, 'Niet geïsoleerd, ik heb geen spouw', 1, '1945', 1, 0, NULL, 1, 0),
(10, 2, 2, 'Niet geïsoleerd, ik heb spouwmuren', 1, '1970', 1, 0, 18, 1, 0),
(12, 3, 2, 'Geïsoleerd in de spouw', 1, NULL, 1, 1, 18, 1, 0),
(9, 4, 2, 'Geïsoleerd met buitengevelisolatie', 0, NULL, 1, 1, 5, 1, 0),

(15, 2, 3, 'Niet geïsoleerd, ik heb een kelder', 1, NULL, 1, 0, NULL, 1, 0),
(16, 1, 3, 'Niet geïsoleerd, ik heb geen kelder', 0, NULL, 1, 0, NULL, 1, 0),
(17, 3, 3, 'Geïsoleerd: isolatie aan het kelderplafond', 0, NULL, 1, 1, 6, 1, 0.201),
(18, 4, 3, 'Geïsoleerd: vloerisolatie (volle grond)', 0, NULL, 1, 1, 7, 1, 0.201),

(25, 1, 4, 'deels enkel glas', 0, NULL, 1, 0, NULL, 1, 0),
(21, 2, 4, 'overal enkel glas (5,5)', 1, '1970', 1, 0, NULL, 1, 0),
(26, 3, 4, 'deels gewoon dubbel glas', 0, NULL, 1, 0, NULL, 1, 0),
(22, 4, 4, 'overal gewoon dubbel glas (2,5)', 1, '2000', 1, 0, NULL, 1, 0),
(23, 5, 4, 'overal hoogrendementsglas (1,1)', 1, NULL, 1, 1, 8, 1, 0.201),
(24, 6, 4, 'overal super isolerend glas (0,8)', 0, NULL, 1, 1, 9, 1, 0.201),

(27, 1, 5, 'natuurlijke ventilatie', 1, NULL, 1, 0, NULL, 1, 0),
(28, 2, 5, 'minimale mechanische ventilatie (C)', 0, NULL, 1, 1, 10, 1, 0.239),
(29, 3, 5, 'vraaggestuurde mechanische ventilatie (C+)', 0, NULL, 1, 1, 11, 1, 0.239),
(30, 4, 5, 'balansventilatie met warmterecuperatie (D)', 0, NULL, 1, 1, 12, 1, 0.239),

(32, 1, 6, 'individuele gaskachels', 0, NULL, 1, 0, NULL, 1, 0),
(33, 2, 6, 'centrale verwarming met gasketel ouder dan 20 jaar', 0, NULL, 1, 0, NULL, 1, 0),
(34, 3, 6, 'centrale verwarming met HR ketel', 1, NULL, 1, 0, NULL, 1, 0),
(35, 4, 6, 'centrale verwarming met condensatieketel HR+', 0, NULL, 1, 1, 13, 1, 0.239),
(36, 5, 6, 'centrale verwarming met condensatieketel HR top', 0, NULL, 1, 1, 14, 1, 0.239),
(37, 6, 6, 'centrale verwarming met warmtepomp lucht', 0, NULL, 1, 1, 16, 1, 0.239),
(38, 7, 6, 'centrale verwarming met warmtepomp bodem', 0, NULL, 1, 1, 15, 1, 0.239),

(39, 1, 7, 'elektrische verwarming', 1, NULL, 1, 0, NULL, 1, 0),
(40, 2, 7, 'centrale verwarming met warmtepomp lucht', 0, NULL, 1, 1, 16, 1, 0.239),
(41, 3, 7, 'centrale verwarming met warmtepomp bodem', 0, NULL, 1, 1, 15, 1, 0.239)

;