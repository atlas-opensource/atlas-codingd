<?php

# Define namespace for model
namespace common\models;

# Use other useful namespaces
use Yii;
use Abraham\TwitterOAuth\TwitterOAuth;
use common\models\TwitterApi;

/**
 * This is the model class for table "handle".
 *
 * @property int $id
 * @property int $user_id
 * @property string $handle
 * @property string $name
 * @property int $verified
 * @property string $user_since
 * @property string $profile_image
 * @property string $label
 * @property string $last_update
 *
 * @property User $user
 * @property Tweet[] $tweets
 */
class Handle extends \yii\db\ActiveRecord
{
  /**
    * {@inheritdoc}
    */
    public static function tableName()
    {
        return 'handle';
    }

  /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return
        [
            [['user_id', 'handle'], 'required'],
            [['user_id', 'verified'], 'integer'],
            [['user_since', 'last_update'], 'safe'],
            [['handle', 'name', 'profile_image'], 'string', 'max' => 255],
            [['label'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

  /**
    * {@inheritdoc}
    */
    public function attributeLabels()
    {
        return
        [
            'id' => 'ID',
            'user_id' => 'User ID',
            'handle' => 'Handle',
            'name' => 'Name',
            'verified' => 'Verified',
            'user_since' => 'User Since',
            'profile_image' => 'Profile Image',
            'label' => 'Label',
            'last_update' => 'Last Update',
            'numTweets' => 'Number of Tweets',
        ];
    }

    /**
    * This accepts an array of twitter api tweet objects
    * and saves them for this user
    *
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @param array $tweetsArray This is an array of twitter api Tweet Objects
    * @return boolean (false on error) (true on success)
    */
    public function storeHandleTweets($tweetsArray)
    {
        # Define return container
        $returnVal = array();

        # Set return val to true
        $returnVal['status'] = true;

        # Turn on debugging
        $debug = false;

        # Sanity
        if (is_array($tweetsArray) && count($tweetsArray))
        {
            # Debug
            if ($debug)
                echo date('Y-m-d H:i:s').":: STORING HANDLE TWEETS, SSANITY CHECK PASSED\n";

            # Offset by 1
            $lastNdx = count($tweetsArray) - 1;

            # Debug
            if ($debug)
                echo date('Y-m-d H:i:s').":: LAST INDEX CALCULATED AT: $lastNdx\n";

            for ($i = $lastNdx; $i >= 0; $i--)
            {
                # Get tweet from array of tweets
                $tweetObj = $tweetsArray[$i];

                # Check for existing tweet id
                // $existingTweet = Tweet::find()->where(["tweet_id" => $tweetObj->id])->count();

                $existingTweet = false;

                # Debug
                if ($debug)
                    echo date('Y-m-d H:i:s').":: EXISTING TWEET QUERY RESULT: $existingTweet\n";

                # Check for this tweet
                # We have this tweet already (THIS USE CASE NEEDS TO BE RE-EXAMINED)
                // if (empty($tweet) || !is_object($tweet) || empty($tweet->id))
                if (!$existingTweet)
                {
                    # Debug
                    if ($debug)
                        echo date('Y-m-d H:i:s').":: TWEET DOES NOT EXIST IN OUR DB\n";

                    # Instantiate tweet object
                    $tweet = new \common\models\Tweet();

                    # Set data
                    $tweet->handle_id = (integer)$this->id;
                    $tweet->date = date("Y-m-d H:i:s", strtotime($tweetObj->created_at));
                    $tweet->tweet_id = (integer)$tweetObj->id;
                    $tweet->tweet_text = (isset($tweetObj->full_text) && !empty($tweetObj->full_text)) ? $tweetObj->full_text : "";
                    $tweet->retweet_text = (isset($tweetObj->retweeted_status) && !empty($tweetObj->retweeted_status->full_text)) ? $tweetObj->retweeted_status->full_text : "";
                    $tweet->app = $tweetObj->source;
                    $tweet->retweets = (integer)$tweetObj->retweet_count;
                    $tweet->favorites = (integer)$tweetObj->favorite_count;

                    # These two fields are actually properties of the twitter user but the thinking here is that they are subject to change as the user tweets and we want to know the location and how many followers the user had when they tweeted. Maybe this should be moved to the user object
                    $tweet->followers = (isset($tweetObj->user) && isset($tweetObj->user->followers_count)) ? (integer)$tweetObj->user->followers_count : 0;
                    $tweet->location = (isset($tweetObj->user) && isset($tweetObj->user->location)) ? $tweetObj->user->location : "";

                    # Save
                    if ($tweet->validate())
                    {
                        if (!$tweet->save())
                        {
                            # Debug
                            if ($debug)
                                print_r($tweet->getErrors());

                            $returnVal['status'] = false;
                            $returnVal['messages'][] = $tweet->getErrors();
                        }
                    }
                    else {
                        $returnVal['status'] = false;
                        $returnVal['messages'][] = $tweet->getErrors();
                    }
                }
            }
        }

        # Return true on success
        return $returnVal;
    }

    /**
    * This is a function which pulls information
    * about twitter handles from the Twitter API
    *
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @param string $handle This is a twitter handle to get information about
    * @return object (false on error) an array of information about the twitter user
    */
    public function getHandleInfo()
    {
        # Query twitter to get other information
        $twitter = new TwitterOAuth(\Yii::$app->params['twitter_consumer_key'], \Yii::$app->params['twitter_consumer_secret'], \Yii::$app->params['twitter_oauth_token'], \Yii::$app->params['twitter_token_secret']);

        # Default to false
        $handleInfo = false;

        # Sanity check
        if (is_object($twitter))
        {
            # Set host
            $twitter->host = "https://api.twitter.com/1.1/";

            # Get information about handle
            $handleInfo = $twitter->get("users/show", ["screen_name" => $this->handle]);

            # Sanity
			if (isset($handleInfo) && !empty($handleInfo) && is_object($handleInfo))
			{
            	$this->name = (isset($handleInfo->name)) ? $handleInfo->name : "";
				if (isset($handleInfo->verified))
				{
            		$this->verified = ($handleInfo->verified) ? 1 : 0;
				}
				else
				{
            		$this->verified = 0;
				}
            	$this->user_since = (isset($handleInfo->created_at)) ? date("Y-m-d H:i:s", strtotime($handleInfo->created_at)) : date("Y-m-d H:i:s", strtotime("now"));
            	$this->profile_image = (isset($handleInfo->profile_image_url)) ? $handleInfo->profile_image_url : "";
			}
        }

        # Return
        return $handleInfo;
    }

    /**
    * This is a function which pulls all the
    * tweets since the last stored tweet
    * for this twitter handle from the Twitter API
    *
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @return boolean (false on error) (true on success)
    */
    public function getAndStoreNewTweets()
    {
        # Pull the last tweet we have for this twitter handle
        $lastLocalTweet = $this->getLastStoredTweet();

        # Request all the tweets after this one from the twitter API
        $tweetsArray = $this->pullTweets($lastLocalTweet->tweet_id);

        # Store the tweets
        $saveTweetsResponse = $this->storeHandleTweets($tweetsArray);

        # Return without error
        return true;
    }

    /**
    * This function gets tweets for this handle from the twitter api
    * 
    * If the user does not specify any arguments:
    *   Function will return all tweets made by this content creator which were created after the most recent one that we have.
    *   If we dont have any it will return as many as it can (maxes out at 3200 until we get a license) 

    * If the user specifies an afterId:
    *   Function will return all the tweets made by this content creator created after the ID specified

    * If the user specifies a beforeId:
    *   Function will return all the tweets made by this content creater created before the ID specified
    *   This works with afterId as well

    * If the user specifies a count:
    *   Function will (only) the specified number of tweets. 
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @return object (false on error) an array of tweets about the twitter user
    */
    public function pullTweets($afterId = NULL, $beforeId = NULL, $count = NULL, $iterator = 0)
    {
        # Set debug toggle
        $debug = false;

        # Define the paramters
        $params = 
        [
            'count' => 200,
            'screen_name' => $this->handle,
            'tweet_mode' => 'extended',
            'trim_user' => 1,
            'exclude_replies' => 1,
            'include_rts' => 1,
        ];

        # Only set after ID if it is greater than 0
        $afterId = (integer)$afterId;
        if ($afterId > 0)
        {
            $params['since_id'] = $afterId;
        }

        # If beforeId was NULL, we want to get all the tweets from afterId, going forward in time, up to the most recent one published by the content creator
        # Otherwise we want to get all the tweets starting with afterId, going forward in time, up until the ID specified in beforeId parameter
        $beforeId = (integer)$beforeId;
        if ($beforeId > 0)
        {
            $params['max_id'] = $beforeId;
        }

        # Instantiate a value to keep track of the number of tweets we have collected in total
        $numTweets = 0;

        # Sanitize the value passed by the user
        $count = (integer)$count;

        # Set a flag to indicate that the user specified a count
        $userCountFlag = (!empty($count)) ? true : false;

        # If the user specified a count between 0 and 200 use it 
        # If they specified a count over 200 we want to only query for 200 at a time but will loop until we get up to their specified count
        $params['count'] = ($count > 0 && $count <= 200) ? $count : 200;

        # Debug
        if ($debug)
        {
            echo "userCountFlag IS SET TO: ";
            echo json_encode($userCountFlag)."\n";
            echo "pullTweet() SET count PARAMETER TO: {$params['count']}\n";
        }

        # Instantiate array to contain all tweets
        $allTweets = array();

        # Instantiate twitter api access class
        $twitter = new TwitterApi();

        # Query for the specified number (if less than 200) or the first 200 tweets in the stack (starting with the newest)
        $tweetsArray = $twitter->query("get", "statuses/user_timeline", $params);

        # Loop until there are no more tweets or until we reach the user specified maximum
        while (is_array($tweetsArray) && count($tweetsArray) > 0 && $numTweets < $count)
        {
            # Get the number of requests remaining
            $reqAvail = $twitter->restApi->getLastXHeaders();
            $reqAvail = (integer)$reqAvail['x_rate_limit_remaining'];

            # Check the status of the query
            if ($reqAvail <= 5)
            {
                # Return with error
                return false;
            }

            # Calculate how many tweets we have pulled so far so we only get the number requested by the user
            $numTweets += count($tweetsArray);

            # The user specified a specific number of tweets that we want. 
            if ($userCountFlag)
            {
                # Calculate how many tweets between the number that we have and the number the user specified
                $countDiff = $count - $numTweets;

                # We would normally get another 200 so cut it down to the diff only if diff is less than 200
                if ($countDiff < 200)
                {
                    # Set the difference as the number of tweets to request
                    $params['count'] = $countDiff;
                }

                # We don't touch the actual count since the user specified it.
                $count = $count;
            }
                        
            # If the user did not specify a count, we should increment count along with numTweets (this will keep the condition from failing).
            # Otherwise we don't touch it and condition will fail when we have reached the number of tweets the user specified.
            else
            {
                $count = $numTweets;
            }

            # Pluck the ID of the oldest tweet that we got last time and decrement it by one
            # This will serve as the range highest tweet that we want returned. 
            # $afterId is the lowest tweet that we want returned.
            $params['max_id'] = $tweetsArray[count($tweetsArray) - 1]->id - 1;

            # Append the tweets to the stack of all tweets
            $allTweets = array_merge($allTweets, $tweetsArray);

            # Debug
            if ($debug)
            {
                echo "INSIDE pullTweets WHILE LOOP\n";
                echo "NUM TWEETS IS SET TO: $numTweets\n";
                echo "COUNT IS SET TO: $count\n";
            }

            # Query for the next two hundred tweets (between afterId and maxId) in the stack
            $tweetsArray = $twitter->query("get", "statuses/user_timeline", $params);
        }

        # Return the selected tweets
        return $allTweets;
    }


    /**
    * This function will collect and store the tweets 
    * specified by the arguments passed
    * 
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @return boolean true on success false on error
    */
    public function getAndStoreTweetsAfter($afterId)
    {
        # Get the first 200 tweet starting with the most recent
        $newTweets = $this->pullTweets($afterId, false, 200);

        # Check for a good batch
        if ($newTweets !== false)
        {
            # Loop until there is no more tweets
            while (is_array($newTweets) && !empty($newTweets) && count($newTweets) > 0)
            {
                # Get the ID -1 of the oldest tweet in this batch
                $beforeId = $newTweets[count($newTweets) - 1]->id - 1;

                # Store the new tweets
                $this->storeHandleTweets($newTweets);

                # Get the next 200 tweets
                $newTweets = $this->pullTweets($afterId, $beforeId, 200);
            }

            # Return (TODO: add error checking and return false on error)
            return true;
        }

        # Return error
        return false;
    }


    /**
    * This function will collect and store the tweets 
    * that we are missing for this handle
    *
    * 1. Try to fill in any missing tweets (between our stored after_id and our before_id)
    * 2. Get new tweets
    * 
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @return boolean true on success false on error or interruption (rate limit)
    */
    public function updateTweets()
    {          
        # Debug conditional
        $debug = false;

        # Define interrupt boolean (default to false)
        $interrupt = false; 

        # Put a condition here which only runs this block if this is existing user that got interrupted.
        if ($this->before_id !== NULL)
        {
            # Debug
            if ($debug)
            {
                echo "INSIDE EXISTING USER WITH UNFINISHED COLLECTION CONDITION (FIRST CONDITION OF updateTweets()\n";
            }

            # Get our first batch of tweets (first value will be null until we get all availble tweets at least once)
            $tweets = $this->pullTweets($this->after_id, $this->before_id, 200);

            # Count how many tweets we have (use this when we rewrite this function)
            $numTweets = (is_array($tweets)) ? count($tweets) : 0;

            # Check for a good batch
            if ($tweets !== false)
            {
                # Loop until there is no more tweets
                while (!empty($tweets) && is_array($tweets) && count($tweets) > 0)
                {
                    # Get oldest tweet from this stack
                    $oldestTweet = $tweets[count($tweets) - 1];
                    
                    # Get the ID -1 of the oldest tweet in this batch - 1
                    $beforeId = $oldestTweet->id - 1;

                    # Store the new tweets
                    $this->storeHandleTweets($tweets);
                    
                    # Get the next 200 tweets
                    $tweets = $this->pullTweets($this->after_id, $beforeId, 200);

                    # If we could not get all of the tweets requested (we need to stop)
                    if ($tweets === false)
                    {
                        # Save the before id
                        $this->before_id = $beforeId;

                        # Save the handle
                        $this->save();

                        # Return an error
                        return false;
                    }
                    # We got through all the tweets starting with our initial before_id (max_id) (this could also indicate a rete limit error because sometimes it just sends back empty arrays).
                    else if (is_array($tweets) && count($tweets) == 0)
                    {
                        # Set the after_id (since_id)
                        $this->after_id = (is_a($this->getLastStoredTweet(), "common\models\Tweet")) ? $this->getLastStoredTweet()->tweet_id : $this->after_id;

                        # Set the before_id (max_id) to null
                        $this->before_id = NULL;

                        # Save the handle
                        $this->save();

                        # Continue to top of loop where condition will fail
                    }
                }
            }
            # Did not get all the tweets we requested (we probably ran into a rate limit) need to try again
            else
            {                
                # Save handle object
                $this->save();

                # We did not do any updates here
                return false;
            }
        }

        # Debug
        if ($debug)
        {
            echo "GOT PAST FIRST CONDITION OF updateTweets()\n";
            echo "ABOUT TO QUERY FOR FIRST SET OF TWEETS\n";
            echo "after_id IS SET TO: {$this->after_id}\n";
            echo "before_id IS SET TO: {$this->before_id}\n";
        }

        # Now that we have all the older tweets get the new ones
        $newTweets = $this->pullTweets($this->after_id, $this->before_id, 200);

        # Check for tweets: we got some
        if ($newTweets !== false && (is_array($newTweets) && count($newTweets) > 0))
        {
            # Debug
            if ($debug)
            {
                echo "INSIDE: PULL TWEETS HAS RETURNED AN ARRAY (DID NOT RETURN FALSE)\n";
                echo "newTweets LOOKS LIKE THIS: \n";
                print_r($newTweets);
            }

            # Loop until there is no more new tweets (will return early on error)
            while (is_array($newTweets) && !empty($newTweets) && count($newTweets) > 0)
            {
                # Debug
                if ($debug)
                {
                    echo "INSIDE WHILE LOOP\n";
                    echo "WE RETRIEVED: ".count($newTweets)." TWEETS\n";
                }

                # Get oldest tweet from this stack
                $beforeId = $newTweets[count($newTweets) - 1]->id - 1;

                if ($debug)
                {
                    echo "CALCULATED before_id AS: $beforeId\n";
                }
                
                # Store the new tweets
                $this->storeHandleTweets($newTweets);

                # Get the next 200 tweets
                $newTweets = $this->pullTweets($this->after_id, $beforeId, 200);
                
                # We got interrupted
                if ($newTweets === false)
                {
                    # Set before ID (max_id)
                    $this->before_id = $beforeId;

                    # Save handle
                    $this->save();

                    # Set interrupt flag
                    $interrupt = true;

                    # Return an error
                    return false;
                }
            }

            # If we did not get interrupted
            if (!$interrupt)
            {
                # Set the after_id (or there was no tweets at all-- so leave it NULL) (this is the key line, maybe roll back other changes...but its just sanity checking....)
                $this->after_id = (is_a($this->getLastStoredTweet(), "common\models\Tweet")) ? $this->getLastStoredTweet()->tweet_id : NULL;

                # Unset the before ID (signals we want the newest on remote stack)
                $this->before_id = NULL;

                # Update the last updated stamp on handle
                $this->last_update = date("Y-m-d H:i:s");

                # Save handle
                $this->save();
            
                # Return (TODO: add error checking and return false on error)
                return true; 
            }
        }

        # Return error
        return false;
    }

    /**
    * This function returns the most recent tweet
    * stored locally for this twitter handle
    *
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @return object (false on error) an array of tweets about the twitter user
    */
    public function getLastStoredTweet()
    {
        # Get and return the most recent handle id
        $lastTweet = \common\models\Tweet::find()->where(['handle_id' => $this->id])->orderBy("tweet_id DESC")->one();

        # Check if we got a tweet
        if (!empty($lastTweet) && is_object($lastTweet) && is_a($lastTweet, "common\models\Tweet"))
        {
            # Return the tweet
            return $lastTweet;
        }

        # We couldn't find the tweet
        return false;
    }

    /**
    * This function returns the oldest tweet
    * stored locally for this twitter handle
    *
    * @author  Ben Shirani <ben.shirani@gmail.com>
    *
    * @since 1.0
    *
    * @return object (false on error) an array of tweets about the twitter user
    */
    public function getOldestStoredTweet()
    {
        # Get the most recent handle id
        $tweet = \common\models\Tweet::find()->where(['handle_id' => $this->id])->orderBy("tweet_id ASC")->one();

        # Sanity check tweet
        if (!empty($tweet) && is_object($tweet) && is_a($tweet, "common\models\Tweet"))
        {
            # Return the tweet
            return $tweet;
        }

        # Return an error
        return false;
    }

    /**
    * @return \yii\db\ActiveQuery
    */
     public function getUser()
     {
         return $this->hasOne(User::className(), ['id' => 'user_id']);
     }

     /**
      * @return \yii\db\ActiveQuery
      */
     public function getTweets()
     {
         return $this->hasMany(Tweet::className(), ['handle_id' => 'id']);
     }

     /**
       * {@inheritdoc}
       */
     public function getNumTweets()
     {
         return $this->getTweets()->count();
     }

     /**
       * {@inheritdoc}
       */
     public function getNum_Tweets()
     {
         return $this->getTweets()->count();
     }
}
