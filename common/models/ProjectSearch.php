<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project".
 *
 */
class ProjectSearch extends \common\models\Project
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'name' => 'Name'
        ];
    }

    /**
     * {@inheritdoc}
     */
     public function search($params)
     {
         # Main query
         $query = Project::find();

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
                 'id',
                 'user_id',
                 'name'
             ]
         ]);

         # Try to load in params
         if (!($this->load($params) && $this->validate()))
         {
             return $dataProvider;
         }

         # Handle
         $query->andFilterWhere(['like', 'name', $this->name]);

         # Label
         $query->andFilterWhere(['like', 'user_id', $this->user]);

         # Name
         $query->andFilterWhere(['like', 'id', $this->id]);

         # Return
         return $dataProvider;
     }
}
