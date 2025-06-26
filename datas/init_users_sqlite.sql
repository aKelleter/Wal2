-- Script de création de la base pour Git.Docs (SQLite)
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username TEXT NOT NULL UNIQUE,
    password TEXT NOT NULL
);

-- Utilisateur par défaut (mot de passe : admin)
INSERT INTO users (username, password) VALUES (
    'admin',
    '$2y$10$tqqH8lIlvkyDyylDUrNsDOznnCufrNAXlcACg6R9COoNS3Bm198XS' 
);
