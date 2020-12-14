<?php

require_once "classes/Validator.php";
require_once "classes/QueryBuilder.php";

/* Валидация формы принимает поля с атрибутами 'name' */
/* Проверка доступна по следующим правилам:
    1. required - обязательное поле
    2. unique   - уникальность, проверка существует ли данное значение в БД (в примере БД 'users')
    3. email    - соответствует ли поле формату email
    4. max      - максимальное количество символов доступное для ввода в поле
    5. min      - минимальное количество симолов доступное для ввода в поле
    6. matches  - соотвествуют ли два поле на идентичный ввод
*/

$validation = new Validator();

$validate = $validation->check($_POST, [
    'email' => [
        'required' => true,
        'min'      => 3,
        'max'      => 25,
        'unique'   => 'users',
        'email'    => true
    ],
    'name' => [
        'required' => true,
        'min'      => 3,
        'max'      => 25
    ],
    'surname' => [
        'required' => true,
        'min'      => 3,
        'max'      => 25
    ],
    'password' => [
        'required' => true,
        'min'      => 3,
        'max'      => 25
    ],
    'password_again' => [
        'required' => true,
        'matches'  => 'password'
    ]
]);

/* Если форма прошла без ошибок, выводим что результат прошел успешно */
if($validate->passed()) {
    echo 'We have passed';
} else {
    /* В противном случае выводим все полученные ошибки из полей */
    $errors = $validate->error();
}

?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-giJF6kkoqNQ00vy+HMDP7azOuL0xtbfIcaT9wjKHr8RbDVddVHyTfAAsrekwKmP1" crossorigin="anonymous">
</head>
<body>
<div class="container">
    <div class="row">
        <div class="col-lg-8 offset-2">
            <!--  Вывод всех доступных ошибок через цикл foreach -->
            <?php if($errors): ?>
                <div class="alert alert-danger">
                    <?php foreach ($errors as $error): ?>
                        <?php echo $error . "<br>"?>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <h1>Заполните поля</h1>
            <form action="" method="POST">
                <div class="mb-3">
                    <label for="" class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Name</label>
                    <input type="text" name="name" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Surname</label>
                    <input type="text" name="surname" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Password</label>
                    <input type="password" name="password" class="form-control">
                </div>
                <div class="mb-3">
                    <label for="" class="form-label">Password again</label>
                    <input type="password" name="password_again" class="form-control">
                </div>
                <div class="form-group">
                    <input type="submit" class="btn btn-success">
                </div>
            </form>
        </div>
    </div>
</div>
</body>
</html>