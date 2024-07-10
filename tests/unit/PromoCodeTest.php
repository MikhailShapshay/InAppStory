<?php

use app\models\PromoCode;
use app\models\User;
use Codeception\Test\Unit;

class PromoCodeTest extends Unit
{
	/**
	 * @var \UnitTester
	 */
	protected $tester;

	protected function _before()
	{
		$this->tester->haveRecord(User::class, [
			'username' => 'testuser',
			'api_key' => 'testkey',
			'password_hash' => Yii::$app->security->generatePasswordHash('testpassword'),
			'email' => 'testuser@example.com',
		]);

		$this->tester->haveRecord(PromoCode::class, [
			'code' => 'PROMO1',
			'is_used' => false,
		]);
	}

	public function testGetPromoCode()
	{
		$response = $this->tester->sendGET('/promo-code/get', ['api-key' => 'testkey']);
		$response->seeResponseCodeIs(200);
		$response->seeResponseIsJson();
		$response->seeResponseContains('PROMO1');

		$this->tester->dontSeeRecord(PromoCode::class, ['code' => 'PROMO1', 'is_used' => false]);
	}
}
