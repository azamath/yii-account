<?php

namespace nordsoftware\yii_account\controllers;

use nordsoftware\yii_account\exceptions\Exception;
use nordsoftware\yii_account\helpers\Helper;
use nordsoftware\yii_account\models\ar\AccountToken;
use nordsoftware\yii_account\Module;

/**
 * @property \nordsoftware\yii_account\Module $module
 */
class Controller extends \CController
{
    /**
     * @param \CFilterChain $filterChain
     */
    public function filterGuestOnly(\CFilterChain $filterChain)
    {
        if (!\Yii::app()->user->isGuest) {
            $this->accessDenied();
        }

        $filterChain->run();
    }

    /**
     * @param \CFilterChain $filterChain
     */
    public function filterAuthenticatedOnly(\CFilterChain $filterChain)
    {
        if (\Yii::app()->user->isGuest) {
            $this->accessDenied();
        }

        $filterChain->run();
    }

    /**
     * @param \CFilterChain $filterChain
     */
    public function filterValidateToken(\CFilterChain $filterChain)
    {
        $request = \Yii::app()->request;

        if (($token = $request->getQuery('token')) === null) {
            $this->accessDenied();
        }

        $filterChain->run();
    }

    /**
     * Generates a new random token and saves it in the database.
     *
     * @param string $type token type.
     * @param int $accountId account id.
     * @param string $expires token expiration date (mysql date).
     * @throws \nordsoftware\yii_account\exceptions\Exception if the token cannot be generated.
     * @return string the generated token.
     */
    public function generateToken($type, $accountId, $expires)
    {
        if (!$this->module->hasComponent(Module::COMPONENT_TOKEN_GENERATOR)) {
            throw new Exception("Failed to get the token generator component.");
        }

        /** @var \nordsoftware\yii_account\components\TokenGenerator $tokenGenerator */
        $tokenGenerator = $this->module->getComponent(Module::COMPONENT_TOKEN_GENERATOR);
        $token = $tokenGenerator->generate();

        $modelClass = $this->module->getClassName(Module::CLASS_TOKEN_MODEL);

        /** @var \nordsoftware\yii_account\models\ar\AccountToken $model */
        $model = new $modelClass();
        $model->type = $type;
        $model->accountId = $accountId;
        $model->token = $token;
        $model->expiresAt = $expires;

        if (!$model->save()) {
            throw new Exception("Failed to save account token.");
        }

        return $token;
    }

    /**
     * Loads a token of a specific type.
     *
     * @param string $type token type.
     * @param string $token token string.
     * @throws \nordsoftware\yii_account\exceptions\Exception
     * @return \nordsoftware\yii_account\models\ar\AccountToken
     */
    public function loadToken($type, $token)
    {
        $modelClass = $this->module->getClassName(Module::CLASS_TOKEN_MODEL);

        /** @var \nordsoftware\yii_account\models\ar\AccountToken $model */
        $model = \CActiveRecord::model($modelClass)->findByAttributes(
            array('type' => $type, 'token' => $token, 'status' => AccountToken::STATUS_UNUSED)
        );

        if ($model === null || $model->hasExpired()) {
            $this->accessDenied();
        }

        return $model;
    }

    /**
     * @throws \CHttpException when called.
     */
    public function accessDenied()
    {
        throw new \CHttpException(401, Helper::t('controllers', 'Access denied.'));
    }

    /**
     * @throws \CHttpException when called.
     */
    public function pageNotFound()
    {
        throw new \CHttpException(404, Helper::t('controllers', 'Page not found.'));
    }

    /**
     * @throws \CHttpException when called.
     */
    public function fatalError()
    {
        throw new \CHttpException(500, Helper::t('controllers', 'Something went wrong.'));
    }

    /**
     * Loads a specific account model.
     *
     * @param int $id account identifier.
     * @throws \CHttpException if the account model cannot be found.
     * @return \nordsoftware\yii_account\models\ar\Account
     */
    public function loadModel($id)
    {
        $modelClass = $this->module->getClassName(Module::CLASS_MODEL);
        $model = \CActiveRecord::model($modelClass)->findByPk($id);

        if ($model === null) {
            throw new \CHttpException(404, Helper::t('errors', "Page not found."));
        }

        return $model;
    }

    /**
     * @inheritDoc
     */
    public function getLayoutFile($layoutName)
    {
        if (($layoutFile = parent::getLayoutFile($layoutName)) === false) {
            $moduleViewPath = dirname(__DIR__) . '/views';
            $layoutFile = $this->resolveViewFile($layoutName, "{$moduleViewPath}/layouts", $moduleViewPath);
        }

        return $layoutFile;
    }

    /**
     * @inheritDoc
     */
    public function getViewFile($viewName)
    {
        if (($viewFile = parent::getViewFile($viewName)) === false) {
            $moduleViewPath = dirname(__DIR__) . '/views';
            $viewFile = $this->resolveViewFile($viewName, "{$moduleViewPath}/{$this->getId()}", $moduleViewPath);
        }

        return $viewFile;
    }
}