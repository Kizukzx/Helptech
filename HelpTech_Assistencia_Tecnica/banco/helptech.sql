CREATE DATABASE IF NOT EXISTS helptech2 CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE helptech;
DROP TABLE IF EXISTS chamados;
DROP TABLE IF EXISTS usuarios;
CREATE TABLE usuarios (
 id INT AUTO_INCREMENT PRIMARY KEY,
 nome VARCHAR(100) NOT NULL,
 email VARCHAR(120) NOT NULL UNIQUE,
 senha VARCHAR(255) NOT NULL,
 perfil VARCHAR(30) NOT NULL DEFAULT 'Técnico'
);
CREATE TABLE chamados (
 id INT AUTO_INCREMENT PRIMARY KEY,
 titulo VARCHAR(150) NOT NULL,
 descricao TEXT NOT NULL,
 prioridade ENUM('Baixa','Média','Alta') NOT NULL DEFAULT 'Média',
 status ENUM('Aberto','Em atendimento','Resolvido') NOT NULL DEFAULT 'Aberto',
 usuario_id INT NOT NULL,
 criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
 CONSTRAINT fk_chamados_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
INSERT INTO usuarios (nome,email,senha,perfil) VALUES
('Administrador','admin@helptech.local','123456','Administrador'),
('Marina Silva','marina@helptech.local','123456','Técnico'),
('Carlos Souza','carlos@helptech.local','123456','Técnico');
INSERT INTO chamados (titulo,descricao,prioridade,status,usuario_id) VALUES
('Computador não inicia','O computador apresenta falha durante a inicialização.','Alta','Aberto',1),
('Impressora sem conexão','A impressora do setor administrativo não está disponível na rede.','Média','Em atendimento',2),
('Instalação de software','Solicitação de instalação de ferramenta utilizada pela equipe.','Baixa','Resolvido',3);
