<?php
/* Компонент для работы с сессиями и создания флэш сообщения */
/* Пример создания флэш сообщения sessionFlash.php  */
/* Пример вывода флэш сообщения sessionFlashExample.php */
class  Session {

    /* Проверка на существование сессии с определенным именем '$name' вернет true или false */
    public static function exists($name) {
        return (isset($_SESSION[$name])) ? true : false;
    }

    /* Удаление существующей сессии '$name' */
    public static function delete($name) {
        if(self::exists($name)) {
            unset($_SESSION[$name]);
        }
    }

    /* Создание сессии с именем '$name' c присвоением значения '$value' */
    public static function put($name, $value) {
        return $_SESSION[$name] = $value;
    }

    /* Вызов сессии с именем '$name' */
    public static function get($name) {
        return $_SESSION[$name];
    }

    /* Создание флэш сообщения и ее иницаилизация, принимает название '$name' флэш сообщения и ее содержимое в виде строки */
    public static function flash($name, $value = null) {
        if(self::exists($name) && self::get($name)) {   /* Проверка на существование сессии с '$name' */
            $message = self::get($name); /* Запись в переменную $message сессии c именем '$name' */
            self::delete($name); /* Удаление текущей сессии, после обновления страницы удалится */
            return $message; /* Возваращем в качестве результата сессию */
        } else {
            return self::put($name, $value); /* Записывает новую сессию с именем '$name' где '$value' содержимое в виде строки */
        }
    }
}