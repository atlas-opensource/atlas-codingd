<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category_sub_option_value".
 *
 * @property int $id
 * @property int $user_id
 * @property int $tweet_id
 * @property int $category_sub_option_id
 * @property int $category_option_value_id
 *
 * @property CategoryOptionValue $categoryOptionValue
 * @property CategorySubOption $categorySubOption
 * @property Tweet $tweet
 * @property User $user
 */
class CategorySubOptionValue extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category_sub_option_value';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'tweet_id', 'category_sub_option_id', 'category_option_value_id'], 'required'],
            [['user_id', 'tweet_id', 'category_sub_option_id', 'category_option_value_id'], 'integer'],
            [['category_option_value_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryOptionValue::className(), 'targetAttribute' => ['category_option_value_id' => 'id']],
            [['category_sub_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategorySubOption::className(), 'targetAttribute' => ['category_sub_option_id' => 'id']],
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
            'category_sub_option_id' => 'Category Sub Option ID',
            'category_option_value_id' => 'Category Option Value ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryOptionValue()
    {
        return $this->hasOne(CategoryOptionValue::className(), ['id' => 'category_option_value_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategorySubOption()
    {
        return $this->hasOne(CategorySubOption::className(), ['id' => 'category_sub_option_id']);
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
}
