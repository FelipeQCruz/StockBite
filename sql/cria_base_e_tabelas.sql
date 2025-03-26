CREATE DATABASE IF NOT EXISTS stockbite;

USE stockbite;

-- Criando tabelas caso não existam
CREATE TABLE IF NOT EXISTS unidades_medida (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS categoria (
    ID INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    id_pai INT
);

CREATE TABLE IF NOT EXISTS fornecedor (
    ID INT AUTO_INCREMENT PRIMARY KEY,
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
    id INT AUTO_INCREMENT PRIMARY KEY ,
    cnpj BIGINT NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    razao_social VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS item (
    ID INT AUTO_INCREMENT PRIMARY KEY ,
    nome VARCHAR(255) NOT NULL,
    preco_unitario FLOAT NOT NULL,
    quantidade_medida INT,
    id_fornecedor BIGINT,
    email_cadastro VARCHAR(255),
    id_categoria INT,
    id_subcategoria INT,
    id_medida INT
);

CREATE TABLE IF NOT EXISTS estoque (
    ID INT AUTO_INCREMENT PRIMARY KEY ,
    id_item INT NOT NULL,
    data_hora_entrada DATETIME NOT NULL,
    data_hora_saida DATETIME,
    quantidade FLOAT NOT NULL,
    id_usuario VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS validade (
    ID_item INT,
    validade DATE NOT NULL,
    quantidade FLOAT NOT NULL
);

CREATE TABLE IF NOT EXISTS usuarios_restaurantes (
    email VARCHAR(255),
    id_restaurante INT,
    perfis VARCHAR(255),
    PRIMARY KEY (email, id_restaurante, perfis)
);

CREATE TABLE IF NOT EXISTS perfis (perfis VARCHAR(255) PRIMARY KEY);

CREATE TABLE IF NOT EXISTS faturamento (
    cnpj BIGINT,
    valor float,
    cadastrado_por varchar (255),
    data_faturamento date
);

-- Adicionando ou atualizando chaves estrangeiras
ALTER TABLE
    categoria
ADD
    CONSTRAINT fk_categoria_pai FOREIGN KEY (id_pai) REFERENCES categoria(ID) ON DELETE
SET
    NULL;

ALTER TABLE
    faturamento
ADD
    CONSTRAINT fk_restaurante_faturamento FOREIGN KEY (cnpj) REFERENCES restaurante(cnpj) ON DELETE
SET
    NULL,
ADD
    CONSTRAINT fk_usuario_faturamento FOREIGN KEY (cadastrado_por) REFERENCES usuario(email) ON DELETE
SET
    NULL;

ALTER TABLE
    item
ADD
    CONSTRAINT fk_item_fornecedor FOREIGN KEY (id_fornecedor) REFERENCES fornecedor(CNPJ) ON DELETE
SET
    NULL,
ADD
    CONSTRAINT fk_item_usuario FOREIGN KEY (id_categoria) REFERENCES categoria(ID) ON DELETE
SET
    NULL,
ADD
    CONSTRAINT fk_item_subcategoria FOREIGN KEY (id_subcategoria) REFERENCES categoria(ID) ON DELETE
SET
    NULL,
ADD
    CONSTRAINT fk_item_medida FOREIGN KEY (id_medida) REFERENCES unidades_medida(ID) ON DELETE
SET
    NULL;

ALTER TABLE
    estoque
ADD
    CONSTRAINT fk_estoque_item FOREIGN KEY (id_item) REFERENCES item(ID) ON DELETE CASCADE,
ADD
    CONSTRAINT fk_estoque_usuario FOREIGN KEY (id_usuario) REFERENCES usuario(email) ON DELETE
SET
    NULL;

ALTER TABLE
    validade
ADD
    CONSTRAINT fk_validade_item FOREIGN KEY (ID_item) REFERENCES item(ID) ON DELETE CASCADE;

ALTER TABLE
    usuarios_restaurantes
ADD
    CONSTRAINT fk_usuarios_restaurantes_email FOREIGN KEY (email) REFERENCES usuario(email) ON DELETE CASCADE,
ADD
    CONSTRAINT fk_usuarios_restaurantes_restaurante FOREIGN KEY (id_restaurante) REFERENCES restaurante(id) ON DELETE CASCADE;

USE stockbite;

-- Inserindo categorias principais
INSERT INTO
    categoria (nome, id, id_pai)
VALUES
    ('Insumos', 1, NULL),
    ('Embalagens', 2, NULL),
    ('Pao', 3, null),
    ('Proteinas', 4, null),
    ('Laticinio', 5, null),
    ('Hortifruti', 6, null),
    ('Bebidas', 7, null),
    ('Limpeza', 8, null),
    ('Molhos', 9, null),
    ('Descartaveis', 10, null);

-- Inserindo subcategorias para cada categoria principal
INSERT INTO
    categoria (nome, id_pai)
VALUES
    -- Subcategorias de Insumos
    ('Tempero', 1),
    ('Farinha de trigo', 1),
    ('Extrato de tomate', 1),
    ('Oleo de soja', 1),
    ('Oleo de algodão', 1),
    ('Leite integral', 1),
    ('Farinha Panko', 1),
    ('Cerveja preta', 1),
    ('Ketchup Galao', 1),
    ('Barbecue Galao', 1),
    ('Mostarda amarela', 1),
    
    -- Subcategorias de Embalagens
    ('Sacola Craft', 2),
    ('Caixa de hamburguer/porcao', 2),
    ('Papel Manteiga', 2),
    
    -- Subcategorias de Pao
    ('Pao Brioche', 3),
    ('Pao Tigre', 3),
    ('Pao Australiano', 3),
    ('Pao kids', 3),
    ('Pao hot dog', 3),
    ('Pao mini', 3),
    ('Pao pandora', 3),
    
    -- Subcategoria de proteinas
    ('Carne', 4),
    ('File de Frango', 4),
    ('Linguiça toscana', 4),
    ('Contra filet', 4),
    ('Hamburguer veggie', 4),
    ('Costela', 4),
    ('Frango porcao', 4),
    ('Bacon manta', 4),
    ('Bacon fatiado', 4),
    
    -- Subcategoria de laticinio
    ('Queijo prato', 5),
    ('Molho de cheddar', 5),
    ('Bisnaga de cheddar', 5),
    ('Bisnaga de requeijao', 5),
    ('Queijo mussarela', 5),
    ('Gorgonzola', 5),
    
    -- Subcategoria de Hortifruti
	('Hortifruti',6),
    
    -- Subcategoria Bebidas
	('Refrigerante', 7),
    ('Cerveja', 7),
    ('Destilado',7),
    ('Não alcolico', 7),
    
    -- Subcategoria Limpeza
    ('Saco de lixo', 8),
    ('Perfex', 8),
    ('Detergente', 8),
    ('Papel Toalha', 8),
    ('Bobina de saco picotado', 8),
    ('Luva de vinil', 8),
    ('Agua sanitaria', 8),
    ('Desinfetante', 8),
    ('Azulim', 8),
    ('Limpa vidro', 8),
    ('Fibraço', 8),
    ('Flotex', 8),
    ('G-food', 8),
    ('Peroxi food', 8),
    ('Bucha de louça', 8),
    ('Sanitizante', 8),
    -- Subcategoria molhos
    ('Maionese verde', 9),
    ('Maionese de alho', 9),
    ('Maionese de bacon', 9),
    ('Maionese de pimenta', 9),
    ('Molho de queijo', 9),
    ('Molho de gorgonzola', 9),
    ('Molho de ervas', 9),
    -- Subcategoria descartaveis
    ('Copo de 300 ml', 10),
    ('Copo de 400 ml', 10),
    ('Copo de 500 ml', 10),
    ('Marmita de 250 ml', 10),
    ('Marmita de 500 ml', 10),
    ('Marmita de 750 ml', 10),
    ('Bobina termica', 10),
    ('Separador de hamburguer', 10),
    ('Pá de maionese', 10),
    ('Palito de hamburguer', 10),
    ('Palito de porcao', 10);

INSERT INTO
    perfis (perfis)
VALUES
    ('Administrador'),
    ('Editor'),
    ('Visualizador');

INSERT INTO
    unidades_medida (nome)
VALUES
    ('Grama'),
    ('Quilograma'),
    ('Litro'),
    ('Unidade');

INSERT INTO
    restaurante (cnpj, nome, razao_social)
VALUES
    (26064269000125, 'Atlantis Burguer', 'Atlantis Burguer Hamburgueria LTDA');