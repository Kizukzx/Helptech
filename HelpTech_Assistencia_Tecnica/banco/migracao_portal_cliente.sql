-- Migração HelpTech: portal separado para clientes e equipe.
-- Execute no banco helptech já existente. Não apaga tabelas nem dados.
USE helptech2;

-- Contas de clientes devem usar perfil = 'Cliente'.
-- Esta tabela separa chamados do portal das ordens de serviço internas.
CREATE TABLE IF NOT EXISTS chamados_cliente (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  titulo VARCHAR(150) NOT NULL,
  descricao TEXT NOT NULL,
  prioridade ENUM('Baixa','Média','Alta') NOT NULL DEFAULT 'Média',
  status ENUM('Aberto','Em atendimento','Aguardando cliente','Resolvido','Cancelado') NOT NULL DEFAULT 'Aberto',
  resposta TEXT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  atualizado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX idx_chamados_cliente_usuario (usuario_id),
  INDEX idx_chamados_cliente_status (status),
  CONSTRAINT fk_chamados_cliente_usuario FOREIGN KEY (usuario_id)
    REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Conta de demonstração para testar a área do cliente.
-- Senha inicial: Cliente@123 (armazenada em texto por compatibilidade com o login atual).
-- Altere a senha após o primeiro acesso e, em produção, use password_hash().
INSERT INTO usuarios (nome,email,senha,perfil)
SELECT 'Cliente Demonstração','cliente@helptech.local','Cliente@123','Cliente'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='cliente@helptech.local');

-- Garanta que as contas da equipe mantenham perfis reconhecidos pelo sistema.
UPDATE usuarios SET perfil='Administrador' WHERE LOWER(perfil) IN ('admin','administrador');
UPDATE usuarios SET perfil='Técnico' WHERE LOWER(perfil) IN ('tecnico','técnico','trabalhador');

-- Especificações do eletrônico informado pelo cliente.
DELIMITER $$
CREATE PROCEDURE helptech_add_ticket_specs()
BEGIN
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='chamados_cliente' AND COLUMN_NAME='tipo_equipamento') THEN
    ALTER TABLE chamados_cliente ADD COLUMN tipo_equipamento VARCHAR(100) NULL AFTER resposta;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='chamados_cliente' AND COLUMN_NAME='marca') THEN
    ALTER TABLE chamados_cliente ADD COLUMN marca VARCHAR(100) NULL AFTER tipo_equipamento;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='chamados_cliente' AND COLUMN_NAME='modelo') THEN
    ALTER TABLE chamados_cliente ADD COLUMN modelo VARCHAR(120) NULL AFTER marca;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='chamados_cliente' AND COLUMN_NAME='numero_serie') THEN
    ALTER TABLE chamados_cliente ADD COLUMN numero_serie VARCHAR(120) NULL AFTER modelo;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='chamados_cliente' AND COLUMN_NAME='estado_fisico') THEN
    ALTER TABLE chamados_cliente ADD COLUMN estado_fisico TEXT NULL AFTER numero_serie;
  END IF;
  IF NOT EXISTS (SELECT 1 FROM information_schema.COLUMNS WHERE TABLE_SCHEMA=DATABASE() AND TABLE_NAME='chamados_cliente' AND COLUMN_NAME='acessorios') THEN
    ALTER TABLE chamados_cliente ADD COLUMN acessorios VARCHAR(255) NULL AFTER estado_fisico;
  END IF;
END$$
CALL helptech_add_ticket_specs()$$
DROP PROCEDURE helptech_add_ticket_specs$$
DELIMITER ;
