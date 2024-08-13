# Создание глобальной корневой папки (нужна, чтобы обойти невозможность
# создания в MySQL уникальных индексов с NULL-полями)
INSERT IGNORE INTO files_list (parent_id, is_dir, owner_id, original_file_name)
    VALUES (1, 1, 1, 'ВСЕ ФАЙЛЫ')
