<?php

namespace tests\_support;

use Codeception\Module;
use Yii;
use yii\web\Request;
use yii\web\Response;

class UnitHelper extends Module
{
    /**
     * Mock a Yii request with the given body parameters.
     *
     * @param array $bodyParams
     */
    public function mockRequest(array $bodyParams)
    {
        $request = new Request();
        $request->setBodyParams($bodyParams);
        Yii::$app->set('request', $request);
    }

    /**
     * Mock a Yii response.
     *
     * @return Response
     */
    public function mockResponse()
    {
        $response = new Response();
        Yii::$app->set('response', $response);
        return $response;
    }

    /**
     * Mock user identity for authentication.
     *
     * @param \app\models\User $user
     */
    public function loginUser($user)
    {
        Yii::$app->user->setIdentity($user);
    }

    /**
     * Reset the request component to its original state.
     */
    public function resetRequest()
    {
        Yii::$app->set('request', \Yii::createObject(\Yii::$app->params['request']));
    }

    /**
     * Reset the response component to its original state.
     */
    public function resetResponse()
    {
        Yii::$app->set('response', \Yii::createObject(\Yii::$app->params['response']));
    }

    /**
     * Reset the user component to its original state.
     */
    public function resetUser()
    {
        Yii::$app->set('user', \Yii::createObject(\Yii::$app->params['user']));
    }
}
