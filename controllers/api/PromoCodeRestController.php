<?php

namespace app\controllers\api;

use Throwable;
use Yii;
use yii\filters\auth\CompositeAuth;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\rest\ActiveController;
use yii\web\NotFoundHttpException;
use app\models\PromoCode;

class PromoCodeRestController extends ActiveController
{
    public $modelClass = PromoCode::class;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                HttpBearerAuth::class,
                [
                    'class' => QueryParamAuth::class,
                    'tokenParam' => 'api-key',
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * Действие для получения доступного промокода
     *
     * @return array|string[]
     */
    public function actionGetPromoCode(): array
    {
        $user = Yii::$app->user->identity;

        // Поиск промокода пользователя
        $promoCode = PromoCode::findOne(['user_id' => $user->id]);

        if ($promoCode === null) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $promoCode = PromoCode::findAvailablePromoCode();

                if ($promoCode === null) {
                    throw new NotFoundHttpException('Нет доступных промокодов.');
                }

                $promoCode->markAsUsed($user->id);
                $transaction->commit();

                return [
                    'status' => 'success',
                    'message' => 'Промокод успешно выдан.',
                    'promo_code' => $promoCode->code,
                ];
            } catch (Throwable $e) {
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