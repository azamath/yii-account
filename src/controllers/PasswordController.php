<?php

namespace nordsoftware\yii_account\controllers;

use nordsoftware\yii_account\helpers\Helper;
use nordsoftware\yii_account\Module;

class PasswordController extends Controller
{
    /**
     * @var string
     */
    public $emailSubject;

    /**
     * @var string
     */
    public $forgotFormId = 'forgotPasswordForm';

    /**
     * @var string
     */
    public $resetFormId = 'changePasswordForm';

    /**
     * @var string
     */
    public $layout = 'narrow';

    /**
     * @var string
     */
    public $defaultAction = 'forgot';

    /**
     * @inheritDoc
     */
    public function init()
    {
        parent::init();

        if ($this->emailSubject === null) {
            $this->emailSubject = Helper::t('email', 'Password recovery');
        }
    }

    /**
     * @inheritDoc
     */
    public function filters()
    {
        return array(
            'guestOnly + index',
            'validateToken + reset',
        );
    }

    /**
     * Displays the 'forgot password' page.
     */
    public function actionForgot()
    {
        $modelClass = $this->module->getClassName(Module::CLASS_RECOVER_PASSWORD_FORM);

        /** @var \nordsoftware\yii_account\models\form\RecoverPasswordForm $model */
        $model = new $modelClass();

        $request = \Yii::app()->request;

        if ($request->isAjaxRequest && $request->getPost('ajax') === $this->forgotFormId) {
            echo \CActiveForm::validate($model);
            \Yii::app()->end();
        }

        if ($request->isPostRequest) {
            $model->attributes = $request->getPost(Helper::classNameToKey($modelClass));

            if ($model->validate()) {
                $accountClass = $this->module->getClassName(Module::CLASS_MODEL);

                $account = \CActiveRecord::model($accountClass)->findByAttributes(array('email' => $model->email));

                $token = $this->generateToken(
                    Module::TOKEN_RESET_PASSWORD,
                    $account->id,
                    Helper::sqlDateTime(time() + $this->module->recoverExpireTime)
                );

                $resetUrl = $this->createAbsoluteUrl('/account/password/reset', array('token' => $token));

                $this->module->sendMail(
                    $account->email,
                    $this->emailSubject,
                    $this->renderPartial('/mail/recoverPassword', array('resetUrl' => $resetUrl))
                );

                $this->redirect('sent');
            }
        }

        $this->render('forgot', array('model' => $model));
    }

    /**
     * Displays the 'reset password' page.
     */
    public function actionReset()
    {
        $token = $this->loadToken(Module::TOKEN_RESET_PASSWORD, \Yii::app()->request->getQuery('token'));

        $modelClass = $this->module->getClassName(Module::CLASS_RESET_PASSWORD_FORM);

        /** @var \nordsoftware\yii_account\models\form\ResetPasswordForm $model */
        $model = new $modelClass();

        $request = \Yii::app()->request;

        if ($request->isAjaxRequest && $request->getPost('ajax') === $this->resetFormId) {
            echo \CActiveForm::validate($model);
            \Yii::app()->end();
        }

        if ($request->isPostRequest) {
            $model->attributes = $request->getPost(Helper::classNameToKey($modelClass));

            if ($model->validate()) {
                $accountClass = $this->module->getClassName(Module::CLASS_MODEL);

                $account = \CActiveRecord::model($accountClass)->findByPk($token->accountId);
                $account->changePassword($model->password);

                $token->markUsed();

                $this->redirect(array('/account/authenticate/login'));
            }
        }

        $this->render('reset', array('model' => $model));
    }
} 