<?php

namespace frontend\controllers;

use yii\filters\AccessControl;
use common\models\Group;


class GroupController extends \yii\web\Controller
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
                'only' => ['index', 'update', 'add', 'delete'],
                'rules' =>
                [
                    [
                        'allow' => false,
                        'actions' => ['index', 'update', 'add', 'delete'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'add', 'delete'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * Index of all groups per each user
     *
     *
     * @author  Ben Shirani <ben.shirani@gmail.com>
     *
     * @since 1.0
     *
     * @return  Array of access rules.
     */
    public function actionIndex()
    {
        # Instantiate data provider
        $groupDataProvider = new \yii\data\ActiveDataProvider(
        [
            'query' => \common\models\Group::find()->where(['admin_id' => \Yii::$app->user->id]),
            'pagination' =>
            [
                'pageSize' => 20,
             ],
        ]);

        # Instantiate an empty group
        $group = new Group();

        # Register Javascript
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/GroupIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);


        # Set variables to be passed to the view
        $this->view->params['groupDataProvider'] = $groupDataProvider;
        $this->view->params['group'] = $group;

        # Return
        return $this->render('index');
    }

    public function actionAdd()
    {
        # Sanity Check
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate form model
            $group = new Group();

            # Load data and validate
            if ($group->load(\Yii::$app->request->post()))
            {
                # Assign to current user
                $group->admin_id = \Yii::$app->user->id;

                # Save model
                if ($group->validate() && $group->save())
                {
                    # Return data to page
                    return [
                            'data' => [
                                    'success' => true,
                                    'model' => $group,
                                    'message' => 'Category has been saved.',
                            ],
                            'code' => 0,
                    ];
                }
            }

            # Return error
            return
            [
                'data' =>
                [
                    'success' => false,
                    'model' => $group,
                    'message' => $group->getErrors(),
                ],
                'code' => -1,
            ];
        }

        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to complete your last request.");

        # Send them back to the index
        return $this->redirect(URL::to('index'));
    }

    public function actionDelete()
    {
        # Sanity
		if (\Yii::$app->request->isAjax)
		{
			# Sanitize
			$groupId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$group = \common\models\Group::find()->where(['id' => $groupId])->one();

			# Save model
			if (!empty($group) && is_object($group) && !empty($group->id) && $group->delete())
			{
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Group has been deleted.',
					],
					'code' => 0,
				];
			}
			else
			{
				# Return error
				return
				[
					'data' =>
					[
						'success' => false,
						'message' => 'Group was not not saved.',
					],
					'code' => -1,
				];
			}
		}

		# User got here by mistake (probably typed the URL in) set message
		\Yii::$app->session->setFlash('error', "Unfortunately I was unable to complete your last request.");

		# Send them back to the index
		return $this->redirect(URL::to('index'));
    }

    public function actionUpdate()
    {
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Extract and sanitize request variables
            $id = base64_decode($_REQUEST['pk']);
            $id = explode(":", $id);
            $id = (int)$id[1];

            # Extract and sanitize name of property to update
            # TODO: Update this to the YII way
            $attr = trim($_REQUEST['name']);
            $attr = (string)$attr;

            # Extract and sanitize the value of the property to update
            # TODO: Update this to the YII way
            $value = trim($_REQUEST['value']);
            $value = (string)$value;

            # Instantiate model object to update
            $group = \common\models\Group::find()->where(['id' => $id])->one();

            # Set handle model property to new value
            $group->$attr = $value;

            # Save model
            if (!empty($group) && is_object($group) && !empty($group->id) && $group->save())
            {
                # Return error
                return
                [
                    'data' =>
                    [
                        'success' => true,
                        'message' => 'Group was saved.',
                    ],
                    'code' => 0,
                ];
            }
            else
            {
                # Return error
                return
                [
                    'data' =>
                    [
                        'success' => false,
                        'message' => 'Group was not saved.',
                    ],
                    'code' => -1,
                ];
            }
        }

        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to complete your last request.");

        # Send them back to the index
        return $this->redirect(URL::to('index'));
        //return $this->render('update');
    }
}
