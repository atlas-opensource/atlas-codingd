<?php
namespace common\models;

use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 * @property string $password write-only password
 */
class MnemosyneUser extends User
{
    /**
     * Returns true if user is an administrator
     */
    public function isAdmin()
    {
        // This needs to be stored in a configuration or database. 
        $admins = [1];
        return (in_array($this->id, $admins));
    }
}
