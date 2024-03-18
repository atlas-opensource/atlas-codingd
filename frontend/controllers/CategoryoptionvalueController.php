<?php

namespace frontend\controllers;

use Yii;
use yii\web\Controller;
use yii\filters\AccessControl;
use yii\helpers\Url;
use common\models\Category;
use common\models\CategoryOption;
use common\models\CategorySubOption;
use common\models\CategorySubOptionValue;
use common\models\CategoryOptionValue;
use common\models\Tweet;
use \yii\web\View;


class CategoryoptionvalueController extends \yii\web\Controller
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
                'only' => ['index', 'update', 'add', 'delete', 'getcategoryjson', 'getcodejson', 'getsuboptionjson', 'addmultiple'],
                'rules' =>
                [
                    [
                        'allow' => false,
                        'actions' => ['index', 'update', 'add', 'delete', 'getcategoryjson', 'getcodejson', 'getsuboptionjson', 'addmultiple'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index', 'update', 'add', 'delete', 'getcategoryjson', 'getcodejson', 'getsuboptionjson', 'addmultiple'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function actionAdd()
    {
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Sanitize input
            $categoryOptionId = (integer)\Yii::$app->request->post('CategoryOption')['id'];

            # Instantiate form model
            $category = new Category();
            $categoryOption = CategoryOption::find()->where(['id' => $categoryOptionId])->one();
            $categoryOptionValue = new CategoryOptionValue();

            # Load data and validate
            if ($categoryOptionValue->load(\Yii::$app->request->post()))
            {
                # Assign data
                $categoryOptionValue->user_id = \Yii::$app->user->id;
                $categoryOptionValue->category_option_id = $categoryOption->id;

                # Save model
                if ($categoryOptionValue->validate() && $categoryOptionValue->save())
                {
                    # Instantiate return debgu containter
                    $returnObjects = array();

                    # Set return status
                    $returnStatus = true;
                    $returnObjects[] = $categoryOptionValue;

                    # Check to see if there are sub options for this category option
                    if (CategorySubOption::find()->where(['category_option_id' => $categoryOptionId])->count())
                    {
                        # Instantiate sub option value object
                        $categorySubOptionValue = new CategorySubOptionValue();

                        # Load and sanitize
                        if ($categorySubOptionValue->load(\Yii::$app->request->post()))
                        {
                            # Assign data to sub option value object
                            $categorySubOptionValue->user_id = \Yii::$app->user->id;
                            $categorySubOptionValue->category_option_value_id = $categoryOptionValue->id;

                            # Save category sub option value
                            if (!$categorySubOptionValue->validate() || !$categorySubOptionValue->save())
                            {
                                $returnStatus = false;
                            }

                            # Append to return objects container
                            $returnObjects[] = $categorySubOptionValue;
                        }
                    }

                    # Return data to page
                    return
                    [
                            'data' =>
                            [
                                    'success' => $returnStatus,
                                    'model' => $returnObjects,
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
                    'model' => array($categoryOption, $categorySubOptionValue),
                    'message' => array($_REQUEST, $category, $category->getErrors(), $categoryOption, $categoryOption->getErrors(), $categoryOptionValue, $categoryOptionValue->getErrors(), $categorySubOptionValue->getErrors()),
                ],
                'code' => -1,
            ];
        }

        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to complete your last request.");

        # Send them back to the index
        return $this->redirect(URL::to('index'));
    }

    /**
     * @inheritdoc
     */
    public function actionAddmultiple()
    {
        # Define the return status as success until otherwise
        $returnStatus = true;

        # Define success message
        $returnMessage = "Coding has been applied to the tweet.";

        # Setup container for models to return (TODO: this is really only helpful for debugging)
        $returnModels = array();

        # Debug
        $debug = array();

        # Check if this is an ajax request
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Check to make sure there is data
            if (!empty(\Yii::$app->request->post('category_ids')) && is_array(\Yii::$app->request->post('category_ids')))
            {
                # Loop through each of the supplied categories
                foreach (\Yii::$app->request->post('category_ids') as $categoryLabel => $categoryId)
                {
                    # Debug
                    $debug[] = "category id: ".$categoryId;

                    # Sanitize category id
                    $categoryId = (integer)$categoryId;

                    # Check to make sure the user selected an option for this category
                    if (!empty(\Yii::$app->request->post('category_options')[$categoryLabel]) && \Yii::$app->request->post('category_options')[$categoryLabel] != "notSelected")
                    {
                        # Get the selected option for this category
                        $categoryOptionId = (integer)\Yii::$app->request->post('category_options')[$categoryLabel];

                        # Get and clean the tweet id
                        $tweetId = (integer)\Yii::$app->request->post('tweet_id');

                        # Instantiate a category option value object
                        $categoryOptionValue = new CategoryOptionValue();

                        # Load data into category option value obj
                        $categoryOptionValue->user_id = \Yii::$app->user->id;
                        $categoryOptionValue->tweet_id = $tweetId;
                        $categoryOptionValue->category_option_id = $categoryOptionId;

                        # Try to validate and save the category option value
                        if ($categoryOptionValue->validate() && $categoryOptionValue->save())
                        {
                            # Check for sub options
                            if (!empty(\Yii::$app->request->post('category_suboptions') && is_array(\Yii::$app->request->post('category_suboptions')) && !empty(\Yii::$app->request->post('category_suboptions')[$categoryLabel])))
                            {
                                # Debug
                                $debug[] = "Sub options found";

                                # Sanitize category_sub_option
                                $categorySuboptionId = (integer)\Yii::$app->request->post('category_suboptions')[$categoryLabel];

                                # Instantiate sub option value object
                                $categorySubOptionValue = new CategorySubOptionValue();

                                # Load data
                                $categorySubOptionValue->user_id = \Yii::$app->user->id;
                                $categorySubOptionValue->tweet_id = $tweetId;
                                $categorySubOptionValue->category_sub_option_id = $categorySuboptionId;
                                $categorySubOptionValue->category_option_value_id = $categoryOptionValue->id;

                                # Try to save category sub option value object
                                if ($categorySubOptionValue->validate() && $categorySubOptionValue->save())
                                {
                                    # Debug
                                    $debug[] = "Suboption saved";

                                    # This was a success!
                                    $models[] = $categoryOptionValue;
                                    $models[] = $categorySubOptionValue;
                                }
                                else
                                {
                                    # Debug
                                    $debug[] = "Suboption save failed";

                                    # Set return status
                                    $returnStatus = false;

                                    # Define success message
                                    $returnMessage = "Coding has not been applied to the tweet. Please check below";

                                    # This was a success!
                                    $models[] = $categoryOptionValue;
                                    $models[] = $categorySubOptionValue;
                                }
                            }
                        }
                        else
                        {
                            # Debug
                            $debug[] = "Category option value failed to save.";

                            # Set return status
                            $returnStatus = false;

                            # Define success message
                            $returnMessage = "Coding has not been applied to the tweet. Please check below";

                            # This was a success!
                            $models[] = $categoryOptionValue;
                        }
                    }
                }
            }

            # Return
            return
            [
                'data' =>
                [
                    'success' => $returnStatus,
                    'model' => $returnModels,
                    'message' => $returnMessage,
                    'debug' => $debug,
                ],
            ];
        }
    }

    /**
     * @inheritdoc
     */
    public function actionDelete()
    {
        if (\Yii::$app->request->isAjax)
		{
			# Sanitize
			$categoryOptionValueId = (integer)$_REQUEST['id'];

			# Set response type
			\Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

			# Instantiate model object
			$categoryOptionValue = CategoryOptionValue::find()->where(['id' => $categoryOptionValueId])->one();

			# Save model
			if (!empty($categoryOptionValue) && is_object($categoryOptionValue) && !empty($categoryOptionValue->id) && $categoryOptionValue->delete())
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
						'message' => $categoryOptionValue->getErrors(),
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

    /**
     * @inheritdoc
     */
    public function actionIndex($tweet_id)
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

          # Sanitze paramaters
          $tweet_id = (integer)$tweet_id;

          # Get tweet object
          $tweet = \common\models\Tweet::find()->where(['id' => $tweet_id])->one();

          # Instantiate data provider
          $categoryOptionValueDataProvider = new \yii\data\ActiveDataProvider(
            [
              'query' => CategoryOptionValue::find()->where(['user_id' => \Yii::$app->user->id, 'tweet_id' => $tweet_id]),
              'pagination' =>
              [
                  'pageSize' => 20,
                ],
            ]);

            # Instantiate empty objects for form
            $category = new Category();
            $categoryOption = new CategoryOption();
            $categoryOptionValue = new CategoryOptionValue();
            $categorySubOptionValue = new CategorySubOptionValue();

            # Define container for coding options
            $codingOptions = array();

            # Pull all the categories
            $categories = \common\models\Category::find()->where(["project_id" => $project->id])->all();

            # Get all the options we can code for each category
            $categoryOptionArray = \common\models\CategoryOption::find()->all();
            foreach ($categoryOptionArray as $categoryOptionObj)
            {
              if (empty($categoryOptions[$categoryOptionObj->getCategory()->one()->id]['notSelected']))
              {
                  $categoryOptions[$categoryOptionObj->getCategory()->one()->id]['notSelected'] = "Chose Code";
              }
              $categoryOptions[$categoryOptionObj->getCategory()->one()->id][$categoryOptionObj->id] = $categoryOptionObj->code . " - " . $categoryOptionObj->name;
            }

            # Get all the sub options we can code for each option
            $categorySubOptionArray = \common\models\CategorySubOption::find()->all();
            foreach ($categorySubOptionArray as $categorySubOptionObj)
            {
              $categorySubOptions[$categorySubOptionObj->getCategoryOption()->one()->id][$categorySubOptionObj->code] = $categorySubOptionObj->code . " - " . $categorySubOptionObj->name;
            }

            # Build breadcrumbs
            $this->view->params['breadcrumbs'] = array();
            $this->view->params['breadcrumbs'][] = ['url' => ['projects/index'], 'label' => 'Projects'];
            $this->view->params['breadcrumbs'][] = ['url' => ['handle/index', 'project_id' => $project_id], 'label' => $project->name];
            $this->view->params['breadcrumbs'][] = ['url' => Url::toRoute(['tweet/index', 'handleId' => $tweet->getHandle()->one()->id]), 'label' => $tweet->getHandle()->one()->handle];
            $this->view->params['breadcrumbs'][] = ['label' => $tweet->tweet_id];

            # Send data to view
            $this->view->params['tweet'] = $tweet;
            $this->view->params['category'] = $category;
            $this->view->params['categories'] = $categories;
            $this->view->params['categoryOptions'] = $categoryOptions;
            $this->view->params['categorySubOptions'] = $categorySubOptions;
            $this->view->params['categoryOption'] = $categoryOption;
            $this->view->params['categorySubOptionValue'] = $categorySubOptionValue;
            $this->view->params['categoryOptionValue'] = $categoryOptionValue;
            $this->view->params['categoryOptionValueDataProvider'] = $categoryOptionValueDataProvider;

            # Register Javascript
            \Yii::$app->getView()->registerJsFile('https://platform.twitter.com/widgets.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
            \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/CategoryOptionValueIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);
            \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/Common.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

            # Include css
            \Yii::$app->getView()->registerCssFile(\Yii::$app->request->BaseUrl . '/css/CategoryOptionValueIndex.css', ['position' => VIEW::POS_HEAD, 'depends' => [\yii\web\JqueryAsset::className()]]);

            # Render the page
            return $this->render('index');
          }

        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to retrieve the webpage. Please alert the administrator!");

        # Send them back to the index
        return $this->redirect(URL::toRoute(['project/index']));  
      }
    }

    /**
     * @inheritdoc
     */
    public function actionGetcategoryjson()
    {
        # Make sure this is an ajax request
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate array of categories
            // $categories = Category::find()->where(['user_id' => \Yii::$app->user->id])->all();
            $categories = Category::find()->all();


            if (!empty($categories) && is_array($categories) && count($categories))
            {
                # Loop through categories
                foreach ($categories as $category)
                {
                    # Append array of category id and name's for select
                    $selectOptions[] = array('id' => $category->id, 'name' => $category->display_name);
                }

                if (count($selectOptions))
                {
                    # Return success
                    return
                    [
                        [
                            'success' => true,
                            'message' => 'Select options returned.',
                            'payload' => $selectOptions,
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

    /**
     * @inheritdoc
     */
    public function actionGetcodejson($categoryId)
    {
        # Debug
        $debug = array();

        # Make sure this is an ajax request
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate array of categories
            $categoryOptions = CategoryOption::find()->where(['category_id' => $categoryId])->all();

            if (!empty($categoryOptions) && is_array($categoryOptions) && count($categoryOptions))
            {
                # Debug
                $debug[] = "Category options found";

                # Loop through categories
                foreach ($categoryOptions as $categoryOption)
                {
                    # Append array of category id and name's for select
                    $selectOptions[] = array('id' => $categoryOption->id, 'name' => $categoryOption->code." - ".$categoryOption->name);
                }

                if (count($selectOptions))
                {
                    # Debug
                    $debug[] = "Category options added to return array";

                    # Return success
                    return
                    [
                        //'data' =>
                        [
                            'success' => true,
                            'message' => 'Select options returned.',
                            'payload' => $selectOptions,
                            'code' => 0,
                            'debug' => $debug,
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
            'debug' => $debug,
        ];
    }

    /**
     * @inheritdoc
     */
    public function actionGetsuboptionjson($category_option_id)
    {
        # Make sure this is an ajax request
        if (\Yii::$app->request->isAjax)
        {
            # Set response type
            \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;

            # Instantiate array of categories
            $categorySubOptions = CategorySubOption::find()->where(['category_option_id' => $category_option_id])->all();

            if (!empty($categorySubOptions) && is_array($categorySubOptions) && count($categorySubOptions))
            {
                # Loop through categories
                foreach ($categorySubOptions as $categorySubOption)
                {
                    # Append array of category id and name's for select
                    $selectOptions[] = array('id' => $categorySubOption->id, 'name' => $categorySubOption->code." - ".$categorySubOption->name);
                }

                if (count($selectOptions))
                {
                    # Return success
                    return
                    [
                        [
                            'success' => true,
                            'message' => 'Select options returned.',
                            'payload' => $selectOptions,
                            'code' => 0,
                        ],
                    ];
                }
            }
        }

        # Return error
        return
        [
            [
                'success' => false,
                'message' => 'Unable to return select options',
                'code' => -1,
            ],
        ];
    }
}
