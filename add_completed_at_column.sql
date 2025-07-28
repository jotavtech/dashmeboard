-- Script para adicionar a coluna completed_at na tabela atividades
-- Execute este script no seu banco MySQL

USE todoapp;

-- Verificar se a coluna já existe
SET @column_exists = (
    SELECT COUNT(*)
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = 'todoapp'
    AND TABLE_NAME = 'atividades'
    AND COLUMN_NAME = 'completed_at'
);

-- Adicionar a coluna se ela não existir
SET @sql = IF(@column_exists = 0,
    'ALTER TABLE atividades ADD COLUMN completed_at TIMESTAMP NULL AFTER progresso',
    'SELECT "Column completed_at already exists" as message'
);

PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

-- Verificar se a coluna foi adicionada
SELECT COLUMN_NAME, DATA_TYPE, IS_NULLABLE, COLUMN_DEFAULT
FROM INFORMATION_SCHEMA.COLUMNS
WHERE TABLE_SCHEMA = 'todoapp'
AND TABLE_NAME = 'atividades'
AND COLUMN_NAME = 'completed_at'; 