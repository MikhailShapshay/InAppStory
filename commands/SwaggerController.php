<?php

namespace app\commands;

use Yii;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use OpenApi\Generator;

class SwaggerController extends Controller
{
    public function actionGo()
    {
        $openApi = Generator::scan([Yii::getAlias('@app/controllers/api')]);
        $file = Yii::getAlias('@app/web/api-doc/swagger.yaml');
        if (file_put_contents($file, $openApi->toYaml()) !== false) {
            echo $this->ansiFormat("Created: {$file}\n", Console::FG_BLUE);
            return ExitCode::OK;
        } else {
            echo $this->ansiFormat("Failed to create: {$file}\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }
}