<?php

namespace frontend\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\Handle;

class AdmincodingController extends \yii\web\Controller
{
    /**
     * Function handles access rules. Currently allows
     * only authenticated users to access any handle ops
     *
     * @author  Ben Shirani <ben.shirani@gmail.com>
     *
     * @since 1.0
     *
     * @return array Array of access rules.
     */
    public function behaviors()
    {
        return
        [
            'access' =>
            [
                'class' => AccessControl::className(),
                'only' => ['index'],
                'rules' =>
                [
                    [
                        'allow' => false,
                        'actions' => ['index'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex()
    {
        # Get an array of user objects
        return $this->render('index');
    }

}
