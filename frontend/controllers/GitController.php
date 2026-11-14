<?php

namespace frontend\controllers;

use yii\web\Controller;

/**
 * Git controller
 * @author Dmitry Suvorov <soovorow@gmail.com>
 */
class GitController extends Controller
{
    const WEBHOOK_KEY = 'REDACTED_BEFORE_PUBLICATION';

    /**
     * @todo Automate git pull using github webhooks
     */
    public function actionPull()
    {

    }
}