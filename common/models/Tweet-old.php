<?php

# Define namespace for twitter model
namespace common\models;

# Use other useful namespaces
use Yii;
use Abraham\TwitterOAuth\TwitterOAuth;

/**
 * This is the model class for table "tweet".
 *
 * @property int $id
 * @property int $handle_id
 * @property string $date
 * @property BIGINT $tweet_id
 * @property string $tweet_text
 * @property string $app
 * @property int $followers
 * @property int $follows
 * @property int $retweets
 * @property int $favorites
 * @property string $location
 *
 * @property Tweet $handle
 * @property Tweet[] $tweets
 */
class Tweet extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tweet';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return
        [
            [['handle_id'], 'required'],
            [['handle_id', 'followers', 'follows', 'retweets', 'favorites', 'tweet_id'], 'integer'],
            [['date'], 'safe'],
            [['app', 'location'], 'string', 'max' => 255],
            [['tweet_text'], 'string', 'max' => 800],
            //[['handle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tweet::className(), 'targetAttribute' => ['handle_id' => 'id']],
            [['handle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handle::className(), 'targetAttribute' => ['handle_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'handle_id' => 'Handle ID',
            'date' => 'Date',
            'tweet_id' => 'Tweet ID',
            'tweet_text' => 'Tweet Text',
            'app' => 'App',
            'followers' => 'Followers',
            'follows' => 'Follows',
            'retweets' => 'Retweets',
            'favorites' => 'Favorites',
            'location' => 'Location',
        ];
    }

    /**
     * Makes sure the twitter id does not alerady exist for this user before inserting
     * @return \yii\db\ActiveRecord::beforeSave()
     */
    public function beforeSave($insert)
    {
        # Only continue if we are inserting
        if ($insert)
        {
            # Test value to see if it is actually a tweet (I don't know what find() returns when it doens't find anything)
            if (\common\models\Tweet::find()->where(['tweet_id' => $this->tweet_id, 'handle_id' => $this->handle_id])->exists())
            {
                # Should not insert this because we already stored this tweet.
                return false;
            }
        }

        # Return ActiveRecord::beforeSave();
        return parent::beforeSave($insert);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandle()
    {
        return $this->hasOne(Tweet::className(), ['id' => 'handle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTweets()
    {
        return $this->hasMany(Tweet::className(), ['handle_id' => 'id']);
    }
}
