DROP DATABASE IF EXISTS bdebetzone;
CREATE DATABASE IF NOT EXISTS bdebetzone;
USE bdebetzone;

-- TABLE : utilisateurs (joueurs et admins)
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    login VARCHAR(100) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    role ENUM('user', 'admin') DEFAULT 'user',
    points INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE regions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);

CREATE TABLE teams (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE,
    jeu VARCHAR(50) NOT NULL,
    region_id INT NULL,
    FOREIGN KEY (region_id) REFERENCES regions(id) ON DELETE SET NULL
);

-- TABLE : matchs esport
CREATE TABLE matchs (
    id INT AUTO_INCREMENT PRIMARY KEY,
    jeu VARCHAR(50) NOT NULL,
    equipe1 VARCHAR(100) NOT NULL,
    equipe2 VARCHAR(100) NOT NULL,
    date_match DATETIME NOT NULL,
    gagnant ENUM('equipe1', 'equipe2', 'en cours') DEFAULT 'en cours'
);

-- TABLE : paris des utilisateurs
CREATE TABLE paris (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    match_id INT,
    choix ENUM('equipe1', 'equipe2') NOT NULL,
    mise INT NOT NULL,
    resultat ENUM('en attente', 'gagné', 'perdu') DEFAULT 'en attente',
    UNIQUE KEY unique_bet_per_user_match (user_id, match_id),
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (match_id) REFERENCES matchs(id) ON DELETE CASCADE
);

-- TABLE : classement (vue générée ou table calculée)
CREATE VIEW classement AS
SELECT 
    id AS user_id,
    username,
    points
FROM users
WHERE role = 'user'
ORDER BY points DESC;


INSERT INTO users (username, login, mdp, role)
VALUES ('admin1',
 'admin1', 
'1234',
 'admin');

INSERT INTO users (username, login, mdp, role)
VALUES ('joueur1',
 'joueur1',
'1234',
 'user');

INSERT INTO regions (nom)
VALUES ('EMEA'), ('NA'), ('APAC');

INSERT INTO teams (nom, jeu, region_id)
VALUES
('G2', 'League of Legends', 1),
('Fnatic', 'League of Legends', 1),
('Sentinels', 'Valorant', 2),
('Karmine Corp', 'Valorant', 1);
 
 -- Matchs en cours
 INSERT INTO matchs (jeu, equipe1, equipe2, date_match, gagnant)
VALUES ('League of Legends', 'G2', 'Fnatic', NOW() - INTERVAL 15 MINUTE, 'en cours');

-- Matchs à venir
INSERT INTO matchs (jeu, equipe1, equipe2, date_match, gagnant)
VALUES ('Valorant', 'Karmine Corp', 'Sentinels', NOW() + INTERVAL 1 DAY, 'en cours');
