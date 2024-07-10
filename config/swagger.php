<?php

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="My Yii2 API",
 *     version="1.0.0"
 * )
 */

header('Content-Type: application/json');
echo \OpenApi\Generator::scan(['@app/controllers'])->toJson();
