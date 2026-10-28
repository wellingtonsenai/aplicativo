-- Importe no phpMyAdmin (aba Importar) ou: mysql -u root < api/banco.sql
SET NAMES utf8mb4;
CREATE DATABASE IF NOT EXISTS cantina CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cantina;

CREATE TABLE IF NOT EXISTS usuarios (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20)  NOT NULL UNIQUE,
    nome      VARCHAR(100) NOT NULL,
    senha     VARCHAR(255) NOT NULL, -- hash gerado por password_hash()
    perfil    ENUM('atendente', 'supervisor') NOT NULL,
    ativo     BOOLEAN NOT NULL DEFAULT TRUE
) ENGINE=InnoDB; -- MyISAM (padrão do WAMP) ignora chaves estrangeiras

-- Mesmos usuários do antigo js/usuarios.js (senhas: cantina123, lanche2026, super2026, diego2026)
INSERT IGNORE INTO usuarios (matricula, nome, senha, perfil, ativo) VALUES
('1001', 'Ana Souza',    '$2y$10$j9HqTM5NhMnmlkMU72rIl.rnxLTt.fSe4thvwQKBcA4qrSfBOcuQO', 'atendente',  TRUE),
('1002', 'Bruno Lima',   '$2y$10$GvDIy0kA8wACUKjhNsU12.vS0t1qFEefNGP6NPQFtVb1R8qn4HFPC', 'atendente',  TRUE),
('2001', 'Carla Mendes', '$2y$10$Mo.beIRz/pjehhqbgF8Kpeort93qLvVOFDmn4wD3EfDiIca45snQK', 'supervisor', TRUE),
('2002', 'Diego Rocha',  '$2y$10$6TgLEjEhPspovPlSmEcSquXIWI108VvOS9Jj4Zi.vRq12gULn7Kd2', 'atendente',  FALSE);

CREATE TABLE IF NOT EXISTS turmas (
    id   INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL UNIQUE -- ex.: Desenvolvimento de Sistemas V1 22255
) ENGINE=InnoDB;

CREATE TABLE IF NOT EXISTS alunos (
    id        INT AUTO_INCREMENT PRIMARY KEY,
    matricula VARCHAR(20)  NOT NULL UNIQUE,
    nome      VARCHAR(100) NOT NULL,
    turma_id  INT          NOT NULL,
    codigo    CHAR(32)     NOT NULL UNIQUE, -- conteúdo do QR Code, gerado com random_bytes() no PHP
    ativo     BOOLEAN NOT NULL DEFAULT TRUE,
    FOREIGN KEY (turma_id) REFERENCES turmas (id)
) ENGINE=InnoDB; -- MyISAM (padrão do WAMP) ignora chaves estrangeiras

-- Um lanche por aluno por dia: o UNIQUE (aluno_id, data) barra a segunda retirada
CREATE TABLE IF NOT EXISTS entregas (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    aluno_id    INT NOT NULL,
    usuario_id  INT NULL, -- atendente que entregou
    data        DATE NOT NULL,
    entregue_em DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    UNIQUE (aluno_id, data),
    FOREIGN KEY (aluno_id) REFERENCES alunos (id),
    FOREIGN KEY (usuario_id) REFERENCES usuarios (id) ON DELETE SET NULL
) ENGINE=InnoDB;

INSERT IGNORE INTO turmas (id, nome) VALUES
(1, 'Técnico em Desenvolvimento de Sistemas'),
(2, 'Técnico em Mecânica');

INSERT IGNORE INTO alunos (matricula, nome, turma_id, codigo) VALUES
('A001', 'João Pereira',  1, '714cd53eff57870fdfafc4ab09621a09'),
('A002', 'Maria Schmitt', 2, '7a8fae16944c13dceb9b94ee5af09129');
