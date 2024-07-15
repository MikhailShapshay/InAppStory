<?php

namespace tests\unit\controllers\api;

use Yii;
use app\models\User;
use app\models\PromoCode;
use Codeception\Test\Unit;
use app\controllers\api\PromoCodeRestController;
use tests\_support\UnitHelper;

class PromoCodeRestControllerTest extends Unit
{
    /**
     * @var \tests\UnitTester
     */
    protected $tester;

    public function testGetApiKeySuccess()
    {
        // Создаем фейкового пользователя для теста
        $user = new User([
            'username' => 'testuser',
            'password' => 'testpassword',
            'auth_key' => 'testapikey',
        ]);
        $user->save(false);

        // Мокаем Request с правильными данными
        $this->tester->mockRequest([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);

        // Создаем контроллер и вызываем действие
        $controller = new PromoCodeRestController('promo-code-rest', Yii::$app);
        $response = $controller->actionGetApiKey();

        // Проверяем результат
        $this->assertEquals(['api_key' => 'testapikey'], $response);
    }

    public function testGetApiKeyFail()
    {
        // Создаем фейкового пользователя для теста
        $user = new User([
            'username' => 'testuser',
            'password' => 'testpassword',
        ]);
        $user->save(false);

        // Мокаем Request с неправильными данными
        $this->tester->mockRequest([
            'username' => 'testuser',
            'password' => 'wrongpassword',
        ]);

        // Создаем контроллер и вызываем действие
        $controller = new PromoCodeRestController('promo-code-rest', Yii::$app);
        $response = $controller->actionGetApiKey();

        // Проверяем результат
        $this->assertEquals(['error' => 'Неверные учетные данные'], $response);
        $this->assertEquals(401, Yii::$app->response->statusCode);
    }

    public function testGetPromoCodeSuccess()
    {
        // Создаем фейкового пользователя и промокод для теста
        $user = new User([
            'id' => 1,
            'username' => 'testuser',
            'auth_key' => 'testapikey',
        ]);
        $user->save(false);

        $promoCode = new PromoCode([
            'code' => 'PROMO123',
            'user_id' => null, // промокод доступен
        ]);
        $promoCode->save(false);

        // Мокаем пользователя
        $this->tester->loginUser($user);

        // Создаем контроллер и вызываем действие
        $controller = new PromoCodeRestController('promo-code-rest', Yii::$app);
        $response = $controller->actionGetPromoCode();

        // Проверяем результат
        $this->assertEquals('success', $response['status']);
        $this->assertEquals('Промокод успешно выдан.', $response['message']);
        $this->assertEquals('PROMO123', $response['promo_code']);
    }

    public function testGetPromoCodeAlreadyExists()
    {
        // Создаем фейкового пользователя и промокод для теста
        $user = new User([
            'id' => 1,
            'username' => 'testuser',
            'auth_key' => 'testapikey',
        ]);
        $user->save(false);

        $promoCode = new PromoCode([
            'code' => 'PROMO123',
            'user_id' => 1, // промокод уже использован
        ]);
        $promoCode->save(false);

        // Мокаем пользователя
        $this->tester->loginUser($user);

        // Создаем контроллер и вызываем действие
        $controller = new PromoCodeRestController('promo-code-rest', Yii::$app);
        $response = $controller->actionGetPromoCode();

        // Проверяем результат
        $this->assertEquals('error', $response['status']);
        $this->assertEquals('У вас уже есть промокод.', $response['message']);
        $this->assertEquals('PROMO123', $response['promo_code']);
    }

    public function testGetPromoCodeNotAvailable()
    {
        // Создаем фейкового пользователя для теста
        $user = new User([
            'id' => 1,
            'username' => 'testuser',
            'auth_key' => 'testapikey',
        ]);
        $user->save(false);

        // Мокаем пользователя
        $this->tester->loginUser($user);

        // Удаляем все промокоды для проверки ситуации, когда промокоды недоступны
        PromoCode::deleteAll();

        // Создаем контроллер и вызываем действие
        $controller = new PromoCodeRestController('promo-code-rest', Yii::$app);
        $response = $controller->actionGetPromoCode();

        // Проверяем результат
        $this->assertEquals('error', $response['status']);
        $this->assertEquals('Не удалось получить промокод: Нет доступных промокодов.', $response['message']);
    }
}
