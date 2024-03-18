<?php

namespace frontend\models;

class CategoryForm extends \yii\base\Model
{
    public $name;
    public $options;

    public function rules()
    {
        # Array of rules
        return
        [
          # Required to load
          [['AddCategoryForm'], 'safe'],

        ];
    }
}
