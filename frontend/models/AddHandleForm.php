<?php

namespace frontend\models;

class AddHandleForm extends \yii\base\Model
{
    public $handle;
    public $label;

    public function rules()
    {
        # Array of rules
        return
        [
          # Required to load
          [['AddHandleForm'], 'safe'],

          # These fields are required
          [['handle'], 'required'],

          # Handle must look like this:
          [['handle'], 'match', 'pattern' => '/^@?(\w){1,15}$/i'],

          # Labels are strings
          [['label'], 'string', 'max' => 155]
        ];
    }
}
