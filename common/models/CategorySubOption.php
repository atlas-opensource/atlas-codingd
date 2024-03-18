<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category_sub_option".
 *
 * @property int $id
 * @property int $category_option_id
 * @property string $code
 * @property string $name
 * @property string $description
 *
 * @property CategoryOption $categoryOption
 * @property CategorySubOptionValue[] $categorySubOptionValues
 */
class CategorySubOption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category_sub_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_option_id', 'code', 'name'], 'required'],
            [['category_option_id'], 'integer'],
            [['description'], 'string'],
            [['code', 'name'], 'string', 'max' => 255],
            [['category_option_id'], 'exist', 'skipOnError' => true, 'targetClass' => CategoryOption::className(), 'targetAttribute' => ['category_option_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_option_id' => 'Category Option ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
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
    public function getCategorySubOptionValues()
    {
        return $this->hasMany(CategorySubOptionValue::className(), ['category_sub_option_id' => 'id']);
    }
}
