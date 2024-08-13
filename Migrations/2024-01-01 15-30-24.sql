# Создание таблицы users
CREATE TABLE IF NOT EXISTS users (
    user_id INT PRIMARY KEY auto_increment,
    login VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(100) NOT NULL,
    role VARCHAR(100) NOT NULL,
    nickname VARCHAR(100) NOT NULL,
    phone VARCHAR(100),
    reset_token VARCHAR(100)
)
