CREATE DATABASE IF NOT EXISTS crm_imobiliario;
USE crm_imobiliario;

CREATE TABLE IF NOT EXISTS imoveis (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo INT NOT NULL,
    bairro VARCHAR(255) NOT NULL,
    quartos INT NOT NULL,
    preco DECIMAL(10,2) NOT NULL CHECK (preco > 0)
);

CREATE TABLE IF NOT EXISTS clientes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    telefone VARCHAR(50) NOT NULL,
    tipo_id INT NOT NULL,
    preco_min DECIMAL(10,2) DEFAULT NULL,
    preco_max DECIMAL(10,2) DEFAULT NULL,
    quartos_min INT DEFAULT NULL,
    bairros TEXT
);

CREATE TABLE IF NOT EXISTS tipos_imovel (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);
