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
use app\models\User;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="PromoCode API", version="1.0.0")
 * @OA\Server(url="http://localhost:8282")
 *
 * @OA\SecurityScheme(
 *     type="http",
 *     scheme="bearer",
 *     securityScheme="API-KEY",
 *     name="Authorization",
 *     in="header",
 *     description="Введите ваш API ключ"
 * )
 *
 * @OA\Tag(
 *     name="API-KEY",
 *     description="API получения ключа"
 * )
 *
 * @OA\Tag(
 *     name="PromoCode",
 *     description="API для работы с промокодами"
 * )
 *
 * @OA\PathItem(
 *     path="/api/promo-code-rest"
 * )
 */
class PromoCodeRestController extends ActiveController
{
    public $modelClass = PromoCode::class;

    /**
     * @return array
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();

        // Устанавливаем аутентификацию только для actionGetPromoCode
        $behaviors['authenticator'] = [
            'class' => CompositeAuth::class,
            'authMethods' => [
                [
                    'class' => HttpBearerAuth::class,
                ],
                [
                    'class' => QueryParamAuth::class,
                    'tokenParam' => 'auth_key',
                ],
            ],
            'except' => ['get-api-key'], // Исключаем actionGetApiKey из проверки
        ];

        return $behaviors;
    }

    /**
     * @OA\Post(
     *     path="/api/promo-code-rest/get-api-key",
     *     summary="Получить api_key",
     *     tags={"API-KEY"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\MediaType(
     *             mediaType="application/json",
     *             @OA\Schema(
     *                 @OA\Property(property="username", type="string", example="admin"),
     *                 @OA\Property(property="password", type="string", example="admin123"),
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Успешная авторизация",
     *         @OA\JsonContent(
     *             @OA\Property(property="api_key", type="string"),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Ошибка авторизации",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string"),
     *         )
     *     )
     * )
     */
    public function actionGetApiKey(): array
    {
        $request = Yii::$app->getRequest();
        $username = $request->getBodyParam('username');
        $password = $request->getBodyParam('password');

        $user = User::findByUsername($username);
        if ($user !== null && $user->validatePassword($password)) {
            return ['api_key' => $user->auth_key];
        } else {
            Yii::$app->response->statusCode = 401;
            return ['error' => 'Неверные учетные данные'];
        }
    }

    /**
     * @OA\Get(
     *     path="/api/promo-code-rest/get-promo-code",
     *     summary="Получить промокод",
     *     description="Возвращает доступный промокод для авторизованного пользователя",
     *     tags={"PromoCode"},
     *     security={{ "bearerAuth":{} }},
     *     @OA\Response(
     *         response=200,
     *         description="Промокод успешно выдан",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="promo_code", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Нет доступных промокодов",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="status", type="string"),
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
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