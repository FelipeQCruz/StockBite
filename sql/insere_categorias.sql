USE stockbite;
 
-- Inserindo categorias principais
INSERT INTO categoria (ID, nome, id_pai) VALUES
(1, 'Bebidas', NULL),
(2, 'Comidas', NULL),
(3, 'Sobremesas', NULL);
 
-- Inserindo subcategorias para cada categoria principal
INSERT INTO categoria (ID, nome, id_pai) VALUES
-- Subcategorias de Bebidas
(4, 'Refrigerante', 1),
(5, 'Sucos', 1),
(6, 'Cervejas', 1),
 
-- Subcategorias de Comidas
(7, 'Lanches', 2),
(8, 'Pratos Feitos', 2),
(9, 'Massas', 2),
 
-- Subcategorias de Sobremesas
(10, 'Sorvetes', 3),
(11, 'Bolos', 3),
(12, 'Doces', 3);