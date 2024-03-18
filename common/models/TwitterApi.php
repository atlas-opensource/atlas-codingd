<?php

# Namespace
namespace common\models;

# Include namespaces
use Abraham\TwitterOAuth\TwitterOAuth;
use Yii;

/**
 * This is the model class for table "twitter_api".
 *
 * @property int $id
 * @property string $handle
 * @property string $consumer_key
 * @property string $consumer_secret
 * @property string $oauth_token
 * @property string $token_secret
 * @property string $persist_data
 * @property string $updated_at
 * @property string $created_at
 * @property int $active
 */
class TwitterApi extends \yii\db\ActiveRecord
{
    # This is local to the object and not persisted in the DB
    public $restApi;

    /**
     * {@inheritdoc}
     */
    public function __construct()
    {
        # Set all the required User Auth Values
        $this->consumer_key = \Yii::$app->params['twitter_consumer_key'];
        $this->consumer_secret = \Yii::$app->params['twitter_consumer_secret'];
        $this->oauth_token = \Yii::$app->params['twitter_oauth_token'];
        $this->token_secret = \Yii::$app->params['twitter_token_secret'];

        # Connect to the api
        $this->restApi = new TwitterOAuth($this->consumer_key, $this->consumer_secret, $this->oauth_token, $this->token_secret);
    }
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'twitter_api';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['handle', 'consumer_key', 'consumer_secret', 'oauth_token', 'token_secret', 'updated_at'], 'required'],
            [['persist_data'], 'string'],
            [['updated_at', 'created_at'], 'safe'],
            [['active'], 'integer'],
            [['handle', 'consumer_key', 'consumer_secret', 'oauth_token', 'token_secret'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'handle' => 'Handle',
            'consumer_key' => 'Consumer Key',
            'consumer_secret' => 'Consumer Secret',
            'oauth_token' => 'Oauth Token',
            'token_secret' => 'Token Secret',
            'persist_data' => 'Persist Data',
            'updated_at' => 'Updated At',
            'created_at' => 'Created At',
            'active' => 'Active',
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function checkLimit()
    {
    }

    /**
     * {@inheritdoc}
     */
    public function query($method, $endPoint, $params)
    {
        # Set method to get if not set
        $method = (empty($method)) ? "GET" : $method;

        # Convert method to lower case
        $method = strtolower($method);

        # Instantiate default result
        $result = false;

        # Switch on method
        switch ($method)
        {
            # Post
            case 'post':
                break;

            # Put
            case 'put':
                break;

            # Delete
            case 'delete':
                break;

            # Get
            case 'get':
            default:

                # Query against the api
                $result = $this->restApi->get($endPoint, $params);
                break;
        }

        # TODO: Implement this
        // # Store the rate limit information after the request
        // $this->x_rate_limit_limit = $this->restApi->getLastXHeaders()['x_rate_limit_limit'];
        // $this->x_rate_limit_remaining = $this->restApi->getLastXHeaders()['x_rate_limit_remaining'];
        // $this->x_rate_limit_reset = $this->restApi->getLastXHeaders()['x_rate_limit_reset'];

        // # Save the object
        // $this->save();

        # Return the result
        return $result;
    }

    /**
     * Returns true if twitter api rate limit is reached
     * This can be moved to a class which implements the twitter api
     */
    // public function ($responeData)
    // {
    //     return $this->hasMany(Tweet::className(), ['handle_id' => 'id']);
    // }

}
