<?php

namespace app\components;

use Yii;
use yii\base\ActionFilter;

class AccessControl extends ActionFilter
{
    public function beforeAction($action)
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->url);
            Yii::$app->response->redirect(['user/login'])->send();
            return false;
        }
        return true;
    }
}
