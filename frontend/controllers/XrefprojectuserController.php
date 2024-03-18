<?php

namespace frontend\controllers;

use yii\helpers\Url;
use yii\filters\AccessControl;
use \common\models\User;
use \common\models\Project;
use \common\models\XrefProjectUser;


class XrefprojectuserController extends \yii\web\Controller
{
    /*
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
               'only' => ['index', 'add', 'delete'],
               'rules' =>
               [
                   [
                       'allow' => false,
                       'actions' => ['index', 'add', 'delete'],
                       'roles' => ['?'],
                   ],
                   [
                       'allow' => true,
                       'actions' => ['index', 'add', 'delete'],
                       'roles' => ['@'],
                   ],
               ],
           ],
       ];
   }

    public function actionIndex($project_id)
    {
        # Sanitize projectid
        $project_id = (integer)$project_id;

        # Instantiate data provider
        $xrefProjectUserDataProvider = new \yii\data\ActiveDataProvider(
        [
			'query' => XrefProjectUser::find()->where(['project_id' => $project_id]),
        	'pagination' =>
        	[
        		'pageSize' => 20,
        	 ],
        ]);

        # Instantiate Project Object
        $project = Project::find()->where(['id' => $project_id])->one();
        $user = new User();
        $xrefProjectUser = new XrefProjectUser();

        # Register Javascript
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/XrefProjectUserIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

        # Build breadcrumbs
        $this->view->params['breadcrumbs'] = array();
        $this->view->params['breadcrumbs'][] = ['url' => ['project/index'], 'label' => 'Projects'];

        # Send data to view
        $this->view->params['user'] = $user;
        $this->view->params['project'] = $project;
        $this->view->params['xrefProjectUser'] = $xrefProjectUser;
        $this->view->params['xrefProjectUserDataProvider'] = $xrefProjectUserDataProvider;

        # Display page
        return $this->render('index');
    }

    public function actionAdd()
    {
        # Sanity
		if (\Yii::$app->request->isAjax)
		{
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate form model
            $user = new \common\models\MnemosyneUser();
			$xrefProjectUser = new XrefProjectUser();

			# Load data and validate
			if ($xrefProjectUser->load(\Yii::$app->request->post()))
			{
                # User
                $user = User::find()->where(['email' => \Yii::$app->request->post('User')['email']])->one();

                $xrefProjectUser->user_id = $user->id;

                # Save model
                if ($xrefProjectUser->validate() && $xrefProjectUser->save())
                {
                    # Return data to page
                    return
                    [
                        'data' => [
                            'success' => true,
                            'model' => $xrefProjectUser,
                            'message' => 'User has been saved.',
                        ],
                        'code' => 0,
                    ];
                }
            }
		}

		# Return error
		return
		[
			'data' =>
			[
				'success' => false,
				'model' => $xrefProjectUser,
				'message' => $xrefProjectUser->getErrors(),
			],
			'code' => -1,
		];
        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to complete your last request.");

        # Send them back to the index
        return $this->redirect(URL::to('index'));
	}


    public function actionDelete()
    {
        if (\Yii::$app->request->isAjax)
		{
			# Sanitize
			$xrefProjectUserId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$xrefProjectUser = XrefProjectUser::find()->where(['id' => $xrefProjectUserId])->one();
 
			# Save model
			if (!empty($xrefProjectUser) && is_object($xrefProjectUser) && !empty($xrefProjectUser->id) && $xrefProjectUser->delete())
			{
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Project user has been deleted.',
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
						'message' => $xrefProjectUser->getErrors(),
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


}
