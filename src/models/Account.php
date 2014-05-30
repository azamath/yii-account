<?php
/**
 * This file was generated by Caviar.
 * http://github.com/Crisu83/yii-caviar
 */

namespace nordsoftware\yii_account\models;

/**
 * This is the model class for table Account".
 *
 * The followings are the available columns in table 'account':
 *
 * @property integer $id
 * @property string $salt
 * @property string $username
 * @property string $password
 * @property string $passwordStrategy
 * @property integer $requireNewPassword
 * @property string $lastLoginAt
 * @property string $lastActiveAt
 * @property integer $status
 */
class Account extends \CActiveRecord
{
    /**
     * @inheritDoc
     */
    public function tableName()
    {
        return 'account';
    }

    /**
     * @inheritDoc
     */
    public function rules()
    {
        return array(
            array('salt, username, password, passwordStrategy', 'required'),
            array('requireNewPassword, status', 'numerical', 'integerOnly' => true),
            array('salt, username, password, passwordStrategy', 'length', 'max' => 255),
            array('lastLoginAt, lastActiveAt', 'safe'),
            array('id, username, passwordStrategy, requireNewPassword, lastLoginAt, lastActiveAt, status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @inheritDoc
     */
    public function behaviors()
    {
        return array(
            'PasswordBehavior' => array(
                'class' => 'YiiPassword\Behavior',
                'defaultStrategyName' => 'bcrypt',
                'strategies' => array(
                    'bcrypt' => array(
                        'class' => 'YiiPassword\Strategies\Bcrypt',
                        'workFactor' => 14
                    ),
                ),
            ),
        );
    }

    /**
     * @inheritDoc
     */
    public function attributeLabels()
    {
        return array(
            'id' => \Yii::t('yii-account', 'ID'),
            'salt' => \Yii::t('yii-account', 'Salt'),
            'username' => \Yii::t('yii-account', 'Username'),
            'password' => \Yii::t('yii-account', 'Password'),
            'passwordStrategy' => \Yii::t('yii-account', 'Password Strategy'),
            'requireNewPassword' => \Yii::t('yii-account', 'Require New Password'),
            'lastLoginAt' => \Yii::t('yii-account', 'Last Login At'),
            'lastActiveAt' => \Yii::t('yii-account', 'Last Active At'),
            'status' => \Yii::t('yii-account', 'Status')
        );
    }

    /**
     * Retrieves a list of models based on the current search/filter conditions.
     * @return \CActiveDataProvider the data provider that can return the models based on the search conditions.
     */
    public function search()
    {
        $criteria = new \CDbCriteria();

        $criteria->compare('id', $this->id);
        $criteria->compare('username', $this->username, true);
        $criteria->compare('passwordStrategy', $this->passwordStrategy, true);
        $criteria->compare('requireNewPassword', $this->requireNewPassword);
        $criteria->compare('lastLoginAt', $this->lastLoginAt, true);
        $criteria->compare('lastActiveAt', $this->lastActiveAt, true);
        $criteria->compare('status', $this->status);

        return new \CActiveDataProvider($this, array('criteria' => $criteria));
    }

    /**
     * Returns the static model of this class.
     * @param string $className active record class name.
     * @return \nordsoftware\yii_account\models\Account the static model class.
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
