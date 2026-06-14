CREATE TABLE utilisateurs (
    id SERIAL PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    mot_de_passe VARCHAR(255) NOT NULL,
    role VARCHAR(20) CHECK (role IN ('promoteur', 'enseignant', 'etudiant')) NOT NULL
);

CREATE TABLE modules (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    description TEXT,
    cree_par INT REFERENCES utilisateurs(id) ON DELETE SET NULL
);

CREATE TABLE cours (
    id SERIAL PRIMARY KEY,
    titre VARCHAR(150) NOT NULL,
    module_id INT REFERENCES modules(id) ON DELETE CASCADE,
    enseignant_id INT REFERENCES utilisateurs(id) ON DELETE CASCADE
);

CREATE TABLE lecons (
    id SERIAL PRIMARY KEY,
    cours_id INT REFERENCES cours(id) ON DELETE CASCADE,
    titre VARCHAR(150) NOT NULL,
    type_contenu VARCHAR(10) CHECK (type_contenu IN ('pdf', 'video')) NOT NULL,
    fichier_url VARCHAR(255) NOT NULL,
    ordre INT NOT NULL
);

CREATE TABLE evaluations (
    id SERIAL PRIMARY KEY,
    lecon_id INT UNIQUE REFERENCES lecons(id) ON DELETE CASCADE,
    titre VARCHAR(150) NOT NULL
);

CREATE TABLE questions (
    id SERIAL PRIMARY KEY,
    evaluation_id INT REFERENCES evaluations(id) ON DELETE CASCADE,
    question_texte TEXT NOT NULL,
    option_a VARCHAR(255) NOT NULL,
    option_b VARCHAR(255) NOT NULL,
    option_c VARCHAR(255) NOT NULL,
    option_d VARCHAR(255) NOT NULL,
    reponse_correcte CHAR(1) CHECK (reponse_correcte IN ('A', 'B', 'C', 'D')) NOT NULL
);

CREATE TABLE progressions (
    id SERIAL PRIMARY KEY,
    etudiant_id INT REFERENCES utilisateurs(id) ON DELETE CASCADE,
    lecon_id INT REFERENCES lecons(id) ON DELETE CASCADE,
    note_obtenue INT NOT NULL,
    valide INT DEFAULT 0,
    CONSTRAINT unique_etudiant_lecon UNIQUE (etudiant_id, lecon_id)
);

CREATE TABLE certificats (
    id SERIAL PRIMARY KEY,
    etudiant_id INT REFERENCES utilisateurs(id) ON DELETE CASCADE,
    module_id INT REFERENCES modules(id) ON DELETE CASCADE,
    date_attribution TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT unique_etudiant_module UNIQUE (etudiant_id, module_id)
);
