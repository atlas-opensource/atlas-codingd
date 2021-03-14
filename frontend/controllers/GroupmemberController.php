<?php

namespace frontend\controllers;


use yii\filters\AccessControl;
use common\models\GroupMember;
use common\models\Group;
use common\models\User;
use yii\helpers\Url;



class GroupmemberController extends \yii\web\Controller
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
                'only' => ['index', 'add', 'delete', 'getemailjson'],
                'rules' =>
                [
                    [
                        'allow' => false,
                        'actions' => ['index', 'add', 'delete', 'getCategoryJson', 'getemailjson'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'add', 'delete', 'getCategoryJson', 'getemailjson'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionAdd()
    {
        # Sanity
		if (\Yii::$app->request->isAjax)
		{
            //print_r($_REQUEST);
            //exit();
			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate form model
			$groupMember = new GroupMember();

            # Load user from form data
            $user = \common\models\User::find()->where(['id' => (integer)\Yii::$app->request->post('User')['id']])->one();

            // if ($user->load(\Yii::$app->request->post()))
            if (!empty($user) && is_object($user) && !empty($user->id))
            {
                # Load data and validate
    			if ($groupMember->load(\Yii::$app->request->post()))
    			{
                    # Assign user id (group id should have been assigned above)
                    $groupMember->user_id = $user->id;

    				# Save model
    				if ($groupMember->validate() && $groupMember->save())
    				{
    					# Return data to page
    					return [
    							'data' => [
    									'success' => true,
    									'model' => $groupMember,
    									'message' => 'Category has been saved.',
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
					'model' => $groupMember,
					'message' => $groupMember->getErrors(),
                    'debug' => [\Yii::$app->request->post(), $user]
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
        if (\Yii::$app->request->isAjax)
		{
			# Sanitize
			$groupMemberId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$groupMember = GroupMember::find()->where(['id' => $groupMemberId])->one();

			# Save model
			if (!empty($groupMember) && is_object($groupMember) && !empty($groupMember->id) && $groupMember->delete())
			{
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Group member has been deleted.',
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
						'message' => $groupMember->getErrors(),
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

    public function actionIndex($group_id)
    {
        # Sanitze paramaters
        $group_id = (integer)$group_id;

        # Get group object
        $group = \common\models\Group::find()->where(['id' => $group_id])->one();

        # Instantiate data provider
        $groupMemberDataProvider = new \yii\data\ActiveDataProvider(
        [
            'query' => GroupMember::find()->where(['group_id' => $group_id]),
            'pagination' =>
            [
                'pageSize' => 20,
             ],
        ]);

        # Instantiate empty objects for form
        $user = new User();

        # Build breadcrumbs
        $this->view->params['breadcrumbs'] = array();
        $this->view->params['breadcrumbs'][] = ['url' => Url::toRoute(['group/index']), 'label' => $group->name];


        # Send data to view
        $this->view->params['group'] = $group;
        $this->view->params['user'] = $user;
        $this->view->params['groupMemberDataProvider'] = $groupMemberDataProvider;

        # Register Javascript
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/GroupmemberIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

        # Render the page
        return $this->render('index');
    }

    public function actionGetemailjson()
    {
        # Make sure this is an ajax request
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate array of users
            $users = User::find()->all();

            if (!empty($users) && is_array($users) && count($users))
            {
                # Loop through users
                foreach ($users as $user)
                {
                    # Append array of category id and name's for select
                    $userOptions[] = array('id' => $user->id, 'email' => $user->email);
                }

                if (count($userOptions))
                {
                    # Return success
                    return
                    [
                        [
                            'success' => true,
                            'message' => 'Select options returned.',
                            'payload' => $userOptions,
                            'code' => 0,
                        ],
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
                'message' => 'Unable to return select options',
            ],
            'code' => -1,
        ];
    }
}
