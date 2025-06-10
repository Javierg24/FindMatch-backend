use findMatch;

INSERT INTO USERS (USER_ID, HANDLE, EMAIL, PASSWD, PICTURE) VALUES
('USR001', 'juanito', 'juan@example.com', 'pass123', NULL),
('USR002', 'maria22', 'maria@example.com', 'mypass456', NULL),
('USR003', 'pedro_f', 'pedro@example.com', 'pedro789', NULL),
('USR004', 'ana_g', 'ana@example.com', 'ana2023', NULL),
('USR005', 'carlos_r', 'carlos@example.com', 'cr7pass', NULL),
('USR006', 'laura_m', 'laura@example.com', 'laurapw', NULL),
('USR007', 'ref_jose', 'jose@example.com', 'ref123', NULL),
('USR008', 'ref_luis', 'luis@example.com', 'refluis', NULL),
('USR009', 'sportcenter1', 'center1@example.com', 'centerpw1', NULL),
('USR010', 'futbolarena', 'arena@example.com', 'arenapw', NULL);

INSERT INTO PLAYERS (PLAYER_ID, USER_NAME, SURNAME, DNI, BIRTHDAY, PRIMARY_POSITION, SECONDARY_POSITION, GOALS, ASSISTS) VALUES
('USR001', 'Juan', 'García', '12345678A', '1990-05-15', 'DEL', 'MED', 25, 10),
('USR002', 'María', 'López', '23456789B', '1992-08-22', 'MED', 'DEF', 5, 20),
('USR003', 'Pedro', 'Fernández', '34567890C', '1988-11-30', 'DEF', 'POR', 2, 5),
('USR004', 'Ana', 'Gómez', '45678901D', '1995-03-10', 'DEL', NULL, 30, 8),
('USR005', 'Carlos', 'Rodríguez', '56789012E', '1993-07-18', 'MED', 'DEL', 15, 25),
('USR006', 'Laura', 'Martínez', '67890123F', '1991-09-05', 'DEF', 'MED', 3, 12);

INSERT INTO REFEREES (REFEREE_ID, REFEREE_LICENSE) VALUES
('USR007', 'REF-12345-2023'),
('USR008', 'REF-67890-2023');

INSERT INTO SPORT_CENTRES (SPORT_CENTRE_ID, SPORT_CENTRE_NAME, CIF, LOCATION) VALUES
('USR009', 'City Sports Center', 'A12345678', 'Calle Mayor 123, Madrid'),
('USR010', 'Fútbol Arena', 'B87654321', 'Avenida del Sport 45, Barcelona');

INSERT INTO PITCHES (SPORT_CENTRES_ID, CATEGORY) VALUES
('USR009', 5), ('USR009', 7), ('USR009', 11),
('USR010', 5), ('USR010', 7), ('USR010', 11),
('USR009', 5), ('USR010', 7), ('USR009', 11), ('USR010', 5);

INSERT INTO LEAGUES (LEAGUE_BEGINING_DATE, LEAGUE_END_DATE, LEAGUE_NAME, LOCATION, CATEGORY) VALUES
('2023-09-01', '2024-06-30', 'Liga Local 5', 'Madrid', 5),
('2023-09-15', '2024-05-31', 'Liga 7 Norte', 'Barcelona', 7),
('2023-10-01', '2024-07-15', 'Liga Nacional 11', 'Valencia', 11),
('2023-08-20', '2024-04-30', 'Liga Amateur 5', 'Sevilla', 5),
('2023-09-10', '2024-06-20', 'Liga 7 Sur', 'Málaga', 7),
('2023-08-15', '2024-05-15', 'Liga Elite 11', 'Bilbao', 11),
('2023-09-05', '2024-06-10', 'Liga 5 Este', 'Zaragoza', 5),
('2023-10-10', '2024-07-31', 'Liga 7 Oeste', 'Murcia', 7),
('2023-08-25', '2024-05-20', 'Liga Máster 11', 'Palma', 11),
('2023-09-20', '2024-06-15', 'Liga 5 Costa', 'Alicante', 5);

INSERT INTO TOURNAMENTS (TOURNAMENT_BEGINING_DATE, TOURNAMENT_END_DATE, TOURNAMENT_NAME, LOCATION, CATEGORY) VALUES
('2023-12-01', '2023-12-03', 'Torneo Navideño 5', 'Madrid', 5),
('2023-11-15', '2023-11-19', 'Copa Otoño 7', 'Barcelona', 7),
('2024-01-10', '2024-01-15', 'Torneo Reyes 11', 'Valencia', 11),
('2023-10-20', '2023-10-22', 'Copa Halloween 5', 'Sevilla', 5),
('2024-02-14', '2024-02-18', 'Torneo San Valentín 7', 'Málaga', 7),
('2024-03-15', '2024-03-20', 'Copa Primavera 11', 'Bilbao', 11),
('2023-09-30', '2023-10-01', 'Torneo Inicio 5', 'Zaragoza', 5),
('2024-04-20', '2024-04-22', 'Copa Abril 7', 'Murcia', 7),
('2024-05-01', '2024-05-05', 'Torneo del Trabajo 11', 'Palma', 11),
('2024-06-10', '2024-06-12', 'Copa Verano 5', 'Alicante', 5);

INSERT INTO TEAMS (TEAM_NAME, TEAM_ADDRESS, TEAM_TYPE, CAPTAIN, LEAGUE_ID, TOURNAMENT_ID, PICTURE) VALUES
('Los Tigres', 'Calle Tigre 1, Madrid', 5, 'USR001', 1, 1, NULL),
('Águilas FC', 'Avenida Águila 2, Barcelona', 7, 'USR002', 2, 2, NULL),
('Dragones Unidos', 'Calle Dragón 3, Valencia', 11, 'USR003', 3, 3, NULL),
('Leones Rojos', 'Plaza León 4, Sevilla', 5, 'USR004', 4, 4, NULL),
('Tiburones Azules', 'Avenida Tiburón 5, Málaga', 7, 'USR005', 5, 5, NULL),
('Halcones Negros', 'Calle Halcón 6, Bilbao', 11, 'USR006', 6, 6, NULL),
('Panteras FC', 'Paseo Pantera 7, Zaragoza', 5, 'USR001', 7, 7, NULL),
('Cóndores Unidos', 'Avenida Cóndor 8, Murcia', 7, 'USR002', 8, 8, NULL),
('Lobos Grises', 'Calle Lobo 9, Palma', 11, 'USR003', 9, 9, NULL),
('Osos Polares', 'Plaza Oso 10, Alicante', 5, 'USR004', 10, 10, NULL);

INSERT INTO TEAM_MEMBERS (USER_ID, TEAM_ID) VALUES
('USR001', 1), ('USR002', 1), ('USR003', 1),
('USR004', 2), ('USR005', 2), ('USR006', 2),
('USR001', 3), ('USR002', 3), ('USR003', 3),
('USR004', 4), ('USR005', 4), ('USR006', 4),
('USR001', 5), ('USR002', 5), ('USR003', 5),
('USR004', 6), ('USR005', 6), ('USR006', 6),
('USR001', 7), ('USR002', 7), ('USR003', 7),
('USR004', 8), ('USR005', 8), ('USR006', 8),
('USR001', 9), ('USR002', 9), ('USR003', 9),
('USR004', 10), ('USR005', 10), ('USR006', 10);

INSERT INTO MATCHES (MATCH_DATE_TIME, PITCH_ID, HOME_TEAM, AWAY_TEAM, REFEREE_ID, HOME_GOALS, AWAY_GOALS, PRICE) VALUES
('2023-11-15 18:00:00', 1, 1, 4, 'USR007', 3, 2, 50.00),
('2023-11-16 19:30:00', 2, 2, 5, 'USR008', 1, 1, 75.00),
('2023-11-17 20:00:00', 3, 3, 6, 'USR007', 0, 2, 100.00),
('2023-11-18 17:00:00', 4, 4, 7, 'USR008', 4, 1, 50.00),
('2023-11-19 18:30:00', 5, 5, 8, 'USR007', 2, 3, 75.00),
('2023-11-20 19:00:00', 6, 6, 9, 'USR008', 1, 0, 100.00),
('2023-11-21 20:30:00', 7, 7, 10, 'USR007', 3, 3, 50.00),
('2023-11-22 18:00:00', 8, 8, 1, 'USR008', 0, 1, 75.00),
('2023-11-23 19:30:00', 9, 9, 2, 'USR007', 2, 2, 100.00),
('2023-11-24 20:00:00', 10, 10, 3, 'USR008', 1, 4, 50.00);

INSERT INTO PLAYER_STATS (MATCH_ID, TEAM_ID, USER_ID, GOALS, ASSISTS, RED_CARDS, YELLOW_CARDS) VALUES
(1, 1, 'USR001', 2, 1, 0, 0),
(1, 1, 'USR002', 1, 0, 0, 1),
(1, 4, 'USR004', 1, 1, 0, 0),
(2, 2, 'USR005', 0, 1, 0, 0),
(2, 5, 'USR001', 1, 0, 0, 0),
(3, 6, 'USR006', 1, 1, 0, 0),
(4, 4, 'USR004', 2, 0, 0, 0),
(5, 5, 'USR005', 1, 1, 0, 1),
(6, 6, 'USR006', 1, 0, 0, 0),
(7, 7, 'USR001', 2, 1, 0, 0);

-- LEAGUE_SPORT_CENTRES
INSERT INTO LEAGUE_SPORT_CENTRES (LEAGUE_ID, SPORT_CENTRE_ID) VALUES
(1, 'USR009'), (2, 'USR010'), (3, 'USR009'), (4, 'USR010'), (5, 'USR009'),
(6, 'USR010'), (7, 'USR009'), (8, 'USR010'), (9, 'USR009'), (10, 'USR010');

-- LEAGUE_TEAMS
INSERT INTO LEAGUE_TEAMS (LEAGUE_ID, TEAM_ID) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5),
(6, 6), (7, 7), (8, 8), (9, 9), (10, 10);

-- LEAGUE_MATCHES
INSERT INTO LEAGUE_MATCHES (LEAGUE_ID, MATCH_ID) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5),
(6, 6), (7, 7), (8, 8), (9, 9), (10, 10);

-- TOURNAMENT_SPORT_CENTRES
INSERT INTO TOURNAMENT_SPORT_CENTRES (TOURNAMENT_ID, SPORT_CENTRE_ID) VALUES
(1, 'USR009'), (2, 'USR010'), (3, 'USR009'), (4, 'USR010'), (5, 'USR009'),
(6, 'USR010'), (7, 'USR009'), (8, 'USR010'), (9, 'USR009'), (10, 'USR010');

-- TOURNAMENT_TEAMS
INSERT INTO TOURNAMENT_TEAMS (TOURNAMENT_ID, TEAM_ID) VALUES
(1, 1), (2, 2), (3, 3), (4, 4), (5, 5),
(6, 6), (7, 7), (8, 8), (9, 9), (10, 10);

-- TOURNAMENT_MATCHES
INSERT INTO TOURNAMENT_MATCHES (TOURNAMENT_ID, MATCH_ID, PHASE) VALUES
(1, 1, 'GROUP'), (2, 2, 'GROUP'), (3, 3, 'QUARTER'), (4, 4, 'GROUP'),
(5, 5, 'SEMIFINAL'), (6, 6, 'FINAL'), (7, 7, 'GROUP'), (8, 8, 'ROUND OF 16'),
(9, 9, 'SEMIFINAL'), (10, 10, 'FINAL');
