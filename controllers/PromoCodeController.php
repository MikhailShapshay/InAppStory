<?php

namespace app\controllers;

use Throwable;
use yii\db\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use Faker\Factory;
use Yii;
use yii\web\Controller;
use yii\data\ActiveDataProvider;
use yii\web\NotFoundHttpException;
use app\models\PromoCode;
use yii\web\Response;

class PromoCodeController extends Controller
{
    /**
     * @return array[]
     */
    public function behaviors(): array
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'only' => ['index', 'view', 'create', 'update', 'delete'],
                'rules' => [
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['@'], // только авторизованные пользователи
                    ],
                ],
            ],
        ];
    }

    /**
     * Действие для генерации промокодов
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionGeneratePromoCodes(): string|Response
    {
        $faker = Factory::create();

        $iterations = 2; // Количество итераций для генерации
        $batchSize = 25; // Количество записей за итерацию

        for ($i = 0; $i < $iterations; $i++) {
            $promoCodes = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $promoCode = new PromoCode();
                $promoCode->code = $this->generateUniqueCode(6); // Генерация уникального кода
                $promoCodes[] = [$promoCode->code];
            }
            Yii::$app->db->createCommand()->batchInsert(PromoCode::tableName(), ['code'], $promoCodes)->execute();
        }

        Yii::$app->session->setFlash('success', 'Промокоды успешно сгенерированы!');

        return $this->redirect(['index']);
    }

    /**
     * Действие для отображения списка всех промокодов
     *
     * @return string|Response
     */
    public function actionIndex(): string|Response
    {
        $dataProvider = new ActiveDataProvider([
            'query' => PromoCode::find(),
            'pagination' => [
                'pageSize' => 25, // Количество строк на странице
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Действие для просмотра деталей промокода
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView($id): string|Response
    {
        $model = $this->findModel($id);
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Действие для создания нового промокода
     *
     * @return string|Response
     * @throws Exception
     */
    public function actionCreate(): string|Response
    {
        $model = new PromoCode();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Действие для обновления существующего промокода
     *
     * @param $id
     * @return string|Response
     * @throws Exception
     * @throws NotFoundHttpException
     */
    public function actionUpdate($id): Response|string
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Действие для удаления промокода
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws Throwable
     * @throws StaleObjectException
     */
    public function actionDelete($id): string|Response
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Найти модель промокода по ID
     *
     * @param $id
     * @return PromoCode|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?PromoCode
    {
        if (($model = PromoCode::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Промокод не найден.');
    }

    /**
     * Генерация уникального кода
     *
     * @param $length
     * @return string
     */
    private function generateUniqueCode($length): string
    {
        $faker = Factory::create();
        $unique = false;
        $code = '';

        while (!$unique) {
            $code = $faker->regexify('[A-Za-z0-9]{' . $length . '}');
            $existing = PromoCode::findOne(['code' => $code]);
            if (!$existing) {
                $unique = true;
            }
        }

        return $code;
    }
}
