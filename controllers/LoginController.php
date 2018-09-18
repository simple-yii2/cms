<?php

namespace cms\controllers;

use yii\web\Controller;

class LoginController extends Controller
{

    /**
     * @inheritdoc
     */
    public $layout = 'login';

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'index' => 'cms\user\common\actions\Login',
        ];
    }

}
