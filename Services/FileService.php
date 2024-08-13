<?php

namespace App\Services;

use App\Core\App;
use App\Core\Config\Config;
use App\Core\DB;
use App\Core\Exceptions\ForbiddenException;
use App\Core\Exceptions\WrongRequestException;
use PDOException;
use Ramsey\Uuid\Uuid;

class FileService extends BaseService
{
    private function includeTrailingDelimiter(string $path): string
    {
        if (!str_ends_with($path, '/')) {
            $path .= '/';
        }
        return $path;
    }

    private function getStoragePath(): string
    {
        $config = new Config();
        return $this->includeTrailingDelimiter($config->configParam('storage_path'));
    }

    private function checkFileName(string $fileName): void
    {
        if (mb_strlen($fileName) > 255 || preg_match('/[\/:*?"<>|\\\\]/', $fileName)) {
            throw new WrongRequestException('Недопустимое имя файла');
        }
    }

    public function downloadFile(array $params): void
    {
        $fileID = $params['file_id'];
        $result = DB::getInstance()->selectRowBy('files_list', ['is_dir', 'owner_id', 'file_name', 'original_file_name'],
            ['file_id' => $fileID]);
        if (!$result || $result['is_dir'] == 1) {
            throw new WrongRequestException('Неверно указан ID файла');
        }
        if ($result['owner_id'] != App::$userID) {
            $resultShare = DB::getInstance()->selectRowBy('shares_list', ['1'],
                ['file_id' => $fileID, 'member_id' => App::$userID]);
            if (!$resultShare) {
                throw new ForbiddenException('Нет прав доступа к указанному файлу');
            }
        }
        $fileName = $result['file_name'];
        $originalFileName = $result['original_file_name'];
        header('Content-Description: File Transfer');
        header('Content-Disposition: attachment; filename=' . basename($originalFileName));
        header('Content-Type: ' . mime_content_type($fileName));
        header('Cache-Control: must-revalidate');
        readfile($this->getStoragePath() . $fileName);
    }

    public function uploadFile(array $params): void
    {
        $directoryID = $params['directory_id'];
        App::$security->checkDirectoryOwner($directoryID);
        if (!isset($_FILES['file']['name'])) {
            throw new WrongRequestException('Не отправлен файл');
        }
        if ($_FILES['file']['size'] > 2 * 1024 * 1024 * 1024) {
            throw new WrongRequestException('Размер файла превышает 2 Гб');
        }
        $originalFileName = $_FILES['file']['name'];
        $storageFileName = Uuid::uuid4() . '.' . strtolower(pathinfo($originalFileName)['extension']);
        move_uploaded_file($_FILES['file']['tmp_name'], $this->getStoragePath() . $storageFileName);
        try {
            DB::getInstance()->insertRow('files_list', ['parent_id' => $directoryID,
                'is_dir' => 0,
                'owner_id' => App::$userID,
                'original_file_name' => $originalFileName,
                'file_name' => $storageFileName]);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'check_file_duplicate')) {
                    unlink($this->getStoragePath() . $storageFileName);
                    throw new WrongRequestException('Файл уже загружен');
                } else if (str_contains($e->getMessage(), 'check_parent')) {
                    throw new WrongRequestException('Неверно указан ID папки');
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }

    public function editFile(array $params): void
    {
        $fileID = $params['file_id'];
        $fileName = $params['file_name'];
        App::$security->checkFileOwner((int)$fileID);
        $this->checkFileName($fileName);
        try {
            DB::getInstance()->updateRows('files_list', ['original_file_name' => $fileName], ['file_id' => $fileID]);
        } catch(PDOException $e) {
            if ($e->getCode() == 23000) {
                if (str_contains($e->getMessage(), 'check_file_duplicate')) {
                    throw new WrongRequestException('Файл с таким именем уже существует');
                } else {
                    throw $e;
                }
            } else {
                throw $e;
            }
        }
    }

    public function deleteFile(array $params): void
    {
        $fileID = $params['file_id'];
        $fileName = App::$security->checkFileOwner((int)$fileID);
        DB::getInstance()->deleteRows('files_list', ['file_id' => $fileID]);
        unlink($this->getStoragePath() . $fileName);
    }
}