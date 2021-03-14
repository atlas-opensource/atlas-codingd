<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "xref_project_handle".
 *
 * @property int $id
 * @property int $project_id
 * @property int $handle_id
 *
 * @property Handle $handle
 * @property Project $project
 */
class XrefProjectHandle extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'xref_project_handle';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['project_id', 'handle_id'], 'integer'],
            [['handle_id'], 'exist', 'skipOnError' => true, 'targetClass' => Handle::className(), 'targetAttribute' => ['handle_id' => 'id']],
            [['project_id'], 'exist', 'skipOnError' => true, 'targetClass' => Project::className(), 'targetAttribute' => ['project_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'project_id' => 'Project ID',
            'handle_id' => 'Handle ID',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHandle()
    {
        return $this->hasOne(Handle::className(), ['id' => 'handle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getProject()
    {
        return $this->hasOne(Project::className(), ['id' => 'project_id']);
    }
}
