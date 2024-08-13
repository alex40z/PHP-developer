<?php

namespace App\Core;

class Routes
{
     const URLS_LIST = [
        'migration' => [
            'GET' => 'Migration::startMigration'
        ],
        'user' => [
            'GET' => 'User::showUser',
            'PUT' => 'User::editUser'
        ],
        'login' => [
            'GET' => 'Access::loginUser'
        ],
        'logout' => [
            'POST' => 'Access::logoutUser'
        ],
        'reset_password' => [
            'GET' => 'Access::resetPassword'
        ],
        'update_password' => [
            'GET' => 'Access::updatePassword'
        ],
        'admin' => [
            'GET' => 'Admin::showUser',
            'POST' => 'Admin::addUser',
            'PUT' => 'Admin::editUser',
            'DELETE' => 'Admin::deleteUser'
        ],
        'file' => [
            'GET' => 'File::downloadFile',
            'POST' => 'File::uploadFile',
            'PUT' => 'File::editFile',
            'DELETE' => 'File::deleteFile'
        ],
        'directory' => [
            'GET' => 'Directory::showDir',
            'POST' => 'Directory::addDir',
            'PUT' => 'Directory::editDir',
            'DELETE' => 'Directory::deleteDir'
        ],
        'share' => [
            'GET' => 'Share::showShare',
            'POST' => 'Share::addShare',
            'DELETE' => 'Share::deleteShare'
        ]
    ];
}
