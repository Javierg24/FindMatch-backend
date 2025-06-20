-- Script modificado con AUTO_INCREMENT en los campos que lo necesitan

CREATE DATABASE findMatch;
USE findMatch;

CREATE TABLE USERS (
    USER_ID VARCHAR(6) PRIMARY KEY,
    HANDLE VARCHAR(20) NOT NULL UNIQUE,
    EMAIL VARCHAR(60) NOT NULL,
    PASSWD VARCHAR(30) NOT NULL,
    PICTURE BLOB
);

CREATE TABLE PLAYERS (
    PLAYER_ID VARCHAR(6) PRIMARY KEY,
    USER_NAME VARCHAR(20) NOT NULL,
    SURNAME VARCHAR(40),
    DNI VARCHAR(9),
    BIRTHDAY DATE, 
    PRIMARY_POSITION VARCHAR(3),
    SECONDARY_POSITION VARCHAR(3),
    GOALS DECIMAL(3,0),
    ASSISTS DECIMAL(3,0),
    FOREIGN KEY (PLAYER_ID) REFERENCES USERS(USER_ID)
);

CREATE TABLE REFEREES (
    REFEREE_ID VARCHAR(6) PRIMARY KEY,
    REFEREE_LICENSE VARCHAR(50) NOT NULL,
    FOREIGN KEY (REFEREE_ID) REFERENCES USERS(USER_ID)
);

CREATE TABLE SPORT_CENTRES (
    SPORT_CENTRE_ID VARCHAR(6) PRIMARY KEY,
    SPORT_CENTRE_NAME VARCHAR(50) NOT NULL,
    CIF VARCHAR(9) NOT NULL,
    LOCATION VARCHAR(50) NOT NULL,
    FOREIGN KEY (SPORT_CENTRE_ID) REFERENCES USERS(USER_ID)
);

CREATE TABLE PITCHES (
    PITCH_ID INT PRIMARY KEY AUTO_INCREMENT,
    SPORT_CENTRES_ID VARCHAR(6),
    CATEGORY INT NOT NULL,
    CHECK (CATEGORY IN(5, 7, 11)),
    FOREIGN KEY(SPORT_CENTRES_ID) REFERENCES SPORT_CENTRES(SPORT_CENTRE_ID)
);

CREATE TABLE LEAGUES (
    LEAGUE_ID INT PRIMARY KEY AUTO_INCREMENT,
    LEAGUE_BEGINING_DATE DATE NOT NULL,
    LEAGUE_END_DATE DATE NOT NULL,
    LEAGUE_NAME VARCHAR(20) NOT NULL,
    LOCATION VARCHAR(50) NOT NULL DEFAULT 'UNKNOWN',
    CATEGORY INT NOT NULL DEFAULT 5,
    CHECK (CATEGORY IN(5, 7, 11))
);

CREATE TABLE TOURNAMENTS (
    TOURNAMENT_ID INT PRIMARY KEY AUTO_INCREMENT,
    TOURNAMENT_BEGINING_DATE DATE NOT NULL,
    TOURNAMENT_END_DATE DATE NOT NULL,
    TOURNAMENT_NAME VARCHAR(100) NOT NULL,
    LOCATION VARCHAR(50) NOT NULL DEFAULT 'UNKNOWN',
    CATEGORY INT NOT NULL DEFAULT 5,
    CHECK (CATEGORY IN(5, 7, 11))
);

CREATE TABLE TEAMS (
    TEAM_ID INT PRIMARY KEY AUTO_INCREMENT,
    TEAM_NAME VARCHAR(35) NOT NULL,
    TEAM_ADDRESS VARCHAR(50),
    CREATED DATETIME DEFAULT CURRENT_TIMESTAMP,
    TEAM_TYPE INT NOT NULL,
    CAPTAIN VARCHAR(6) NOT NULL,
    LEAGUE_ID INT,
    TOURNAMENT_ID INT,
    PICTURE BLOB,
    CHECK (TEAM_TYPE IN (5, 7, 11)),
    FOREIGN KEY (CAPTAIN) REFERENCES USERS (USER_ID),
    FOREIGN KEY(LEAGUE_ID) REFERENCES LEAGUES(LEAGUE_ID),
    FOREIGN KEY(TOURNAMENT_ID) REFERENCES TOURNAMENTS(TOURNAMENT_ID)
);

CREATE TABLE TEAM_MEMBERS (
    USER_ID VARCHAR(6),
    TEAM_ID INT,
    PRIMARY KEY(TEAM_ID, USER_ID),
    FOREIGN KEY(USER_ID) REFERENCES USERS(USER_ID),
    FOREIGN KEY(TEAM_ID) REFERENCES TEAMS(TEAM_ID)
);


CREATE TABLE MATCHES (
    MATCH_ID INT PRIMARY KEY AUTO_INCREMENT,
    MATCH_DATE_TIME DATETIME,
    PITCH_ID INT NOT NULL,
    HOME_TEAM INT,
    AWAY_TEAM INT,
    REFEREE_ID VARCHAR(6),
    HOME_GOALS INT,
    AWAY_GOALS INT,
    PRICE DECIMAL(5,2) NOT NULL,
    FOREIGN KEY(PITCH_ID) REFERENCES PITCHES(PITCH_ID),
    FOREIGN KEY(REFEREE_ID) REFERENCES REFEREES(REFEREE_ID),
    FOREIGN KEY(HOME_TEAM) REFERENCES TEAMS(TEAM_ID),
    FOREIGN KEY(AWAY_TEAM) REFERENCES TEAMS(TEAM_ID)
);

CREATE TABLE LEAGUE_SPORT_CENTRES (
    LEAGUE_ID INT,
    SPORT_CENTRE_ID VARCHAR(6),
    PRIMARY KEY (LEAGUE_ID, SPORT_CENTRE_ID),
    FOREIGN KEY (LEAGUE_ID) REFERENCES LEAGUES(LEAGUE_ID),
    FOREIGN KEY (SPORT_CENTRE_ID) REFERENCES SPORT_CENTRES(SPORT_CENTRE_ID)
);

CREATE TABLE LEAGUE_TEAMS (
    LEAGUE_ID INT,
    TEAM_ID INT,
    PRIMARY KEY (LEAGUE_ID, TEAM_ID),
    FOREIGN KEY (LEAGUE_ID) REFERENCES LEAGUES(LEAGUE_ID),
    FOREIGN KEY (TEAM_ID) REFERENCES TEAMS(TEAM_ID)
);

CREATE TABLE PLAYER_STATS (
    MATCH_ID INT,
    TEAM_ID INT,
    USER_ID VARCHAR(6),
    GOALS INT,
    ASSISTS INT,
    RED_CARDS INT,
    YELLOW_CARDS INT,
    PRIMARY KEY (MATCH_ID, TEAM_ID, USER_ID),
    FOREIGN KEY (MATCH_ID) REFERENCES MATCHES(MATCH_ID),
    FOREIGN KEY (TEAM_ID) REFERENCES TEAMS(TEAM_ID),
    FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID)
);

CREATE TABLE LEAGUE_MATCHES (
    LEAGUE_ID INT,
    MATCH_ID INT,
    PRIMARY KEY (LEAGUE_ID, MATCH_ID),
    FOREIGN KEY (LEAGUE_ID) REFERENCES LEAGUES(LEAGUE_ID),
    FOREIGN KEY (MATCH_ID) REFERENCES MATCHES(MATCH_ID)
);

CREATE TABLE TOURNAMENT_SPORT_CENTRES (
    TOURNAMENT_ID INT,
    SPORT_CENTRE_ID VARCHAR(6),
    PRIMARY KEY (TOURNAMENT_ID, SPORT_CENTRE_ID),
    FOREIGN KEY (TOURNAMENT_ID) REFERENCES TOURNAMENTS(TOURNAMENT_ID),
    FOREIGN KEY (SPORT_CENTRE_ID) REFERENCES SPORT_CENTRES(SPORT_CENTRE_ID)
);

CREATE TABLE TOURNAMENT_TEAMS (
    TOURNAMENT_ID INT,
    TEAM_ID INT,
    PRIMARY KEY (TOURNAMENT_ID, TEAM_ID),
    FOREIGN KEY (TOURNAMENT_ID) REFERENCES TOURNAMENTS(TOURNAMENT_ID),
    FOREIGN KEY (TEAM_ID) REFERENCES TEAMS(TEAM_ID)
);

CREATE TABLE TOURNAMENT_MATCHES (
    TOURNAMENT_ID INT,
    MATCH_ID INT,
    PHASE VARCHAR(20),
    CHECK (
        PHASE IN ('GROUP', 'ROUND OF 16', 'QUARTER', 'SEMIFINAL', 'FINAL')
    ),
    PRIMARY KEY (TOURNAMENT_ID, MATCH_ID),
    FOREIGN KEY (TOURNAMENT_ID) REFERENCES TOURNAMENTS(TOURNAMENT_ID),
    FOREIGN KEY (MATCH_ID) REFERENCES MATCHES(MATCH_ID)
);

CREATE TABLE AVAILABLE_MESSAGE_IDS (
    ID INT PRIMARY KEY AUTO_INCREMENT
);

CREATE TABLE TEAM_REQUESTS (
    REQUEST_ID INT AUTO_INCREMENT PRIMARY KEY,
    TEAM_ID INT NOT NULL,
    USER_ID VARCHAR(6)  NOT NULL,
    REQUEST_STATUS ENUM('PENDING', 'APPROVED', 'REJECTED') DEFAULT 'PENDING',
    REQUEST_DATE TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (TEAM_ID) REFERENCES TEAMS(TEAM_ID),
    FOREIGN KEY (USER_ID) REFERENCES USERS(USER_ID)
);
