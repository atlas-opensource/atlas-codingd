<?php

# Namespace declaration
namespace frontend\controllers;

# Other namespaces this file uses
use Yii;
use yii\filters\AccessControl;
use common\models\Tweet;
use common\models\TweetSearch;
use common\models\Category;
use common\models\CategoryOption;
use common\models\CategoryOptionValue;
use yii\helpers\Url;



class TweetController extends \yii\web\Controller
{
    public function behaviors()
    {
        return
        [
            'access' =>
            [
                'class' => AccessControl::className(),
                'only' => ['index','export'],
                'rules' =>
                [
                    [
                        'allow' => false,
                        'actions' => ['index','export'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                        'actions' => ['index','export'],
                        'roles' => ['@'],
                    ],
                ],
            ],
        ];
    }

    public function actionIndex($handleId)
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

            # Sanitize input
            $handleId = (integer)$handleId;

            # Instantiate handle
            $handle = \common\models\Handle::find()->where(['id' => $handleId])->one();

            # Sanity checking
            if (!empty($handle) && is_object($handle) && !empty($handle->id))
            {
                # Instantiate search model (empty object)
                $tweetSearch = new TweetSearch();

                # Get data provider for tweets
                $tweetDataProvider = $tweetSearch->search($handle->id, \Yii::$app->request->queryParams);

                # Get an array of user objects
                $users = \common\models\MnemosyneUser::find()->all();

                # Include javacript
                \Yii::$app->getView()->registerJsFile(\Yii::$app->request->BaseUrl . '/js/TweetIndex.js', ['depends' => [\yii\web\JqueryAsset::className()]]);

                # Build breadcrumbs
                $this->view->params['breadcrumbs'] = array();
                $this->view->params['breadcrumbs'][] = ['url' => ['project/index'], 'label' => 'Projects'];
                $this->view->params['breadcrumbs'][] = ['url' => ['handle/index', 'project_id' => $project_id], 'label' => $project->name];
                $this->view->params['breadcrumbs'][] = ['label' => $handle->handle];

                # Set variables to be passed to the view
                $this->view->params['tweetSearch'] = $tweetSearch;
                $this->view->params['users'] = $users;
                $this->view->params['handle'] = $handle;
                $this->view->params['tweetDataProvider'] = $tweetDataProvider;

                # Render the page
                return $this->render('index');
            }
          }
        }

        # User got here by mistake (probably typed the URL in) set message
        \Yii::$app->session->setFlash('error', "Unfortunately I was unable to retrieve the tweets. Please alert the administrator!");

        # Send them back to the index
    		return $this->redirect(URL::toRoute(['project/index']));
    }

    public function actionExport($handleId)
    {
        # Sanitize handleId
        $handleId = (integer)$handleId;

        # Instantiate handle
        $handle = \common\models\Handle::find()->where(['id' => $handleId])->one();

        # Sanity checking
        if (!empty($handle) && is_object($handle) && !empty($handle->id))
        {
            # Get an array of user objects
            $users = \common\models\MnemosyneUser::find()->all();

            # Get array of categories (TODO: Add project limitor here)
            $categories = Category::find()->all();

            # Sanity (TODO: replace with exception handling)
            if (!empty($users) && is_array($users) && count($users) && !empty($categories) && is_array($categories) && count($categories))
            {
                # Get all tweets for the specified handle (TODO: if not in separate function, needs modified for generic documents)
                $tweets = Tweet::find()->where(["handle_id" => $handle->id])->all();

                # Sanity Check: make sure we have tweets for specified handle
                if (is_array($tweets) && count($tweets))
                {
                    # Instantiate data container
                    $fields = array();

                    # Build header row
                    $headerRow = Tweet::getExportHeader();

                    # Append header row to output
                    $rows = implode($headerRow, ",");

                    # Add delimiter to header row
                    $rows .= "\n";

                    # Loop through tweets
                    foreach($tweets as $tweet)
                    {
                        # Reset row
                        $row = null;
                        unset($row);
                        $row = array();
                        $rowString = "";

                        # Get the data for this tweet
                        $fields = $tweet->getExportTweetData();

                        # Check for coding
                        if (!empty($fields) && is_array($fields) && count($fields))
                        {
                            # Loop through header to maintain column order
                            foreach ($headerRow as $headerColumn)
                            {
                                if (isset($fields[$headerColumn]) && !empty($fields[$headerColumn]))
                                {
                                    $row[] = $fields[$headerColumn];
                                }
                                else {
                                    $row[] = "No Value";
                                }
                            }

                            # Convert to string
                            $rowString = implode($row, ",");

                            # Add line delimiter
                            $rowString .= "\n";

                            # Add to all rows
                            $rows .= $rowString;
                        }
                    }

                    # Send
                    return \Yii::$app->response->sendContentAsFile($rows, 'sample.csv',
                    [
                        'mimeType' => 'application/csv',
                        'inline'   => false
                    ]);
                }
            }
        }
        else {
            # User got here by mistake (probably typed the URL in) set message
            \Yii::$app->session->setFlash('error', "Unfortunately I was unable to export the data. Please alert the administrator!");
        }

        # Send Headers and Data
    }
}
