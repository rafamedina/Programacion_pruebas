-- Active: 1739545017843@@127.0.0.1@3306@librodecocina
DROP database LibrodeCocina;
Create database LibrodeCocina;

Use LibrodeCocina;

Create Table LibroRecetas(
    id_receta int primary key AUTO_INCREMENT,
    nombre VARCHAR(255),
    descripcion text,
    preparacion text,
    categoria VARCHAR(255),
    ingredientes text,
    imagen VARCHAR(255)
    );



desc librorecetas;