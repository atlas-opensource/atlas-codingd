<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category_option_value".
 *
 * @property int $id
 * @property int $user_id
 * @property int $tweet_id
 * @property int $category_option_id
 *
 * @property CategoryOption $categoryOption
 * @property Tweet $tweet
 * @property User $user
 * @property CategorySubOptionValue[] $categorySubOptionValues
 */
class CategoryOptionValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category_option_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tweet_id', 'category_option_id'], 'required'],
            [['user_id', 'tweet_id', 'category_option_id'], 'integer'],
            [['category_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryOption::className(), 'targetAttribute' => ['category_option_id' => 'id']],
            [['tweet_id'], 'exist', 'skipOnError' => true, 'targetClass' => Tweet::className(), 'targetAttribute' => ['tweet_id' => 'id']],
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
            'tweet_id' => 'Tweet ID',
            'category_option_id' => 'Category Option ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryOption()
    {
        return $this->hasOne(CategoryOption::className(), ['id' => 'category_option_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTweet()
    {
        return $this->hasOne(Tweet::className(), ['id' => 'tweet_id']);
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
    public function getCategorySubOptionValues()
    {
        return $this->hasMany(CategorySubOptionValue::className(), ['category_option_value_id' => 'id']);
    }
}
