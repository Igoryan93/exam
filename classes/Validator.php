<?php
/* Компонент валидации формы, взаимодействует с QueryBuilder */
/* Пример использования validatorExample.php */
class Validator  {
    private $passed = false, $error = [], $pdo;

    /* Подключаемся к объекту с БД и записываем в свойство $pdo, также получаем доступ к методу SELECT для проверки на уникальность поля */
    public function __construct() {
        $this->pdo = QueryBuilder::getInstance();
    }

    /* Метод проверки полей приходящих с формы $_POST, а в ассоциативном массиве который передает $fields принимаются все настройки проверки по полям */
    public function check($source, $fields) {
        if(!empty($source)) { /* Проверка на пустоту формы */
            /* Через цикл получаем все ключи и значения в ассоциативном массиве $fields */
            foreach ($fields as $item => $rules) {
                foreach ($rules as $rule => $rule_value) {
                    /* Проверяем на присутствие значения required у поля и на пустоту передаваемого поля */
                    if($rule === 'required' && empty($source[$item])) {
                        /* Добавление ошибки в свойство $error которое принимает массив  */
                        $this->addError("Поле $item обязательно для заполнения");
                    } else if (!empty($source[$item])) { /* Условие в котором пройдет проверка только если поля с формы будут заполнены */
                        switch ($rule) {
                            case 'min' : {
                                /* Количество введеных символов в поле не меньше чем указано в ключе min */
                                if(strlen($source[$item]) < $rule_value) {
                                    /* Добавление ошибки в свойство $error которое принимает массив  */
                                    $this->addError("Поле {$item} должно иметь минимум {$rule_value} символа");
                                }
                            } break;
                            case 'max' : {
                                /* Количество введеных символов в поле не превышает количетсво указаного в ключе max */
                                if(strlen($source[$item]) > $rule_value) {
                                    /* Добавление ошибки в свойство $error которое принимает массив  */
                                    $this->addError("Поле {$item} не должно превышать {$rule_value} символов");
                                }
                            } break;
                            case 'email' : {
                                /* Проверка на несоответствие поля на формат E-mail */
                                if(!filter_var($source[$item], FILTER_VALIDATE_EMAIL)) {
                                    /* Добавление ошибки в свойство $error которое принимает массив  */
                                    $this->addError("Поле {$item} должно быть в формате Email");
                                }
                            } break;
                            case 'unique' : {
                                /* Запись в переменную $user результата полученного в методе selectAll из таблицы `users` пользователя  */
                                $user = $this->pdo->selectAll('users', ['email', '=', $source[$item]]);
                                if($user->count()) { /* Проверка на количество найденых пользователей если не 0 */
                                    /* Добавление ошибки в свойство $error которое принимает массив  */
                                    $this->addError("Пользователь с E-mail {$source[$item]} уже существует");
                                }
                            } break;
                            case 'matches' : {
                                /* Проверка на идентичность введеных данных в двух полях */
                                if($source[$item] !== $source[$rule_value]) {
                                    /* Добавление ошибки в свойство $error которое принимает массив  */
                                    $this->addError("Пороль {$item} и {$rule_value} не совпадают");
                                }
                            } break;
                        }
                    }
                }
            }
        }

        /* Если свойство $error не имеет ошибок присваиваем свойству $passed = true */
        if(!$this->error()) {
            $this->passed = true;
        }

        return $this;
    }

    /* Добавление ошибки в массив свойства $error */
    public function addError($error) {
        return $this->error[] = $error;
    }

    /* Вывод массива ошибок в свойстве $error */
    public function error() {
        return $this->error;
    }

    /* Вывод результата в случае отсутствия ошибок вернет true в противном случае false */
    public function passed() {
        return $this->passed;
    }
}