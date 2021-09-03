CREATE DATABASE KINEXPRESS;

use KINEXPRESS;

CREATE TABLE User(
	id INT PRIMARY KEY AUTO_INCREMENT,
	nom VARCHAR(30) NOT NULL,
	prenom VARCHAR(30) NOT NULL,
	image VARCHAR(100) NOT NULL,
	numero VARCHAR(30) NOT NULL,
	email VARCHAR(100) NOT NULL,
	isconnected INT NOT NULL DEFAULT 0
);

CREATE TABLE Carrefour(
	id INT PRIMARY KEY AUTO_INCREMENT,
	nom VARCHAR(250) NOT NULL,
	commune VARCHAR(250) NOT NULL,
	reperes LONGTEXT NOT NULL,
	description LONGTEXT NOT NULL
);

CREATE TABLE Troncon(
	id INT PRIMARY KEY AUTO_INCREMENT,
	idfrom INT NOT NULL,
	idto INT NOT NULL,
	FOREIGN KEY idfrom REFERENCES Carrefour(id),
	FOREIGN KEY idto REFERENCES Carrefour(id)
);

CREATE TABLE Circulation(
	id INT PRIMARY KEY AUTO_INCREMENT,
	image VARCHAR(250) NOT NULL,
	type VARCHAR(250) NOT NULL,
	niveau INT NOT NULL,
	description LONGTEXT NOT NULL,
	idcarrefour INT NOT NULL,
	idtroncon INT NOT NULL,
	date DATETIME NOT NULL,
	FOREIGN KEY (idcarrefour) REFERENCES Carrefour(id),
	FOREIGN KEY (idtroncon) REFERENCES Troncon(id)
);

-- ntumbakalonji@gmail.com
-- Qsdfg67890@P

CREATE TABLE Abonnement(
	id INT PRIMARY KEY AUTO_INCREMENT,
	iduser INT NOT NULL,
	type VARCHAR(10) NOT NULL,
	debut DATETIME NOT NULL,
	fin DATETIME NOT NULL,
	datepaiement DATETIME NOT NULL,
	FOREIGN KEY iduser REFERENCES User(id)
);

-- Dans abonnement je fais une erreur de logique volontaire par prevision et incertitude actuel

CREATE TABLE Message(
	id INT PRIMARY KEY AUTO_INCREMENT,
	iduser INT NOT NULL,
	message LONGTEXT NOT NULL,
	date DATETIME NOT NULL,
	FOREIGN KEY iduser REFERENCES User(id)
);

