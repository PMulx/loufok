CREATE TABLE Joueur(
   id_joueur INT,
   nom_plume VARCHAR(250) NOT NULL,
   ad_mail_joueur VARCHAR(100) NOT NULL,
   sexe VARCHAR(50),
   ddn DATE NOT NULL,
   mot_de_passe_joueur VARCHAR(100) NOT NULL,
   PRIMARY KEY(id_joueur),
   UNIQUE(nom_plume)
);

CREATE TABLE Administrateur(
   id_administrateur INT,
   ad_mail_administrateur VARCHAR(100) NOT NULL,
   mot_de_passe_administrateur VARCHAR(100) NOT NULL,
   PRIMARY KEY(id_administrateur)
);

CREATE TABLE Cadavre(
   id_cadavre INT,
   titre_cadavre VARCHAR(250) NOT NULL,
   date_debut_cadavre DATE NOT NULL,
   date_fin_cadavre DATE NOT NULL,
   nb_contributions INT NOT NULL,
   nb_jaime INT NOT NULL,
   id_administrateur INT NOT NULL,
   PRIMARY KEY(id_cadavre),
   FOREIGN KEY(id_administrateur) REFERENCES Administrateur(id_administrateur)
);

CREATE TABLE Contribution(
   id_contribution INT,
   texte_contribution VARCHAR(280) NOT NULL,
   ordre_soumission INT NOT NULL,
   date_soumission DATE NOT NULL,
   id_administrateur INT,
   id_cadavre INT NOT NULL,
   id_joueur INT,
   PRIMARY KEY(id_contribution),
   FOREIGN KEY(id_administrateur) REFERENCES Administrateur(id_administrateur),
   FOREIGN KEY(id_cadavre) REFERENCES Cadavre(id_cadavre),
   FOREIGN KEY(id_joueur) REFERENCES Joueur(id_joueur)
);

CREATE TABLE Contribution_aléatoire(
   id_joueur INT,
   id_cadavre INT,
   num_contribution INT NOT NULL,
   PRIMARY KEY(id_joueur, id_cadavre),
   FOREIGN KEY(id_joueur) REFERENCES Joueur(id_joueur),
   FOREIGN KEY(id_cadavre) REFERENCES Cadavre(id_cadavre)
);
