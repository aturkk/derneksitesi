<?php
/*
 * Örnek yapılandırma dosyası.
 * Kurulum sihirbazı (public/install.php) gerçek değerlerle
 * config/config.php dosyasını kendisi oluşturur.
 */
return [
    'debug' => true,
    'db' => [
        'host'     => '127.0.0.1',
        'port'     => 3306,
        'database' => 'dernek_sitesi',
        'username' => 'root',
        'password' => '',
        'charset'  => 'utf8mb4',
    ],
];
