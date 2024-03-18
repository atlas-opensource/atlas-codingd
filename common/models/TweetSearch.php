<?php

namespace common\models;

use Yii;
use common\models\Tweet;

/**
 * This is the model class for TweetSearch.
 *
 * @property string $location
 *
 */
class TweetSearch extends Tweet
{
    # Date filter variable
    public $date_filter;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['handle_id'], 'required'],
            [['id', 'handle_id', 'tweet_id', 'followers', 'follows', 'retweets', 'favorites'], 'integer'],
            [['date'], 'safe'],
            [['tweet_text', 'retweet_text', 'date_filter'], 'string', 'max' => 800],
            [['app', 'location'], 'string', 'max' => 255],
            [['handle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handle::className(), 'targetAttribute' => ['handle_id' => 'id']],
        ];
    }

    /**
      * {@inheritdoc}
      */
     public function search($handleId, $params)
     {
         # Set Handle Id
         $this->handle_id = (integer)$handleId;

         # Main query
         $query = \common\models\Tweet::find()->where(['handle_id' => $handleId]);

         # Data data provider
         $dataProvider = new \yii\data\ActiveDataProvider
         ([
             'query' => $query,
         ]);

         # Sorting
         $dataProvider->setSort
         ([
             'attributes' =>
             [
                 'id',
                 'tweet_id',
                 'date',
                 'tweet_text',
                 'favorites',
                 'retweets'
             ],
             'defaultOrder' => ['date'=>SORT_DESC]
         ]);

         # Try to load in params (TODO: fix validation)
         if (!($this->load($params) && $this->validate()))
         {
             // print_r($this->getErrors());exit();
             return $dataProvider;
         }

         if ( ! is_null($this->date) && strpos($this->date, ' - ') !== false )
         {
             list($start_date, $end_date) = explode(' - ', $this->date);
             $query->andFilterWhere(['between', 'date', $start_date, $end_date]);
             $this->date = null;
         }

         # Tweet text
         $query->andFilterWhere(['=', 'id', $this->id]);

         # Tweet text
         $query->andFilterWhere(['like', 'tweet_text', $this->tweet_text]);

         # Favorites
         $query->andFilterWhere(['>=', 'favorites', $this->favorites]);

         # Retweets
         $query->andFilterWhere(['>=', 'retweets', $this->retweets]);

         # Return
         return $dataProvider;
     }
}
