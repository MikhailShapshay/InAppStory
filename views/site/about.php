<?php

/** @var yii\web\View $this */

use yii\helpers\Html;

$this->title = 'О приложении';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-about">
    <h1><?= Html::encode($this->title) ?></h1>

    <h3>Задача</h3>
    <p>Написать простой сервис для выдачи промокодов с REST API</p>
    <p>В БД должны храниться уникальные промокоды.</p>
    <p>Система должна иметь как минимум один endpoint.</p>
    <p>По этому endpoint можно получить промокод.</p>
    <p>Каждому пользователю доступен только один уникальный промокод.</p>
    <p>Предусмотреть конкурентный доступ и корректную обработку ошибок.</p>
    <p>Идентификация пользователя должна происходить по api-key</p>
    <p>Наличие скриптов для заполнения тестовыми данными - будет плюсом.</p>
    <p>Docker - будет плюсом.</p>
    <p>Unit-тесты - будут плюсом.</p>
    <p>Наличие open-api/swagger документации - будет плюсом.</p>

    <h3>Зависимости</h3>
    <ul>
        <li>Фреймворк: Yii2</li>
        <li>PHP: 8.2</li>
        <li>DВ: mysql</li>
        <li>Сторонние пакеты:
            <ul>
                <li>"zircote/swagger-php"</li>
                <li>"doctrine/annotations"</li>
                <li>"phpunit/phpunit"</li>
            </ul>
        </li>
    </ul>

    <h3>Начало работы</h3>

    <p>Перейдите по адресу:

    <code>
    http://localhost:8282/
    </code></p>

    <p>Логин и пароль для входа:
    <ul>
        <li>admin</li>
        <li>admin123</li>
    </ul></p>

    <h3>Генерация начальных данных</h3>

    <p>На вкладке "Пользователи" нажмите кнопку "Сгенерировать 50 пользователей".

    <code>
        http://localhost:8282/
    </code></p>

    <p>Пользователи генерируются с паролем по умолчанию: <b>user123</b>.</p>

    <p>На вкладке "Промокоды" нажмите кнопку "Сгенерировать 50 Промокодов".

    <code>
    http://localhost:8282/promo-code/index
        </code></p>

    <h3>PromoCode API</h3>

    <p>Для доступа к Swagger перейдите по адресу:

    <code>
    http://localhost:8282/api-doc/
        </code></p>

    <p>Для получения ключа доступа к API воспользуйтесь методом "Получить api_key" из раздела "API-KEY" в формате:<br>

    <code>
    {<br>
    "username": "логин",<br>
    "password": "пароль"<br>
    }
        </code></p>

    <p>Полученный ключ необходимо внести в форму авторизации по кнопке "Authorize".</p>

    <p>Для получения промокода авторизированным пользователем воспользуйтесь методом "Получить промокод" из раздела "PromoCode".</p>

    <h3>Тестирование</h3>

    <p>Тесты PHPUnit для REST API находятся в файле:

    <code>
    tests/unit/controllers/api/PromoCodeRestControllerTest.php
        </code></p>
</div>
