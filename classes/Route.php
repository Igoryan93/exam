<?php
/* Компонет Route */
/* Пример использования в index.php  */
class Route {
    public static function to($server, $url) {
        /* Функция проверки сходства получаемой строки url адреса с одним из ключей в массиве $url
           Принимает адрес url строки и массив с доступными страницами */
        if(array_key_exists($server, $url)) {
            include $url[$server]; exit; /* Подключаем файл соотвествующий файл из массива $url */
        } else {
            include "../404.php"; /* Если url адреса нет в массиве $url то отправляем пользователя на страницу 404.php */
        }
    }

}