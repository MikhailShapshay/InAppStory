<?php

namespace app\controllers;

use Throwable;
use yii\base\Exception;
use yii\db\StaleObjectException;
use yii\filters\AccessControl;
use app\models\LoginForm;
use Faker\Factory;
use Yii;
use app\models\User;
use yii\base\Security;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\web\Response;

class UserController extends Controller
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
     * Действие для генерации пользователей
     *
     * @return string|Response
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionGenerateUsers(): string|Response
    {
        $faker = Factory::create('en_EN');

        $security = new Security();

        $iterations = 2; // Количество итераций для генерации
        $batchSize = 25; // Количество записей за итерацию

        for ($i = 0; $i < $iterations; $i++) {
            $users = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $users[] = [
                    $faker->unique()->userName(),
                    $security->generateRandomString(),
                    Yii::$app->security->generatePasswordHash('user123'),
                    $faker->email,
                    time(),
                    time(),
                ];
            }
            Yii::$app->db->createCommand()->batchInsert(
                'user',
                ['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'],
                $users
            )->execute();
            unset($users);
        }

        Yii::$app->session->setFlash('success', 'Пользователи успешно сгенерированы!');

        return $this->redirect(['index']);
    }

    /**
     * Действие для отображения списка всех пользователей
     *
     * @return string|Response
     */
    public function actionIndex(): string|Response
    {
        $dataProvider = new ActiveDataProvider([
            'query' => User::find(),
            'pagination' => [
                'pageSize' => 25, // Количество строк на странице
            ],
        ]);

        return $this->render('index', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Действие для просмотра деталей пользователя
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     */
    public function actionView($id): string|Response
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Действие для создания нового пользователя
     *
     * @return string|Response
     * @throws Exception
     * @throws \yii\db\Exception
     */
    public function actionCreate(): string|Response
    {
        $model = new User(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->generateAuthKey(); // Генерируем auth_key перед сохранением
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'Пользователь успешно создан.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Действие для обновления существующего пользователя
     *
     * @param $id
     * @return string|Response
     * @throws NotFoundHttpException
     * @throws \yii\db\Exception
     */
    public function actionUpdate($id): string|Response
    {
        $model = $this->findModel($id);
        $model->scenario = 'update'; // Установка сценария

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Действие для удаления пользователя
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
     * Найти модель пользователя по ID
     *
     * @param $id
     * @return User|null
     * @throws NotFoundHttpException
     */
    protected function findModel($id): ?User
    {
        if (($model = User::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('Модель пользователя не найдена.');
    }

    /**
     * Действие авторизации пользователя
     *
     * @return string|Response
     */
    public function actionLogin(): string|Response
    {
        $model = new LoginForm();

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Действие выхода из сессии пользователя
     *
     * @return string|Response
     */
    public function actionLogout(): string|Response
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
