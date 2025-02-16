CREATE DATABASE IF NOT EXISTS estoque_db;
USE estoque_db;
 
-- Criando tabelas caso não existam
CREATE TABLE IF NOT EXISTS unidades_medida (
    ID INT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);
 
CREATE TABLE IF NOT EXISTS categoria (
    ID INT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    id_pai INT
);
 
CREATE TABLE IF NOT EXISTS fornecedor (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    empresa VARCHAR(255) NOT NULL,
    CNPJ BIGINT NOT NULL UNIQUE,
    razao_social VARCHAR(255) NOT NULL,
    nome_vendedor VARCHAR(255),
    telefone VARCHAR(20),
    email VARCHAR(255)
);
 
CREATE TABLE IF NOT EXISTS usuario (
    email VARCHAR(255) PRIMARY KEY,
    senha VARCHAR(255) NOT NULL,
    nome VARCHAR(255) NOT NULL
);
 
CREATE TABLE IF NOT EXISTS restaurante (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cnpj BIGINT NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    razao_social VARCHAR(255) NOT NULL
);
 
CREATE TABLE IF NOT EXISTS item (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(255) NOT NULL,
    preco_unitario FLOAT NOT NULL,
    unidade_medida INT,
    id_fornecedor INT,
    email_cadastro VARCHAR(255)
);
 
CREATE TABLE IF NOT EXISTS estoque (
    ID INT PRIMARY KEY AUTO_INCREMENT,
    id_item INT NOT NULL,
    data_hora_entrada DATETIME NOT NULL,
    data_hora_saida DATETIME,
    quantidade FLOAT NOT NULL,
    id_usuario VARCHAR(255)
);
 
CREATE TABLE IF NOT EXISTS validade (
    ID_item INT,
    validade DATE NOT NULL,
    quantidade FLOAT NOT NULL,
    PRIMARY KEY (ID_item, validade)
);
 
CREATE TABLE IF NOT EXISTS usuarios_restaurantes (
    email VARCHAR(255),
    id_restaurante INT,
    perfis VARCHAR(255),
    PRIMARY KEY (email, id_restaurante, perfis)
);
 
CREATE TABLE IF NOT EXISTS perfis (
    perfis VARCHAR(255) PRIMARY KEY
);
 
-- Adicionando ou atualizando chaves estrangeiras
ALTER TABLE categoria
ADD CONSTRAINT fk_categoria_pai FOREIGN KEY (id_pai) REFERENCES categoria(ID) ON DELETE SET NULL;
 
ALTER TABLE item
ADD CONSTRAINT fk_item_unidade FOREIGN KEY (unidade_medida) REFERENCES unidades_medida(ID) ON DELETE SET NULL,
ADD CONSTRAINT fk_item_fornecedor FOREIGN KEY (id_fornecedor) REFERENCES fornecedor(ID) ON DELETE SET NULL,
ADD CONSTRAINT fk_item_usuario FOREIGN KEY (email_cadastro) REFERENCES usuario(email) ON DELETE SET NULL;
 
ALTER TABLE estoque
ADD CONSTRAINT fk_estoque_item FOREIGN KEY (id_item) REFERENCES item(ID) ON DELETE CASCADE,
ADD CONSTRAINT fk_estoque_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(email) ON DELETE SET NULL;
 
ALTER TABLE validade
ADD CONSTRAINT fk_validade_item FOREIGN KEY (ID_item) REFERENCES item(ID) ON DELETE CASCADE;
 
ALTER TABLE usuarios_restaurantes
ADD CONSTRAINT fk_usuarios_restaurantes_email FOREIGN KEY (email) REFERENCES usuario(email) ON DELETE CASCADE,
ADD CONSTRAINT fk_usuarios_restaurantes_restaurante FOREIGN KEY (id_restaurante) REFERENCES restaurante(id) ON DELETE CASCADE;