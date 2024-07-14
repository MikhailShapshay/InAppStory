<?php

namespace app\components;

use Yii;
use yii\base\ActionFilter;
use yii\base\InvalidRouteException;

class AccessControl extends ActionFilter
{
    /**
     * @param $action
     * @return bool
     * @throws InvalidRouteException
     */
    public function beforeAction($action): bool
    {
        if (Yii::$app->user->isGuest) {
            Yii::$app->user->setReturnUrl(Yii::$app->request->url);
            Yii::$app->response->redirect(['user/login'])->send();
            return false;
        }
        return true;
    }
}
