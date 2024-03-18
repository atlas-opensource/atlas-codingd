<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\Category;
use common\models\CategoryOption;
use common\models\CategorySubOption;


class CategorysuboptionController extends \yii\web\Controller
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

    public function actionIndex($category_option_id)
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
            $categorySubOptionDataProvider = new \yii\data\ActiveDataProvider(
            [
                'query' => CategorySubOption::find()->where(['category_option_id' => $category_option_id]),
                'pagination' =>
                [
                    'pageSize' => 20,
                 ],
            ]);

            # Instantiate Category Option Object
            $categoryOption = CategoryOption::find()->where(['id' => $category_option_id])->one();
            //print_r($categoryOption);exit();

            # Instantiate category sub option for creating new sub options
            $categorySubOption = new CategorySubOption();

            # Register Javascript
            \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/CategorySubOptionIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
            \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

            # Build breadcrumbs
            $this->view->params['breadcrumbs'] = array();
            $this->view->params['breadcrumbs'][] = ['url' => ['project/index'], 'label' => 'Projects'];
            $this->view->params['breadcrumbs'][] = ['url' => ['handle/index', 'project_id' => $project_id], 'label' => $project->name];
            $this->view->params['breadcrumbs'][] = ['url' => ['category/index'], 'label' => 'Categories'];
            $this->view->params['breadcrumbs'][] = ['url' => ['categoryoption/index', 'categoryId' => $categoryOption->category_id], 'label' => $categoryOption->getCategory()->one()->name];
            $this->view->params['breadcrumbs'][] = ['label' => $categoryOption->name];

            # Send data to view
            $this->view->params['categoryOption'] = $categoryOption;
            $this->view->params['categorySubOption'] = $categorySubOption;
            $this->view->params['categorySubOptionDataProvider'] = $categorySubOptionDataProvider;

            # Display page
            return $this->render('index');
          }
        }
    }

    public function actionAdd()
    {
        # Sanity
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate form model
            $categorySubOption = new CategorySubOption();

            # Load data and validate
            if ($categorySubOption->load(\Yii::$app->request->post()))
            {
                # Save model
                if ($categorySubOption->validate() && $categorySubOption->save())
                {
                    # Return data to page
                    return [
                            'data' => [
                                    'success' => true,
                                    'model' => $categorySubOption,
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
                    'model' => $categorySubOption,
                    'message' => $categorySubOption->getErrors(),
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
            $categorySubOption = \common\models\CategorySubOption::find()->where(['id' => $id])->one();

            # Set handle model property to new value
            $categorySubOption->$attr = $value;

            # Save model
            if (!empty($categorySubOption) && is_object($categorySubOption) && !empty($categorySubOption->id) && $categorySubOption->save())
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
                        'message' => $categorySubOption->getErrors(),
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
            $categorySubOptionId = (integer)$_REQUEST['id'];

            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate model object
            $categorySubOption = CategorySubOption::find()->where(['id' => $categorySubOptionId])->one();

            # Save model
            if (!empty($categorySubOption) && is_object($categorySubOption) && !empty($categorySubOption->id) && $categorySubOption->delete())
            {
                return
                [
                    'data' =>
                    [
                        'success' => true,
                        'message' => 'Category Sub Option has been deleted.',
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
                        'message' => $categorySubOption->getErrors(),
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
