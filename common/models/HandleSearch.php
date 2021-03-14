<?php

# Define namespace for model
namespace common\models;

# Use other useful namespaces
use \Yii;
use common\models\Handle;
use common\models\XrefProjectUser;
use common\models\XrefProjectHandle;

/**
 * This is the model class for Handle searching".
 *
 * @property int $numTweets
 *
 */
class HandleSearch extends Handle
{
    # Number of tweets from this handle total
    public $numTweets;

  /**
    * {@inheritdoc}
    */
    public function rules()
    {
        return
        [

            //[['user_id', 'handle'], 'required'],
            [['user_id', 'verified'], 'integer'],
            [['user_since', 'last_update', 'numTweets'], 'safe'],
            [['handle', 'name', 'profile_image'], 'string', 'max' => 255],
            [['label'], 'string', 'max' => 50],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
      * {@inheritdoc}
      */
     public function search($project_id, $params)
     {
         # Main query
         //$query = Handle::find()->where(['']);
         $subQuery = XrefProjectHandle::find()->select('handle_id')->where(['project_id' => $project_id]);

         $query = Handle::find()->where(['in', 'id', $subQuery]);

         # Data data provider
         $dataProvider = new \yii\data\ActiveDataProvider
         ([
             'query' => $query,
             'pagination' =>
             [
                 'pageSize' => 150,
              ],

         ]);

         # Sorting
         $dataProvider->setSort
         ([
             'attributes' =>
             [
                 'handle',
                 'label',
                 'name',
                 'user_since'
             ]
         ]);

         # Try to load in params
         if (!($this->load($params) && $this->validate()))
         {
             return $dataProvider;
         }

         # Handle
         $query->andFilterWhere(['like', 'handle', $this->handle]);

         # Label
         $query->andFilterWhere(['like', 'label', $this->label]);

         # Name
         $query->andFilterWhere(['like', 'name', $this->name]);

         # User Since
         $query->andFilterWhere(['<', 'user_since', $this->user_since]);

         # Return
         return $dataProvider;
     }
}
