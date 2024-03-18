<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "category_option".
 *
 * @property int $id
 * @property int $category_id
 * @property int $code
 * @property string $name
 * @property string $description
 *
 * @property Category $category
 * @property CategoryOptionValue[] $categoryOptionValues
 * @property CategorySubOption[] $categorySubOptions
 */
class CategoryOption extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'category_option';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['category_id', 'code', 'name'], 'required'],
            [['category_id', 'code'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['description'], 'string', 'max' => 500],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::className(), 'targetAttribute' => ['category_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'category_id' => 'Category ID',
            'code' => 'Code',
            'name' => 'Name',
            'description' => 'Description',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::className(), ['id' => 'category_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoryOptionValues()
    {
        return $this->hasMany(CategoryOptionValue::className(), ['category_option_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategorySubOptions()
    {
        return $this->hasMany(CategorySubOption::className(), ['category_option_id' => 'id']);
    }
}
