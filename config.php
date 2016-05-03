<?php
define('B', 1);
define('KB', 1024 * B);
define('MB', 1024 * KB);
define('GB', 1024 * MB);

return array(
    'store_dir' => 'C:\\chomik',

    'allowed_ext' => array(
        'txt', 'pdf', 'doc', 'zip', 'rar', '7z'
    ),

    // Please make sure that php.ini values (upload_max_file and post_max_filesize) are >= max_file_size_in_bytes
    'max_file_size_in_bytes' => 100 * KB,

    'password_upload' => 'upload',
    'password_download' => 'download',
    'password_delete' => 'delete'
);