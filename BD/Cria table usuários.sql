CREATE TABLE Usuario (
    Email VARCHAR(100) PRIMARY KEY, -- Email como chave primária
    PasswordHash VARCHAR(255) NOT NULL, -- Hash da senha
    Name VARCHAR(100) NOT NULL,
    Type VARCHAR(50) NOT NULL
)