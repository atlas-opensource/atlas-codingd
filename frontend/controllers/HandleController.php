<?php

namespace frontend\controllers;

use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\Handle;
use common\models\HandleSearch;
use common\models\Project;
use common\models\XrefProjectHandle;
use Yii;


class HandleController extends \yii\web\Controller
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
                'only' => ['index', 'update', 'add', 'delete', 'getandstorenewtweets'],
                'rules' =>
				[
                    [
                        'allow' => false,
                        'actions' => ['index', 'update', 'add', 'delete', 'getandstorenewtweets'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'add', 'delete', 'getandstorenewtweets'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

	/**
	 * Index of all handles per each user
	 *
	 *
	 * @author  Ben Shirani <ben.shirani@gmail.com>
	 *
	 * @since 1.0
	 *
	 * @return  Array of access rules.
	 */
    public function actionIndex($project_id)
    {
			# Get session
			$session = Yii::$app->session;

			# Check if sessino is open
			if (!$session->isActive)
			{
				# Open Yaf_Session
				$session->open();
			}

			# Get project id
			$project_id = (integer)$project_id;

			# Sanity check: project_id is good
			if (isset($project_id) && is_int($project_id) && $project_id >= 0)
			{
				# Store project ID in session for global use
				$session->set("project_id", $project_id);

				# Get the project object
				$project = Project::find()->where(['id' => $project_id])->one();

				# Sanity: make sure we have a project
				if (!empty($project) && is_object($project) && !empty($project->id))
				{
					# Instantiate search model (empty object)
					$handleSearch = new HandleSearch();

					# Get data provider
					$handleDataProvider = $handleSearch->search($project_id, \Yii::$app->request->queryParams);

					# Build breadcrumbs
					$this->view->params['breadcrumbs'] = array();
					$this->view->params['breadcrumbs'][] = ['url' => ['project/index'], 'label' => 'Projects'];
					$this->view->params['breadcrumbs'][] = ['label' => $project->name];

					# Register Javascript
					\Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/HandleIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
					\Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

					# Set variables to be passed to the view
					//$this->view->params['handleModel'] = $handleModel;
					$this->view->params['project'] = $project;
					$this->view->params['handleSearch'] = $handleSearch;
					$this->view->params['handleDataProvider'] = $handleDataProvider;
					$this->view->params['addHandleFormModel'] = new \frontend\models\AddHandleForm();

					# Return
					return $this->render('index');
				}

				# User got here by mistake (probably typed the URL in) set message
				\Yii::$app->session->setFlash('error', "Unfortunately I was unable to retrieve the tweets. Please alert the administrator!");

				# Send them back to the index
				return $this->redirect(URL::toRoute(['project/index']));
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
	        //$handleModel = new \app\models\Handle($id);
			$handleModel = \common\models\Handle::find()->where(['id' => $id])->one();

			# Set handle model property to new value
			$handleModel->$attr = $value;

			# Save model
			if (!empty($handleModel) && is_object($handleModel) && !empty($handleModel->id) && $handleModel->save())
			{
				# Return error
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Handle was saved.',
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
						'message' => 'Handle was not saved.',
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

	public function actionAdd()
	{
		# Sanity
		if (\Yii::$app->request->isAjax)
		{
			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate form model
			$formModel = new \frontend\models\AddHandleForm();

			# Load data and validate
			if ($formModel->load(\Yii::$app->request->post()) && $formModel->validate())
			{
				# Get project Id from request payload
				$project_id = (integer)\Yii::$app->request->post("XrefProjectHandle")['project_id'];

				# Instantiate project object
				$project = \common\models\Project::find()->where(['id' => $project_id])->one();

				# Sanity check project
				if (!empty($project) && is_object($project) && !empty($project->id) && is_int($project->id))
				{
					# Add handle to project
					$result = $project->addHandleFromForm($formModel);
					print_r
					([
						"inside HandleController::actionAdd() sanity check and incoming data validation check and project sanity check.",
						$result
					]);

					# Return result
					return $result;
				}
			}

			# Return error
			return
			[
				'data' =>
				[
					'success' => false,
					'model' => $formModel,
					'message' => [$handleModel, $handleModel->getErrors()],
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
			$handleId = (integer)$_REQUEST['id'];
			$project_id = (integer)$_REQUEST['project_id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$handleModel = \common\models\Handle::find()->where(['id' => $handleId])->one();

			# Get project handle pivot object
			$xrefProjectHandle = XrefProjectHandle::find()->where(['handle_id' => $handleModel->id, 'project_id' => $project_id])->one();

			# Delete from pivot
			if (!empty($xrefProjectHandle) && is_object($xrefProjectHandle) && !empty($xrefProjectHandle) && $xrefProjectHandle->delete())
			{
				// # Delete model (this probably should not happen here but instead a new view should serve as a master list of all handles where they can be deleted)
				// if (!empty($handleModel) && is_object($handleModel) && !empty($handleModel->id) && $handleModel->delete())
				// {

					return
					[
						'data' =>
						[
							'success' => true,
							'message' => 'Handle has been saved.',
						],
						'code' => 0,
					];
				// }
				// else
				// {
				// 	# Return error
				// 	return
				// 	[
				// 		'data' =>
				// 		[
				// 			'success' => false,
				// 			'message' => 'Handle was not saved.',
				// 		],
				// 		'code' => -1,
				// 	];
				// }
			}
		}

		# User got here by mistake (probably typed the URL in) set message
		\Yii::$app->session->setFlash('error', "Unfortunately I was unable to complete your last request.");

		# Send them back to the index
		return $this->redirect(URL::to('index'));
	}

	public function actionGetandstorenewtweets($handleId)
	{
		# Sanity
		if (\Yii::$app->request->isAjax)
		{
			# Sanitize
			$handleId = (integer)$handleId;

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$handle = \common\models\Handle::find()->where(['id' => $handleId])->one();

			# Save model
			if (!empty($handle) && is_object($handle) && !empty($handle))
			{
				# Update tweets
				$status = $handle->updateTweets();
				
				return
				[
					'data' =>
					[
						'success' => $status,
						'message' => 'Tried to update. Check \'success\'.',
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
						'message' => 'Could not download or store new tweets.',
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
