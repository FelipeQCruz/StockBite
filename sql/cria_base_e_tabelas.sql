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
    ID INT AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT,
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
    id INT AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT,
    cnpj BIGINT NOT NULL UNIQUE,
    nome VARCHAR(255) NOT NULL,
    razao_social VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS item (
    ID INT AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT,
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
    ID INT AUTO_INCREMENT PRIMARY KEY AUTO_INCREMENT,
    id_item INT NOT NULL,
    data_hora_entrada DATETIME NOT NULL,
    data_hora_saida DATETIME,
    quantidade FLOAT NOT NULL,
    id_usuario VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS validade (
    ID_item INT NOT NULL,
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
    ('Temperos', 2, NULL),
    ('Embalagens', 3, NULL),
    ('Pao', 4, null),
    ('Proteinas', 5, null),
    ('Laticio', 6, null),
    ('Hortifruti', 7, null),
    ('Bebidas', 8, null),
    ('Limpeza', 9, null),
    ('Molhos', 10, null),
    ('Descartaveis', 11, null);

-- Inserindo subcategorias para cada categoria principal
INSERT INTO
    categoria (nome, id_pai)
VALUES
    -- Subcategorias de Insumos
    ('Extrato de tomate', 1),
    ('Cerveja preta', 1),
    ('Oleo de soja', 1),
    ('Creme culinario', 1),
    ('Leite integral', 1),
    ('Farinha de trigo', 1),
    ('Farinha Panko', 1),
    ('Açucar', 1),
    ('Açucar mascavo', 1),
    ('Molho ingles', 1),
    ('Molho de pimenta', 1),
    ('Molho shoyu', 1),
    ('Mostarda Amarela', 1),
    ('Mostarda escura', 1),
    ('Ketchup', 1),
    ('Barbecue', 1),
    ('Azeite', 1),
    ('Mel', 1),
    ('Vinagre', 1),
    ('Base americana', 1),
    ('Proteina de soja', 1),
    ('Arroz', 1),
    ('Feijao carioca', 1),
    ('Feijao preto', 1),
    ('Azeitona verde', 1),
    ('Alho frito', 1),
    ('Alho descascado', 1),
    ('Xarope de limao siciliano', 1),
    ('Xarope de pink lemonade', 1),
    ('Xarope de maça verde', 1),
    ('Xarope de pessego', 1),
    ('Base cha', 1),
    ('Oreo', 1),
    ('Pacoca', 1),
    ('Nesqeuick', 1),
    ('Waffle', 1),
    ('Batata congelada', 1),
    ('Nutella', 1),
    -- Subcategorias de Temperos
    ('Sal', 2),
    ('Sal parrilha', 2),
    ('Pimenta do reino preta', 2),
    ('Paprica picante', 2),
    ('Paprica doce', 2),
    ('Alho em po', 2),
    ('Creme de cebola', 2),
    ('Caldo de bacon', 2),
    ('Chimichurri', 2),
    ('Lemon Pepper', 2),
    ('Salsinha', 2),
    ('Cebolinha', 2),
    ('Cominho em po', 2),
    ('Caldo de costela', 2),
    -- Subcategorias de Embalagens
    ('Sacola Craft', 3),
    ('Caixa de hamburguer/porcao', 3),
    ('Papel Manteiga', 3),
    -- Subcategorias de Pao
    ('Pao Brioche', 4),
    ('Pao Tigre', 4),
    ('Pao Australiano', 4),
    ('Pao kids', 4),
    ('Pao hot dog', 4),
    ('Pao mini', 4),
    ('Pao pandora', 4),
    -- Subcategoria de proteinas
    ('Carne', 5),
    ('File de Frango', 5),
    ('Linguiça toscana', 5),
    ('Contra filet', 5),
    ('Hamburguer veggie', 5),
    ('Costela', 5),
    ('Frango porcao', 5),
    ('Bacon manta', 5),
    ('Bacon fatiado', 5),
    -- Subcategoria de laticinio
    ('Queijo prato', 6),
    ('Molho de cheddar', 6),
    ('Bisnaga de cheddar', 6),
    ('Bisnaga de requeijao', 6),
    ('Queijo mussarela', 6),
    ('Gorgonzola', 6),
    -- Subcategoria de Hortifruti
    ('Alface', 7),
    ('Tomate', 7),
    ('Cebola', 7),
    ('Cebola roxa', 7),
    ('Rucula', 7),
    ('Laranja', 7),
    ('Limao', 7),
    ('Maracuja', 7),
    ('Kiwi', 7),
    ('Morango', 7),
    ('Batata', 7),
    -- Subcategoria Bebidas
    ('Agua sem gas 500ml', 8),
    ('Agua com gas 500ml', 8),
    ('Coca cola ks 290ml', 8),
    ('Coca cola lata 350ml', 8),
    ('Coca cola lata zero 350ml', 8),
    ('Coca cola 2 litros', 8),
    ('Coca cola zero 2 litros', 8),
    ('Guarana lata 350ml', 8),
    ('Guarana zero lata 350ml', 8),
    ('Guarana 2 litros', 8),
    ('Fanta laranja lata 350ml', 8),
    ('Sprite lata 350ml', 8),
    ('Schewppes citrus lata 350ml', 8),
    ('Schewppes tonica lata 350ml', 8),
    ('Kapo uva', 8),
    ('Kapo laranja', 8),
    ('Kapo Morango', 8),
    ('H2oh limoneto 500ml', 8),
    ('H2oh limao 500ml', 8),
    ('Corona Long neck 330ml', 8),
    ('Corona retornavel 600ml', 8),
    ('Heineken long neck 330ml', 8),
    ('Heineken zero long neck 330ml', 8),
    ('Heineken lata 350ml', 8),
    ('Heineken retornavel 600ml', 8),
    ('Baden golden 600ml', 8),
    ('Baden peach 600ml', 8),
    ('Baden witbier 600ml', 8),
    ('Colorado ribeirao', 8),
    ('Goose ipa Long neck 330ml', 8),
    ('Goose midway long neck 330ml', 8),
    -- Subcategoria Limpeza
    ('Saco de lixo', 9),
    ('Perflex', 9),
    ('Detergente', 9),
    ('Papel Toalha', 9),
    ('Bobina de saco picotado', 9),
    ('Luva de vinil', 9),
    ('Agua sanitaria', 9),
    ('Desinfetante', 9),
    ('Azulim', 9),
    ('Limpa vidro', 9),
    ('Fibraço', 9),
    ('Flotex', 9),
    ('G-food', 9),
    ('Peroxi food', 9),
    ('Bucha de louça', 9),
    ('Sanitizante', 9),
    -- Subcategoria molhos
    ('Maionese verde', 10),
    ('Maionese de alho', 10),
    ('Maionese de bacon', 10),
    ('Maionese de pimenta', 10),
    ('Molho de queijo', 10),
    ('Molho de gorgonzola', 10),
    ('Molho de ervas', 10),
    -- Subcategoria descartaveis
    ('Copo de 300 ml', 11),
    ('Copo de 400 ml', 11),
    ('Copo de 500 ml', 11),
    ('Marmita de 250 ml', 11),
    ('Marmita de 500 ml', 11),
    ('Marmita de 750 ml', 11),
    ('Bobina termica', 11),
    ('Separador de hamburguer', 11),
    ('Pá de maionese', 11),
    ('Palito de hamburguer', 11),
    ('Palito de porcao', 11);

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
    (43447633867, 'Felipe QC', 'FQC');