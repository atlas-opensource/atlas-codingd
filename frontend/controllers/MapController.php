<?php

namespace frontend\controllers;

use Yii;
use \yii\web\View;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;

class MapController extends \yii\web\Controller
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
        # Register CSS File for map
        \Yii::$app->getView()->registerCssFile(\Yii::$app->request->BaseUrl . '/css/map.css', ['position' => VIEW::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::className()]]);

        # Register javascript for google maps
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/MapIndex.js', ['position' => VIEW::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::className()]]);
        // \Yii::$app->getView()->registerJsFile('https://maps.googleapis.com/maps/api/js?key=randomstring&callback=initMap', ['position' => VIEW::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);
        \Yii::$app->getView()->registerJsFile('https://maps.googleapis.com/maps/api/js?key=randomstring=initialize', ['position' => VIEW::POS_END, 'depends' => [\yii\web\JqueryAsset::className()]]);


        return $this->render('index');
    }

}
