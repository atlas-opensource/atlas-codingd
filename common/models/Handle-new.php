<?php

namespace app\models;

use Yii;

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
        return [
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
        ];
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
}
