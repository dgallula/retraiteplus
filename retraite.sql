CREATE DATABASE IF NOT EXISTS maisons_retraite;

USE maisons_retraite;

CREATE TABLE GroupesQuestions (
    groupe_id INT AUTO_INCREMENT PRIMARY KEY,
    nom_groupe VARCHAR(100) NOT NULL
);

CREATE TABLE Questions (
    question_id INT AUTO_INCREMENT PRIMARY KEY,
    groupe_id INT,
    question_texte TEXT NOT NULL,
    FOREIGN KEY (groupe_id) REFERENCES GroupesQuestions(groupe_id)
);

CREATE TABLE MaisonRetraite (
    maison_retraite_id INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(255) NOT NULL,
    adresse VARCHAR(255),
    ville VARCHAR(100),
    code_postal VARCHAR(10),
    pays VARCHAR(100)
);

CREATE TABLE Notations (
    notation_id INT AUTO_INCREMENT PRIMARY KEY,
    maison_retraite_id INT,
    question_id INT,
    notation INT CHECK (notation BETWEEN 1 AND 4),
    FOREIGN KEY (maison_retraite_id) REFERENCES MaisonRetraite(maison_retraite_id),
    FOREIGN KEY (question_id) REFERENCES Questions(question_id)
);

-- Insertion de données dans "GroupesQuestions"
INSERT INTO GroupesQuestions (nom_groupe) VALUES
('IMPRESSION GÉNÉRALE'),
('PRESTATIONS HÔTELIÈRES');

-- Insertion de données dans "Questions"
INSERT INTO Questions (groupe_id, question_texte) VALUES
(1, 'Globalement, êtes-vous satisfait de cet établissement ?'),
(1, 'Conseillerez-vous cet établissement à quelqu''un ?'),
(2, 'Appréciez-vous le moment du repas ?'),
(2, 'Estimez-vous que la nourriture servie par l''établissement est :');

INSERT INTO MaisonRetraite (nom, adresse, ville, code_postal, pays) VALUES
('Résidence Les Jardins du Lac', '15 Avenue des Lilas', 'Paris', '75012', 'France'),
('Sunset Senior Living', '123 Main Street', 'Los Angeles', '90001', 'USA'),
('Casa di Riposo Bella Vita', 'Via Roma, 10', 'Rome', '00100', 'Italie');

-- Insertion de données dans "Notations" (exemple de notation pour une maison de retraite)
INSERT INTO Notations (maison_retraite_id, question_id, notation) VALUES
(1, 1, 4), -- Globalement satisfait
(2, 2, 3), -- Recommandera à quelqu'un
(1, 3, 4), -- Apprécie le moment du repas
(1, 4, 2); -- Estime que la nourriture servie est moyenne

-- Sélection de données pour vérification
SELECT * FROM GroupesQuestions;
SELECT * FROM Questions;
select * from MaisonRetraite;
SELECT * FROM Notations;


