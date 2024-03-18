<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "project".
 *
 * @property int $id
 * @property int $user_id
 * @property string $name
 *
 * @property User $user
 * @property XrefProjectHandle[] $xrefProjectHandles
 * @property XrefProjectUser[] $xrefProjectUsers
 */
class Project extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'project';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
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
            'name' => 'Name',
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
    public function getXrefProjectHandles()
    {
        return $this->hasMany(XrefProjectHandle::className(), ['project_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getXrefProjectUsers()
    {
        return $this->hasMany(XrefProjectUser::className(), ['project_id' => 'id']);
    }

     /**
     * {@inheritdoc}
     */
    public function addHandleFromForm($formModel)
    {
        # Check for form model
        if (isset($formModel) && !empty($formModel) && is_object($formModel) && isset($formModel->handle) && !empty($formModel->handle))
        {
            # Sanitize input
            $handleString = (string)$formModel->handle;
            $labelString = (string)$formModel->label;

            # Add handle
            return $this->addHandle($handleString, $labelString);
        }

        # Return error
        return false;
    }        
   
    /**
     * {@inheritdoc}
     */
    public function addHandle($handleString, $labelString)
    {
        # Look for existing handle
        $existingHandle = \common\models\Handle::find()->where(['handle' => $handleString])->one();

        # Check for existing handles
        if (empty($existingHandle))
        {
            # TODO: Add more sanity checking after this
            # Instantiate model object
            $handleModel = new \common\models\Handle();

            # Assign data 
            # TODO: (this wont work inside this function)
            $handleModel->setAttributes(array("handle" => $handleString, "label" => $labelString));

            # Assign to current user or to the owner of the project if application does not have user (this could happen if called from mnemosyned)
            $handleModel->user_id = (!empty(\Yii::$app->user) && !empty(\Yii::$app->user->id)) ? \Yii::$app->user->id : $this->user_id;

            # Pull information for twitter user
            $handleInfo = $handleModel->getHandleInfo();

            # Check for errors while pulling info
            if ( !is_object($handleInfo) || ( is_object($handleInfo) && isset($handleInfo->errors[0]) && $handleInfo->errors[0]->code == 50 ) )
            {
                # Return data to page
                return [
                        'data' => [
                                'success' => false,
                                'model' => $handleModel,
                                'message' => 'Handle could not be found.',
                        ],
                        'code' => -1,
                ];
            }
        }
        else
        {
            # This is hacky but we only need the first one.
            $handleModel = $existingHandle;

            # Unset the existing handle object
            $existingHandle = NULL;
            unset($existingHandle);
        }

        # Try to save the handle object
        if ($handleModel->save())
        {
            # Instantiate object to search for existing reference to handle in project
            $existingXrefProjectHandle = \common\models\XrefProjectHandle::find()->where(['handle_id' => $handleModel->id, 'project_id' => $this->id])->one();

            if (empty($existingXrefProjectHandle))
            {
                # Instantiate xref_project_handle
                $xrefProjectHandle = new XrefProjectHandle();

                # Load new xrefProjectHandle object with form data. 
                $xrefProjectHandle->setAttributes(array('project_id' => $this->id));

                # Set handle id
                $xrefProjectHandle->handle_id = $handleModel->id;
            }
            # We found an existing pivot / xref between the handle and the project. This handle already belongs to this project
            else
            {
                # Use the existing reference
                $xrefProjectHandle = $existingXrefProjectHandle;

                # Unset the search object that we used to look for the handle in the project
                $existingXrefProjectHandle = NULL;
                unset($existingXrefProjectHandle);
            }

            # Save pivot
            if ($xrefProjectHandle->save())
            {
                # Get and store all tweets for this handle
                $debugData = $handleModel->updateTweets();

                # Return data to page
                return [
                        'data' => [
                                'success' => true,
                                'model' => $handleModel,
                                'message' => 'Handle has been saved.',
                        ],
                        'code' => 0,
                ];
            }

            # If we get here something went wrong, return error
			return
			[
				'data' =>
				[
					'success' => false,
					'model' => $handleModel,
					'message' => "An unkown error occured adding handle to project",
				],
				'code' => -1,
			];
        }
    }        
}
