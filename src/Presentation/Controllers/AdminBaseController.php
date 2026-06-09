<?php

namespace app\Presentation\Controllers;

use yii\filters\AccessControl;
use yii\web\Controller;

abstract class AdminBaseController extends Controller
{
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::class,
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function ($rule, $action) {
                            return !\Yii::$app->user->isGuest && \Yii::$app->user->identity->is_admin;
                        }
                    ],
                ],
            ],
        ];
    }
}
