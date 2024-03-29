<?php

    /*
    * LimeSurvey (tm)
    * Copyright (C) 2011 The LimeSurvey Project Team / Carsten Schmitz
    * All rights reserved.
    * License: GNU/GPL License v2 or later, see LICENSE.php
    * LimeSurvey is free software. This version may have been modified pursuant
    * to the GNU General Public License, and as distributed it includes or
    * is derivative of works licensed under the GNU General Public License or
    * other free or open source software licenses.
    * See COPYRIGHT.php for copyright notices and details.
    *
    */
class ResetPasswordCommand extends CConsoleCommand
{
    public $connection;

    public function run($sArgument)
    {
        if (isset($sArgument) && isset($sArgument[0]) && isset($sArgument[1])) {
            $oUser = User::findByUsername($sArgument[0]);
            if ($oUser) {
                Yii::import('application.helpers.common_helper', true);
                $oUser->setPassword($sArgument[1]);
                // Save the model validating only the password, because there may be issues with other attributes
                // (like an invalid value for some setting), which the user cannot fix because he doesn't have access.
                if ($oUser->save(true, ['password'])) {
                    echo "Password for user {$sArgument[0]} was set.\n";
                    return 0;
                } else {
                    echo "An error happened when setting a password for user {$sArgument[0]}.\n";
                    return 1;
                }
            } else {
                echo "User " . $sArgument[0] . " not found.\n";
                return 1;
            }
        } else {
            //TODO: a valid error process
            echo 'You have to set username and password on the command line like this: php console.php username password';
        }
    }
}
