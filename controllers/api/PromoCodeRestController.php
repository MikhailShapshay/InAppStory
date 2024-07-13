<?php

namespace app\controllers\api;
use Yii;
use yii\filters\auth\HttpBasicAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;
use yii\rest\Controller;
use yii\web\Response;
use yii\web\NotFoundHttpException;
use app\models\PromoCode;
use app\components\ApiKeyAuth;

class PromoCodeRestController extends ActiveController
{
    public $modelClass = PromoCode::class;
    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::className(),
        ];
        return $behaviors;
    }

    // Действие для получения доступного промокода
    public function actionGetPromoCode()
    {
        $user = Yii::$app->user->identity;

        // Поиск промокода пользователя
        $promoCode = PromoCode::findOne(['user_id' => $user->id]);
        //$promoCode = PromoCode::findOne(['user_id' => 1154]);

        if ($promoCode === null) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $promoCode = PromoCode::findAvailablePromoCode();

                if ($promoCode === null) {
                    throw new NotFoundHttpException('Нет доступных промокодов.');
                }

                $promoCode->markAsUsed($user->id);
                //$promoCode->markAsUsed(1154);
                $transaction->commit();

                return [
                    'status' => 'success',
                    'message' => 'Промокод успешно выдан.',
                    'promo_code' => $promoCode->code,
                ];
            } catch (\Throwable $e) {
                $transaction->rollBack();
                return [
                    'status' => 'error',
                    'message' => 'Не удалось получить промокод: ' . $e->getMessage(),
                ];
            }
        } else {
            // Если у пользователя уже есть промокод, возвращаем его
            return [
                'status' => 'error',
                'message' => 'У вас уже есть промокод.',
                'promo_code' => $promoCode->code,
            ];
        }
    }
}