<?php

namespace app\controllers;

use yii\filters\AccessControl;
use app\models\LoginForm;
use Faker\Factory;
use Yii;
use app\models\User;
use yii\base\Security;
use yii\data\ActiveDataProvider;
use yii\web\Controller;
use yii\web\NotFoundHttpException;

class UserController extends Controller
{
    public function behaviors()
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

    public function actionGenerateUsers()
    {
        $faker = Factory::create('ru_RU');

        $security = new Security();

        // Определим количество итераций для генерации
        $iterations = 2;
        $batchSize = 25;

        for ($i = 0; $i < $iterations; $i++) {
            $users = [];
            for ($j = 0; $j < $batchSize; $j++) {
                $users[] = [
                    $faker->name,
                    $security->generateRandomString(),
                    $security->generatePasswordHash('user'),
                    $faker->email,
                    time(),
                    time(),
                ];
            }
            Yii::$app->db->createCommand()->batchInsert('user', ['username', 'auth_key', 'password_hash', 'email', 'created_at', 'updated_at'], $users)->execute();
            unset($users);
        }

        Yii::$app->session->setFlash('success', 'Users data generation is complete!');

        return $this->redirect(['index']);
    }

    public function actionIndex()
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

	public function actionView($id)
	{
		return $this->render('view', [
			'model' => $this->findModel($id),
		]);
	}

    public function actionCreate()
    {
        $model = new User(['scenario' => 'create']);

        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $model->generateAuthKey(); // Генерируем auth_key перед сохранением
            if ($model->save()) {
                Yii::$app->session->setFlash('success', 'User has been created successfully.');
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    public function actionUpdate($id)
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

	public function actionDelete($id)
	{
		$this->findModel($id)->delete();

		return $this->redirect(['index']);
	}

	protected function findModel($id)
	{
		if (($model = User::findOne($id)) !== null) {
			return $model;
		}

		throw new NotFoundHttpException('The requested page does not exist.');
	}

    public function actionLogin()
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

    public function actionLogout()
    {
        Yii::$app->user->logout();
        return $this->goHome();
    }
}
