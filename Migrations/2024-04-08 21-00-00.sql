# Создание таблицы shares_list
CREATE TABLE IF NOT EXISTS shares_list (
    share_id INT PRIMARY KEY AUTO_INCREMENT,
    file_id INT NOT NULL,
    member_id INT NOT NULL,
    sharing_time TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT check_file_member UNIQUE KEY (file_id, member_id),
    FOREIGN KEY (file_id) REFERENCES files_list(file_id) ON DELETE CASCADE,
    FOREIGN KEY (member_id) REFERENCES users(user_id) ON DELETE CASCADE
)
