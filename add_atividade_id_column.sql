-- Script para adicionar a coluna atividade_id na tabela agenda_items
-- Execute este script no seu banco MySQL

USE todoapp;

-- Verificar se a coluna já existe
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'todoapp'
    AND TABLE_NAME = 'agenda_items'
    AND COLUMN_NAME = 'atividade_id'
);

-- Adicionar a coluna se ela não existir
SET @sql = IF(@column_exists = 0,
    'ALTER TABLE agenda_items ADD COLUMN atividade_id BIGINT UNSIGNED NULL AFTER user_id',
    'SELECT "Column atividade_id already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Adicionar foreign key se não existir
SET @fk_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE
    WHERE TABLE_SCHEMA = 'todoapp'
    AND TABLE_NAME = 'agenda_items'
    AND COLUMN_NAME = 'atividade_id'
    AND REFERENCED_TABLE_NAME = 'atividades'
);

SET @sql_fk = IF(@fk_exists = 0,
    'ALTER TABLE agenda_items ADD CONSTRAINT fk_agenda_items_atividade_id FOREIGN KEY (atividade_id) REFERENCES atividades(id) ON DELETE SET NULL',
    'SELECT "Foreign key already exists" as message'
);

PREPARE stmt_fk FROM @sql_fk;
EXECUTE stmt_fk;
DEALLOCATE PREPARE stmt_fk; 