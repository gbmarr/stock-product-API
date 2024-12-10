CREATE DATABASE IF NOT EXISTS webii_productstpe;
USE webii_productstpe;

/* Tabla categories */
CREATE TABLE IF NOT EXISTS categories (
    idcat INT AUTO_INCREMENT PRIMARY KEY,
    catname VARCHAR(50) NOT NULL,
    catimage VARCHAR(350)
);

/* Tabla products */
CREATE TABLE IF NOT EXISTS products (
    idproduct INT AUTO_INCREMENT PRIMARY KEY,
    prodname VARCHAR(50) NOT NULL,
    description VARCHAR(200) NOT NULL,
    idcategory INT,
    stock BOOLEAN NOT NULL,
    price FLOAT NOT NULL,
    imgproduct VARCHAR(350),
    FOREIGN KEY (idcategory) REFERENCES categories(idcat) ON DELETE SET NULL
);

/* Tabla users */
CREATE TABLE IF NOT EXISTS users (
    iduser INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    surname VARCHAR(30) NOT NULL,
    email VARCHAR(50) NOT NULL,
    pass VARCHAR(150) NOT NULL,
    token VARCHAR(150) NOT NULL,
    admin BOOLEAN NOT NULL
);
