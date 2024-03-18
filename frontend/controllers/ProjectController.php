<?php

namespace frontend\controllers;

use \common\models\Project;
use \common\models\ProjectSearch;
use yii\helpers\Url;
use yii\filters\AccessControl;
use Yii;

class ProjectController extends \yii\web\Controller
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

    public function actionIndex()
    {
        # Get session
        $session = Yii::$app->session;

        # Unset selected proejct since we got here:
        if ($session->isActive)
        {
          $session->remove("project_id");
        }

        # Instantiate search model for gridview
        $projectSearch = new \common\models\ProjectSearch();

        # Get dataprovider from search model
        $projectDataProvider = $projectSearch->search(\Yii::$app->request->queryParams);

        # Instantiate empty object for adding projects
        $project = new Project();

        # Register Javascript
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/ProjectIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

        # Set variables to be passed to the view
        //$this->view->params['handleModel'] = $handleModel;
        $this->view->params['project'] = $project;
        $this->view->params['projectSearch'] = $projectSearch;
        $this->view->params['projectDataProvider'] = $projectDataProvider;

        # Return
        return $this->render('index');
    }

    public function actionAdd()
    {
        # Sanity
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate form model
            $project = new Project();

            # Load data and validate
            if ($project->load(\Yii::$app->request->post()))
            {
                # Assign to current user
                $project->user_id = \Yii::$app->user->id;

                # Save model
                if ($project->validate() && $project->save())
                {
                    # Return data to page
                    return [
                            'data' => [
                                    'success' => true,
                                    'model' => $project,
                                    'message' => 'Project has been saved.',
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
                    'model' => $project,
                    'message' => 'Project was not saved.',
                ],
                'code' => -1,
            ];
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
            $project = \common\models\Project::find()->where(['id' => $id])->one();

            # Set handle model property to new value
            $project->$attr = $value;

            # Save model
            if (!empty($project) && is_object($project) && !empty($project->id) && $project->save())
            {
                # Return error
                return
                [
                    'data' =>
                    [
                        'success' => true,
                        'message' => 'Project was saved.',
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
                        'message' => 'Project was not saved.',
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

    public function actionDelete()
    {
        if (\Yii::$app->request->isAjax)
		{
			# Sanitize
			$projectId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$project = Project::find()->where(['id' => $projectId])->one();

			# Save model
			if (!empty($project) && is_object($project) && !empty($project->id) && $project->delete())
			{
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Project has been deleted.',
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
						'message' => $project->getErrors(),
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
