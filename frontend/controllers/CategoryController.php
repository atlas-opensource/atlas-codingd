<?php

# Define namespace
namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\Category;
use frontend\models\CategoryForm;
use common\models\Handle;


class CategoryController extends \yii\web\Controller
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
				$category = new Category();

				# Load data and validate
				if ($category->load(\Yii::$app->request->post()))
				{
	        # Assign to current user
					$category->user_id = \Yii::$app->user->id;

					# Save model
					if ($category->validate() && $category->save())
					{
						# Return data to page
						return
						[
								'data' =>
								[
									'success' => true,
									'model' => $category,
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
							'model' => $category,
							'message' => 'Category was not saved.',
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
			$categoryId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$category = Category::find()->where(['id' => $categoryId])->one();

			# Save model
			if (!empty($category) && is_object($category) && !empty($category->id) && $category->delete())
			{
				return
				[
					'data' =>
					[
						'success' => true,
						'message' => 'Category has been deleted.',
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
						'message' => $category->getErrors(),
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

		public function actionIndex()
    {
				# Get session
				$session = Yii::$app->session;

				# Check for active session
				if ($session->isActive)
				{
					# Get project id
					$project_id = $session->get("project_id");

					# Sanity check project id
					if (isset($project_id) && $project_id != null && is_int($project_id) && $project_id >= 0)
					{
						# Get project object
						$project = \common\models\Project::find()->where(['id' => $project_id])->one();

						# Instantiate data provider
						$categoryDataProvider = new \yii\data\ActiveDataProvider(
						[
							// 'query' => Category::find()->where(['user_id' => \Yii::$app->user->id]),
							'query' => Category::find()->where(["project_id" => $project_id]),
							'pagination' =>
							[
								'pageSize' => 20,
							],
						]);

						# Instantiate Category Object
		        $category = new Category();

		        # Instantiate form to collect information
		        $categoryForm = new CategoryForm();

		        # Register Javascript
		        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/CategoryIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
		        \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

						# Build breadcrumbs
						$this->view->params['breadcrumbs'] = array();
						$this->view->params['breadcrumbs'][] = ['url' => ['project/index'], 'label' => 'Projects'];
						$this->view->params['breadcrumbs'][] = ['url' => ['handle/index', 'project_id' => $project_id], 'label' => $project->name];
						$this->view->params['breadcrumbs'][] = ['label' => "Categories"];

		        # Send data to view
						$this->view->params['project_id'] = $project_id;
		        $this->view->params['category'] = $category;
		        $this->view->params['categoryForm'] = $categoryForm;
		        $this->view->params['categoryDataProvider'] = $categoryDataProvider;

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
            $category = \common\models\Category::find()->where(['id' => $id])->one();

            # Set handle model property to new value
            $category->$attr = $value;

            # Save model
            if (!empty($category) && is_object($category) && !empty($category->id) && $category->save())
            {
                # Return error
                return
                [
                    'data' =>
                    [
                        'success' => true,
                        'message' => 'Category was saved.',
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
                        'message' => 'Category was not saved.',
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
