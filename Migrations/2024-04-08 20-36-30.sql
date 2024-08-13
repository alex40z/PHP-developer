# Создание таблицы files_list
CREATE TABLE IF NOT EXISTS files_list (
    file_id INT PRIMARY KEY auto_increment,
    parent_id INT NOT NULL,
    is_dir BIT NOT NULL,
    owner_id INT NOT NULL,
    original_file_name VARCHAR(100) NOT NULL,
    file_name VARCHAR(100) NULL,
    loading_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT check_file_duplicate UNIQUE KEY (parent_id, owner_id, original_file_name),
    CONSTRAINT check_parent FOREIGN KEY (parent_id) REFERENCES files_list(file_id),
    FOREIGN KEY (owner_id) REFERENCES users(user_id) ON DELETE CASCADE
)
