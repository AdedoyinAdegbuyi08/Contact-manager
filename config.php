<?php
return [
    'dsn'  => getenv('CM_DSN')  ?: 'mysql:host=localhost;dbname=contact_manager;charset=utf8mb4',
    'user' => getenv('CM_USER') !== false ? getenv('CM_USER') : 'root',
    'pass' => getenv('CM_PASS') !== false ? getenv('CM_PASS') : '',
];