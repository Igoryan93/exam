<?php
/* Компонент QueryBuilder построитель запросов */
/* Примеры использования компонента в queryExample.php */
class QueryBuilder {
    private static $instance;
    private $pdo, $error = false, $query, $result, $count;

    /* Подключение к базе данных*/
    private function __construct() {
        try {
            $this->pdo = new PDO("mysql:host=localhost; dbname=exam_users", "root", "root");
        } catch (PDOException $exception) {
            die($exception);
        }
    }

    /* Вызов класса внутри самого класса делает доступной такую запись Database::getInstance  */
    public static function getInstance() {
        if(self::$instance == null) {
            return self::$instance = new QueryBuilder();
        }

        return self::$instance;
    }


    /* Метод action принимает - SQL строку SELECT * или DELETE, название таблицы и значения в виде массива */
    /* В методе action происходит формирование sql строки перед отправкой в БД */
    public function action($action, $table, $values) {
        if(count($values) === 3) { /* Проверка на соответствие количество передаваемых значений $values */

            $operators = ['>', '<', '=', '<=', '>=']; /* Доступные операторы */

            /* Запись в переменные значения из массива $values */
            $field = $values[0];
            $operator = $values[1];
            $value = $values[2];

            if(in_array($operator, $operators)) { /* Проверка на соотвествие оператора в переменной $operator из доступных операторов в массиве $operators */
                $sql = "{$action} FROM {$table} WHERE {$field} {$operator} ?"; /* Сформированная SQL строка запроса */
                if(!$this->query($sql, [$value])->error()) { /* Если ошибки сформированного SQL запроса нету то возвращаем результат объекта. */
                    return $this;
                }
            }
        }
        return false;
    }

    /* Метод query отвечает за выполнение запроса sql и получения результата.
       Принимает SQL строку и значения в виде массива для работы с несколькими полями сразу */
    public function query($sql, $where= []) {
        $this->error = false;
        $this->query = $this->pdo->prepare($sql); /* Подготовка SQL запроса */

        /* Обработка предаваемых значений для изменения соответствующих полей (можно передать неограниченное количество) */
        if($where) {
            $i = 1;
            foreach ($where as $value) {
                $this->query->bindValue($i, $value);
                $i++;
            }
        }

        if(!$this->query->execute()) { /* Если SQL запрос не выполнен вызываем ошибку иначе возвращаем объект */
            $this->error = true;
        } else {
            $this->result = $this->query->fetchAll(PDO::FETCH_OBJ); /* Запись в свойство $result получаемого результата в виде объекта */
            $this->count  = $this->query->rowCount(); /* Запись в свойство $count количество строк получаемого результата из таблицы */
        }

        return $this;
    }

    /* Метод selectAll принимает название таблицы и значения в виде массива.
      Передает в метод action данные для дальнейшей обработки. */
    public function selectAll($table, $values) {
        return $this->action('SELECT *', $table, $values);
    }

    /* Метод delete принимает название таблицы и значения в виде массива.
    Передает в метод action данные для дальнейшей обработки. */
    public function delete($table, $values) {
        return $this->action('DELETE', $table, $values);
    }

    /* Метод insert принимает название таблицы и значения в виде массива. */
    public function insert($table, $values) {

        /* Создаем строку из меток '?' того количетсва которое принимается в массиве $values через ',' */
        $where = '';
        foreach ($values as $item) {
            $where .= "?, ";
        }

        /* Удаляем лишнюю ',' справа в полученной строке */
        $where = rtrim($where, ', ');

        /* Выбираем все ключи из принятого массива $values */
        $keys = array_keys($values);

        /* Формируем строку из ключей разделенную ',' и '`' */
        $fields = "`" . implode('`, `', $keys) . "`";

        /* Формируем строку SQL запроса из получившихся манипуляций */
        $sql = "INSERT INTO {$table} ({$fields}) VALUES ({$where})";

        /* Передаем SQL запрос, в случее успеха получаем true иначе false */
        if(!$this->query($sql, $values)) {
            return true;
        }

        return false;
    }

    /* Метод delete принимает название таблицы, id - пользователя и значения в виде массива. */
    public function update($table, $id, $values) {
        /* Создаем строку из ключей вида 'name=?, surname=?,'результат сформированной строки будет равна количеству содержимого в принятом массиве $values */
        $where = '';

        foreach ($values as $key => $val) {
            $where .= "{$key}=?, ";
        }

        /* Удаляем лишнюю ',' справа в полученной строке  */
        $where = rtrim($where, ', ');

        /* Формируем строку SQL запроса из получившихся манипуляций */
        $sql = "UPDATE {$table} SET {$where}  WHERE id={$id}";

        /* Передаем SQL запрос, в случее успеха получаем true иначе false */
        if(!$this->query($sql, $values)->error()) {
            return true;
        }

        return false;
    }

    /* Метод для вывода результата из свойста $result */
    public function result() {
        return $this->result;
    }

    /* Метод для вывода количество строк из свойста $result */
    public function count() {
        return $this->count;
    }

    /* Метод для вывода одной записи из свойста $result */
    public function first() {
        return $this->result()[0];
    }

    /* Метод для вывода ошибки в случае невыполнения запроса к таблице с пользователями */
    public function error() {
        return $this->error;
    }

}