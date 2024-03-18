<?php

namespace console\controllers;

use Yii;
use yii\console\Controller;
use yii\helpers\Url;
use common\models\Handle;
use common\models\Tweet;
use common\models\Project;
use common\models\XrefProjectHandle;
use common\models\TwitterApi;


class MnemosynedController extends Controller
{
    public function actionIndex()
    {
        echo "cron service runnning\n";
    }

    public function actionGetTweets($userId, $force = 0)
    {
        # Turn debug output on
        $debug = false;

        # Find out 1 hour into the past
        $updateThreshold = date('Y-m-d H:i:s', strtotime('-3 hour'));
        
        # Get all the handles older than the threshold or that have never been updated
        $handles = ($force) ? \common\models\Handle::find()->where(['user_id' => $userId])->all() : \common\models\Handle::find()->where(['user_id' => $userId])->andWhere(
        [
            'or',
            ['last_update' => NULL],
            ['<', 'last_update', $updateThreshold],
        ])->orderBy('last_update ASC')->all();

        if ($debug)
        {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');

            echo "UPDATE THRESHOLD: ".$updateThreshold."\n\n";
            // echo "GETTING HANDLES:\n\n";
            // print_r($handles);
        }
        
        if (!empty($handles) && is_array($handles) && count($handles) > 0)
        {
            # Debug
            if ($debug)
                echo "INSIDE HANDLES IS ARRAY CONDITION\n";

            # Loop through handles
            foreach ($handles as $ndx => $handle)
            {
                # Debug
                if ($debug)
                {
                    echo "INSIDE HANDLES FOREACH LOOP\n";
                }
                    

                if (!empty($handle) && is_object($handle) && is_a($handle, "common\models\Handle"))
                {
                    # Debug
                    if ($debug)
                    {
                        echo "INSIDE HANDLE IS OBJECT CONDITION.\n";
                        echo "TRYING TO PROCESS HANDLE: $handle->id - $handle->handle\n";
                    }
                    
                    # Update tweets and return status
                    $status = $handle->updateTweets();     
                    
                    # Debug 
                    if ($debug)
                    {
                        echo "COMPLETED UPDATE OF HANDLE: $handle->id - $handle->handle\n";
                        echo "STATUS RETURNED:\n";
                        print_r(json_encode($status));
                        echo "\n\n";
                    }
                }
            }
        }        
    }

    public function actionGetTweetsByHandle($handle, $force = 0)
    {
        # Turn debug output on
        $debug = false;

        # Find out 1 hour into the past
        $updateThreshold = date('Y-m-d H:i:s', strtotime('-1 hour'));
        
        # Get all the handles older than the threshold or that have never been updated
        $handleObj = ($force) ? \common\models\Handle::find()->where(['handle' => $handle])->one() : \common\models\Handle::find()->where(['handle' => $handle])->andWhere(
        [
            'or',
            ['last_update' => NULL],
            ['<', 'last_update', $updateThreshold],
        ])->one();

        if ($debug)
        {
            error_reporting(E_ALL);
            ini_set('display_errors', '1');

            echo "UPDATE THRESHOLD: ".$updateThreshold."\n\n";
            echo "GETTING HANDLE:\n\n";
            print_r($handleObj);
        }
                                
        if (!empty($handleObj) && is_object($handleObj) && is_a($handleObj, "common\models\Handle"))
        {
            # Debug
            if ($debug)
            {
                echo "INSIDE HANDLE IS OBJECT CONDITION.\n";
                echo "TRYING TO PROCESS HANDLE: $handleObj->id - $handleObj->handle\n";
            }
            
            # Update tweets and return status
            $status = $handleObj->updateTweets();     
            
            # Debug 
            if ($debug)
            {
                echo "COMPLETED UPDATE OF HANDLE: $handleObj->id - $handleObj->handle\n";
                echo "STATUS RETURNED:\n";
                print_r(json_encode($status));
                echo "\n\n";
            }
        }
               
    }


    /**
     * Prints out specified number of tweets from the handle given
     *
     * @param string handleString - Text representing twitter handle
     * @param integer numTweets - How many tweets to retrieve and print
    **/
    public function actionTweets($handleString, $afterId = 0, $beforeId = 0, $numTweets = 0)
    {
        # Debug toggle
        $debug = false;

        # Instantiate handle and set screen_name
        $handle = new Handle();
        $handle->handle = $handleString;

        # Set count to zero if not specified
        $numTweets = (!empty($numTweets)) ? $numTweets : 0;

        # Debug
        if ($debug)
        {
            echo "afterId IS SET TO: $afterId\n";
            echo "beforeId IS SET TO: $beforeId\n";
            echo "numTweets IS SET TO: $numTweets\n";
        }

        # Get Tweets
        $tweets = $handle->pullTweets($afterId, $beforeId, $numTweets);

        # Print tweets to standard output
        print_r($tweets);
    }

    /**
     * Prints out header information from twitter rest api response (includes rate limit info)
     *
    **/
    public function actionGetTwitterApiStatus()
    {
        # Instantiate handle and set screen_name
        $handle = new Handle();
        $handle->handle = '@UN';

        # Set count to 1
        $numTweets = 1;

        # Define the paramters
        $params = 
        [
            'count' => $numTweets,
            'screen_name' => $handle->handle,
            'tweet_mode' => 'extended',
            'trim_user' => 1,
            'exclude_replies' => 1,
        ];

        # Instantiate a twitter api object
        $twitter = new TwitterApi();

        # Make a test query
        $tweetsArray = $twitter->query("get", "statuses/user_timeline", $params);

        # Get information from last request
        $headers = $twitter->restApi->getLastXHeaders();
        $responseCode = $twitter->restApi->getLastHttpCode();
        $responseBody = $twitter->restApi->getLastBody();

        # Print tweets to standard output
        echo "LAST X HEADERS: \n\n";
        print_r($headers);
        echo "LAST RESPONSE CODE: $responseCode\n\n";
        echo "LAST RESPONSE BODY: \n";
        print_r($responseBody);
        echo "\n\n";
    }

    /**
     * This is debug code
     *
     * @param string handleString - Text representing twitter handle
     * @param integer numTweets - How many tweets to retrieve and print
    **/
    public function actionDebug($handleString, $numTweets)
    {
        $handle = new Handle();
        $handle->handle = $handleString;
        $numTweets = (!empty($numTweets)) ? $numTweets : 10;
        $tweets = $handle->pullTweets(0, 0, $numTweets);
        print_r($tweets);
    }

    /* This is debug code
    *
    * @param string handleString - Text representing twitter handle
    * @param integer numTweets - How many tweets to retrieve and print
   **/
   public function actionDebug2()
   {
        $handle = \common\models\Handle::find()->where(['id' => 603])->one();
        $handle->getLastStoredTweet();
        //print_r($handle);
        //print_r($handle->getLastStoredTweet());
        exit();
   }

    /**
     * @inheritdoc
     *
    **/
    public function actionSetPass($userEmail, $password)
    {
        # Sanitize
        $userEmail = (string)$userEmail;
        $password = (string)$password;

        # Get user object
        $user = \common\models\User::find()->where(['email' => $userEmail])->one();

        # Set password for user
        $user->setPassword($password);

        # Save user
        $user->save();

        # Return randomly
        return true;
    }

    public function actionImport($filePath, $projectId)
    {
        # Debug
        echo "Finding project...\n";

        # Instantiate project object
        $project = \common\models\Project::find()->where(['id' => $projectId])->one();

        # Sanity check project
        if (!empty($project) && is_object($project) && !empty($project->id) && is_int($project->id))
        {
          # Debug output
          echo "Project Found!\n";          
        
          # Debug
          echo "Opening handles file...\n";

          # Open file handle
          $fh = fopen($filePath,"r");

          # Debug
          echo "File opened!\n";

          # Loop through file returning each line
          while ($line = fgetcsv($fh))
          {
              # Get handle string 
              $handleString = (string)$line[0];

              # Debug output
              echo "Attempting to add handle: ".$handleString."...\n";

              # Get label
              $labelString = "Imported by mnemosyned";

              # Add handle to project
              $result = $project->addHandle($handleString, $labelString);

              # Display result in useful format
              if ($result['data']['success'] === true)
              {
                echo "Successfully added handle: ".$result['data']['model']->handle.". System said: \n";
                echo $result['data']['message']."\n\n";
              }
              else
              {
                echo "Something went wrong when adding handle: $handleString. Error message returned was: \n";
                echo $result['data']['message'].".\n\n";
              }            
          }
       }

        # Close file handle
        fclose($fh);
    }
}
