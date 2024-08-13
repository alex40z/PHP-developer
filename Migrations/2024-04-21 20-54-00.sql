# Добавляем поле для авторизационного токена
ALTER TABLE users ADD COLUMN IF NOT EXISTS auth_token VARCHAR(50) NULL