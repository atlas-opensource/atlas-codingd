<?php


namespace frontend\controllers;

use \yii\web\View;
use yii\filters\AccessControl;
use yii\helpers\Url;


class TimelineController extends \yii\web\Controller
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
        # Register CSS
        \Yii::$app->getView()->registerCssFile(\Yii::$app->request->BaseUrl . '/timeline3/css/timeline.css', ['depends' => [\yii\web\JqueryAsset::className()]]);

        # Register JS
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/timeline3/js/timeline-min.js', ['position' => View::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::className()]]);

        # Render view
        return $this->render('index');
    }

}
