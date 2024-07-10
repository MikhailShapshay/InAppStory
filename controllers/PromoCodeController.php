<?php

namespace app\controllers;

use Yii;
use yii\rest\Controller;
use app\models\PromoCode;
use yii\web\NotFoundHttpException;
use yii\db\StaleObjectException;
use yii\filters\auth\HttpBearerAuth;
use app\components\ApiKeyAuth;

class PromoCodeController extends Controller
{
	public function behaviors()
	{
		$behaviors = parent::behaviors();
		$behaviors['authenticator'] = [
			'class' => ApiKeyAuth::class,
		];
		return $behaviors;
	}

	public function actionGet()
	{
		$user = Yii::$app->user->identity;

		$transaction = Yii::$app->db->beginTransaction();
		try {
			$promoCode = PromoCode::find()
				->where(['is_used' => false])
				->orderBy(['id' => SORT_ASC])
				->limit(1)
				->forUpdate() // Блокируем запись
				->one();

			if ($promoCode === null) {
				throw new NotFoundHttpException('No promo codes available.');
			}

			$promoCode->is_used = true;
			if ($promoCode->save()) {
				$transaction->commit();
				return $promoCode;
			} else {
				throw new \Exception('Failed to retrieve promo code.');
			}
		} catch (\Throwable $e) {
			$transaction->rollBack();
			throw $e;
		}
	}
}
