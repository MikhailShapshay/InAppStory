<?php

namespace app\components;

use Yii;
use yii\filters\auth\AuthMethod;

class ApiKeyAuth extends AuthMethod
{
	public $apiKeyParam = 'api-key';

	public function authenticate($user, $request, $response)
	{
		$apiKey = $request->getHeaders()->get($this->apiKeyParam);
		if ($apiKey === null) {
			return null;
		}

		$identity = $user->loginByAccessToken($apiKey, get_class($this));
		if ($identity === null) {
			$this->handleFailure($response);
		}
		return $identity;
	}
}
