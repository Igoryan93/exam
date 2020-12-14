<?php
    require_once "../classes/Route.php"; /* Подключение компонента Route */

    /* Работает при условии файла .htaccess который ссылает по умолчанию на index.php файл */

    /* Проверочный массив $url, проверяет строки url на соотвествие */
    $url = [
        '/'        => '../view/homepage.php',
        '/example' => '../view/example.php',
    ];
    /* Запись в переменную $server url адресной строки */
    $server = $_SERVER['REQUEST_URI'];

    Route::to($server, $url); /* Выхов объекта Route, передаются полученный url адрес в переменной $server и массив $url с доступными страницами */


