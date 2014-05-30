<?php
/**
 * This file was generated by Caviar.
 * http://github.com/Crisu83/yii-caviar
 */

namespace app\models;

/**
 * This is the model class for table AccountToken".
 *
 * The followings are the available columns in table 'account_token':
 *
 * @property integer $id
 * @property integer $accountId
 * @property string $type
 * @property string $token
 * @property string $expires
 * @property integer $status
 *
 * The followings are the available model relations:
 *
 * @property \nordsoftware\yii_account\models\Account $account
 */
class AccountToken extends \CActiveRecord
{
    /**
     * @return string the associated database table name.
     */
    public function tableName()
    {
        return 'account_token';
    }

    /**
     * @return array validation rules for model attributes.
     */
    public function rules()
    {
        return array(
            array('accountId, type, token, expires', 'required'),
            array('accountId, status', 'numerical', 'integerOnly' => true),
            array('type, token, expires', 'length', 'max' => 255),
            array('id, accountId, type, token, expires, status', 'safe', 'on' => 'search'),
        );
    }

    /**
     * @return array relational rules.
     */
    public function relations()
    {
        return array(
            'account' => array(self::BELONGS_TO, '\nordsoftware\yii_account\models\Account', 'accountId')
        );
    }

    /**
     * @return array customized attribute labels (name=>label).
     */
    public function attributeLabels()
    {
        return array(
            'id' => \Yii::t('yii-account', 'ID'),
            'accountId' => \Yii::t('yii-account', 'Account'),
            'type' => \Yii::t('yii-account', 'Type'),
            'token' => \Yii::t('yii-account', 'Token'),
            'expires' => \Yii::t('yii-account', 'Expires'),
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
        $criteria->compare('accountId', $this->accountId);
        $criteria->compare('type', $this->type, true);
        $criteria->compare('token', $this->token, true);
        $criteria->compare('expires', $this->expires, true);
        $criteria->compare('status', $this->status);

        return new \CActiveDataProvider($this, array('criteria' => $criteria));
    }

    /**
     * Returns the static model of this class.
     * @param string $className active record class name.
     * @return AccountToken the static model class.
     */
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }
}
