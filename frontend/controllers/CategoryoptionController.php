<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\Category;
use common\models\CategoryOption;


class CategoryoptionController extends \yii\web\Controller
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

    public function actionAdd()
    {
        # Sanity
		if (\Yii::$app->request->isAjax)
		{
			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate form model
			$categoryOption = new CategoryOption();

			# Load data and validate
			if ($categoryOption->load(\Yii::$app->request->post()))
			{
				# Save model
				if ($categoryOption->validate() && $categoryOption->save())
				{
					# Return data to page
					return [
							'data' => [
									'success' => true,
									'model' => $categoryOption,
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
					'model' => $categoryOption,
					'message' => $categoryOption->getErrors(),
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
			$categoryOptionId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$categoryOption = CategoryOption::find()->where(['id' => $categoryOptionId])->one();

			# Save model
			if (!empty($categoryOption) && is_object($categoryOption) && !empty($categoryOption->id) && $categoryOption->delete())
			{
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Category Option has been deleted.',
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
						'message' => $categoryOption->getErrors(),
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

    public function actionIndex($categoryId)
    {
      # Get session
      $session = Yii::$app->session;

      # Sanity check session
      if ($session->isActive)
      {
        # Get the project id from the session
        $project_id = $session->get("project_id");

        # Sanity check project
        if (isset($project_id) && is_int($project_id) && $project_id >= 0)
        {
          # Get project object
          $project = \common\models\Project::find()->where(['id' => $project_id])->one();

            # Instantiate data provider
            $categoryOptionDataProvider = new \yii\data\ActiveDataProvider(
            [
                'query' => CategoryOption::find()->where(['category_id' => $categoryId]),
                'pagination' =>
                [
                    'pageSize' => 20,
                 ],
            ]);

            # Instantiate Category Object
            $category = Category::find()->where(['id' => $categoryId])->one();
            $categoryOption = new CategoryOption();
            $categoryOptions = CategoryOption::find()->where(['category_id' => $categoryId])->all();

            # Register Javascript
            \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/CategoryOptionIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
            \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

            # Build breadcrumbs
            $this->view->params['breadcrumbs'] = array();
            $this->view->params['breadcrumbs'][] = ['url' => ['project/index'], 'label' => 'Projects'];
            $this->view->params['breadcrumbs'][] = ['url' => ['handle/index', 'project_id' => $project_id], 'label' => $project->name];
            $this->view->params['breadcrumbs'][] = ['url' => ['category/index'], 'label' => 'Categories'];
            $this->view->params['breadcrumbs'][] = ['label' => $category->name];

            # Send data to view
            $this->view->params['category'] = $category;
            $this->view->params['categoryOption'] = $categoryOption;
            $this->view->params['categoryOptions'] = $categoryOptions;
            $this->view->params['categoryOptionDataProvider'] = $categoryOptionDataProvider;

            # Display page
            return $this->render('index');
          }
        }

        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to retrieve the webpage. Please alert the administrator!");

        # Send them back to the index
        return $this->redirect(URL::toRoute(['project/index']));
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
            $categoryOption = \common\models\CategoryOption::find()->where(['id' => $id])->one();

            # Set handle model property to new value
            $categoryOption->$attr = $value;

            # Save model
            if (!empty($categoryOption) && is_object($categoryOption) && !empty($categoryOption->id) && $categoryOption->save())
            {
                # Return error
                return
                [
                    'data' =>
                    [
                        'success' => true,
                        'message' => 'Category Option was saved.',
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
                        'message' => $categoryOption->getErrors(),
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
