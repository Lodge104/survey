<?php

/*
 * LimeSurvey
 * Copyright (C) 2007-2011 The LimeSurvey Project Team / Carsten Schmitz
 * All rights reserved.
 * License: GNU/GPL License v2 or later, see LICENSE.php
 * LimeSurvey is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 *
 */

/**
 * Quotas Controller
 *
 * This controller performs quota actions
 *
 * @package     LimeSurvey
 * @subpackage  Backend
 */
class Quotas extends SurveyCommonAction
{
    public function __construct($controller, $id)
    {
        parent::__construct($controller, $id);

        // Load helpers
        Yii::app()->loadHelper('surveytranslator');
        // Sanitize/get globals/variables
        $_POST['quotamax'] = sanitize_int(Yii::app()->request->getPost('quotamax'));

        if (empty($_POST['autoload_url'])) {
            $_POST['autoload_url'] = 0;
        }

        if (empty($_POST['quota_limit']) || !is_numeric(Yii::app()->request->getPost('quota_limit')) || Yii::app()->request->getPost('quota_limit') < 0) {
            $_POST['quota_limit'] = 0;
        }
    }

    private function getData($iSurveyId)
    {
        $oSurvey = Survey::model()->findByPk($iSurveyId);
        // Set the variables in an array
        $aData['iSurveyId'] = $aData['surveyid'] = $iSurveyId;
        $aData['sBaseLang'] = $oSurvey->language;
        $aData['aLangs'] = $oSurvey->allLanguages;

        $aData['action'] = $action = Yii::app()->request->getParam('action');
        if (!isset($action)) {
            $aData['action'] = 'quotas';
        }

        return $aData;
    }

    /**
     * Check permission for a survey about quota, redirect if no permission
     * @param integer $iSurveyId
     * @param string $sPermission
     * @return void
     */
    private function checkPermissions($iSurveyId, $sPermission)
    {
        if (!empty($sPermission) && !(Permission::model()->hasSurveyPermission($iSurveyId, 'quotas', $sPermission))) {
            Yii::app()->session['flashmessage'] = gT('Access denied!');
            $this->redirectToIndex($iSurveyId);
        }
    }

    /**
     * Check validity of a quotaid against a survey id
     * @param integer $iSurveyId
     * @param integer $quotaid
     * throw Exception
     * @return void
     */
    private function checkValidQuotaId($surveyId, $quotaId)
    {
        $model = Quota::model()->find(
            "id = :quotaid AND sid = :surveyid",
            [':quotaid' => $quotaId, ':surveyid' => $surveyId]
        );
        if (empty($model)) {
            throw new CHttpException(403, gT("Bad quota ID related to this survey ID"));
        }
    }

    private function redirectToIndex($iSurveyId)
    {
        if (Permission::model()->hasSurveyPermission($iSurveyId, 'quotas', 'read')) {
            $this->getController()->redirect($this->getController()->createUrl("/admin/quotas/sa/index/surveyid/$iSurveyId"));
        } else {
            Yii::app()->session['flashmessage'] = gT('Access denied!');
            $this->getController()->redirect($this->getController()->createUrl("surveyAdministration/view/surveyid/$iSurveyId"));
        }
    }

    public function massiveAction()
    {

        $action = Yii::app()->request->getQuery('action');
        $allowedActions = array('activate', 'deactivate', 'delete', 'changeLanguageSettings');
        if (isset($_POST) && in_array($action, $allowedActions)) {
            $sItems = Yii::app()->request->getPost('sItems');
            $aQuotaIds = json_decode($sItems);
            $errors = array();
            foreach ($aQuotaIds as $iQuotaId) {
                /** @var Quota $oQuota */
                $oQuota = Quota::model()->findByPk($iQuotaId);
                if (in_array($action, array('activate', 'deactivate'))) {
                    if (!Permission::model()->hasSurveyPermission($oQuota->sid, 'quotas', 'update')) {
                        $errors[] = gT("You do not have permission for this survey.");
                        continue;
                    }
                    $oQuota->active = ($action == 'activate' ? 1 : 0);
                    $oQuota->save();
                } elseif ($action == 'delete') {
                    if (!Permission::model()->hasSurveyPermission($oQuota->sid, 'quotas', 'delete')) {
                        $errors[] = gT("You do not have permission for this survey.");
                        continue;
                    }
                    $oQuota->delete();
                } elseif ($action == 'changeLanguageSettings' && !empty($_POST['QuotaLanguageSetting'])) {
                    if (!Permission::model()->hasSurveyPermission($oQuota->sid, 'quotas', 'update')) {
                        $errors[] = gT("You do not have permission for this survey.");
                        continue;
                    }
                    $oQuotaLanguageSettings = $oQuota->languagesettings;
                    foreach ($_POST['QuotaLanguageSetting'] as $language => $aQuotaLanguageSettingAttributes) {
                        $oQuotaLanguageSetting = $oQuota->languagesettings[$language];
                        $oQuotaLanguageSetting->attributes = $aQuotaLanguageSettingAttributes;
                        if (!$oQuotaLanguageSetting->save()) {
                            // save errors
                            $oQuotaLanguageSettings[$language] = $oQuotaLanguageSetting;
                            $errors[] = $oQuotaLanguageSetting->errors;
                        }
                    }
                    // render form again to display errorSummary
                    if (!empty($errors)) {
                        $this->getController()->renderPartial(
                            '/admin/quotas/viewquotas_massive_langsettings_form',
                            array(
                                'oQuota' => $oQuota,
                                'aQuotaLanguageSettings' => $oQuotaLanguageSettings,
                            )
                        );
                        return;
                    }
                }
            }
            if (empty($errors)) {
                eT("OK!");
            }
        }
    }

    /**
     * Index
     * @param int $iSurveyId
     * @param bool $quickreport Default is false
     */
    public function index(int $iSurveyId, bool $quickreport = false)
    {
        $iSurveyId = sanitize_int($iSurveyId);
        $this->checkPermissions($iSurveyId, 'read');
        $aData = $this->getData($iSurveyId);
        $aViewUrls = array();

        if ($quickreport == false) {
            $aViewUrls[] = 'viewquotas_view';
        }

        $aData['surveyid'] = $iSurveyID = $surveyid = sanitize_int($iSurveyId);

        $aData['sidemenu']['state'] = false;

        /** @var Survey $oSurvey */
        $oSurvey = Survey::model()->findByPk($iSurveyID);
        $aData['title_bar']['title'] = $oSurvey->currentLanguageSettings->surveyls_title . " (" . gT("ID") . ":" . $iSurveyID . ")";
        $aData['subaction'] = gT("Survey quotas");

        // TODO: I dont think that is is needed anymore. Remove it.
        $aData['surveybar']['buttons']['view'] = true;
        $aData['surveybar']['active_survey_properties']['img'] = 'quota';
        $aData['surveybar']['active_survey_properties']['txt'] = gT("Quotas");
        $aData['surveybar']['closebutton']['url'] = 'surveyAdministration/view/surveyid/' . $iSurveyID; // Close button
        $aData['surveybar']['closebutton']['forbidden'][] = 'quotas';

        $totalquotas = 0;
        $totalcompleted = 0;
        $csvoutput = array();

        // Set number of page
        if (Yii::app()->getRequest()->getQuery('pageSize')) {
            Yii::app()->user->setState('pageSize', (int) Yii::app()->getRequest()->getQuery('pageSize'));
        }
        $aData['iGridPageSize'] = Yii::app()->user->getState('pageSize', Yii::app()->params['defaultPageSize']);
        $aData['oDataProvider'] = new CArrayDataProvider($oSurvey->quotas, array(
            'pagination' => array(
                'pageSize' => $aData['iGridPageSize'],
                'pageVar' => 'page'
            ),
        ));

        //if there are quotas let's proceed
        $aViewUrls['output'] = '';

        // TopBar
        $aData['topBar']['name'] = 'surveyTopbar_view';
        $aData['topBar']['leftSideView'] = 'quotasTopbarLeft_view';

        if (!empty($oSurvey->quotas)) {
            $aData['output'] = '';
            $aQuotaItems = array();

            //loop through all quotas
            foreach ($oSurvey->quotas as $oQuota) {
                $totalquotas += $oQuota->qlimit;
                $completed = 0;
                $completed = $oQuota->completeCount;
                $totalcompleted = $totalcompleted + $completed;
                $csvoutput[] = $oQuota->name . "," . $oQuota->qlimit . "," . $completed . "," . ($oQuota->qlimit - $completed) . "\r\n";

                if ($quickreport != false) {
                    continue;
                }

                // Edit URL
                $aData['aEditUrls'][$oQuota->primaryKey] = App()->createUrl("admin/quotas/sa/editquota/surveyid/" . $iSurveyId, array(
                    'sid' => $iSurveyId,
                    'action' => 'quotas',
                    'quota_id' => $oQuota->primaryKey,
                    'subaction' => 'quota_editquota'

                ));

                // Delete URL
                $aData['aDeleteUrls'][$oQuota->primaryKey] = App()->createUrl("admin/quotas/sa/delquota/surveyid/" . $iSurveyId, array(
                    'sid' => $iSurveyId,
                    'action' => 'quotas',
                    'quota_id' => $oQuota->primaryKey,
                    'subaction' => 'quota_delquota'
                ));

                //loop through all sub-parts
                foreach ($oQuota->quotaMembers as $oQuotaMember) {
                    $aQuestionAnswers = self::getQuotaAnswers($oQuotaMember['qid'], $iSurveyId, $oQuota['id']);
                    if ($oQuotaMember->question->type == '*') {
                        $answerText = $oQuotaMember->code;
                    } else {
                        $answerText = isset($aQuestionAnswers[$oQuotaMember['code']]) ? flattenText($aQuestionAnswers[$oQuotaMember['code']]['Display']) : null;
                    }

                    $aQuotaItems[$oQuota['id']][] = array(
                        'oQuestion' => Question::model()
                            ->with('questionl10ns', array('language' => $oSurvey->language))
                            ->findByPk(array('qid' => $oQuotaMember['qid'])),
                        'answer_title' => $answerText,
                        'oQuotaMember' => $oQuotaMember,
                        'valid' => isset($answerText),
                    );
                }
            }
            $aData['totalquotas'] = $totalquotas;
            $aData['totalcompleted'] = $totalcompleted;
            $aData['aQuotaItems'] = $aQuotaItems;

            // take the last quota as base for bulk edits
            $aData['oQuota'] = $oQuota;
            $aData['aQuotaLanguageSettings'] = array();
            foreach ($oQuota->languagesettings as $languagesetting) {
                $aData['aQuotaLanguageSettings'][$languagesetting->quotals_language] = $languagesetting;
            }
        } else {
            // No quotas have been set for this survey
            $aData['output'] = $this->getController()->renderPartial('/admin/quotas/viewquotasempty_view', $aData, true);
        }

        $aData['totalquotas'] = $totalquotas;
        $aData['totalcompleted'] = $totalcompleted;

        if ($quickreport == false) {
            Yii::app()->loadHelper('admin.htmleditor');
            $this->renderWrappedTemplate('quotas', $aViewUrls, $aData);
        } else {
            /* Export a quickly done csv file */
            header("Content-Disposition: attachment; filename=quotas-survey" . $iSurveyId . ".csv");
            header("Content-type: text/comma-separated-values; charset=UTF-8");
            echo gT("Quota name") . "," . gT("Limit") . "," . gT("Completed") . "," . gT("Remaining") . "\r\n";
            foreach ($csvoutput as $line) {
                echo $line;
            }
            App()->end();
        }
    }

    /**
     * Insert Quota answer
     * @param int $iSurveyId
     */
    public function insertquotaanswer(int $iSurveyId)
    {
        $this->requirePostRequest();

        $iSurveyId = sanitize_int($iSurveyId);
        $qid = Yii::app()->request->getPost('quota_qid');
        $quota_id = Yii::app()->request->getPost('quota_id');
        $this->checkPermissions($iSurveyId, 'update');
        $this->checkValidQuotaId($iSurveyId, $quota_id);

        $oQuotaMembers = new QuotaMember('create'); // Trigger the 'create' rules
        $oQuotaMembers->sid = $iSurveyId;
        $oQuotaMembers->qid = $qid;
        $oQuotaMembers->quota_id = $quota_id;
        $oQuotaMembers->code = Yii::app()->request->getPost('quota_anscode');
        if ($oQuotaMembers->save()) {
            if (!empty($_POST['createanother'])) {
                $_POST['action'] = "quotas";
                $_POST['subaction'] = "newanswer";
                $sSubAction = "newanswer";
                self::newanswer($iSurveyId, $sSubAction);
            } else {
                self::redirectToIndex($iSurveyId);
            }
        } else {
            // Save was not successful, redirect back
            $_POST['action'] = "quotas";
            $_POST['subaction'] = "newanswer";
            $sSubAction = "new_answer_two";
            self::newanswer($iSurveyId, $sSubAction);
        }
    }

    /**
     * Delete answers
     * @param int $iSurveyId
     */
    public function delans(int $iSurveyId)
    {
        $this->requirePostRequest();

        $iSurveyId = sanitize_int($iSurveyId);
        $id = App()->request->getPost('quota_member_id');
        $qid = App()->request->getPost('quota_qid');
        $code = App()->request->getPost('quota_anscode');
        /* find related quota by quotamember->id */
        $QuotaMember = QuotaMember::model()->findByPk($id);
        if (empty($QuotaMember)) {
            throw new CHttpException(404, gT("Quota member not found."));
        }
        $this->checkPermissions($iSurveyId, 'update');
        $this->checkValidQuotaId($iSurveyId, $QuotaMember->quota_id);
        $QuotaMember->delete();
        self::redirectToIndex($iSurveyId);
    }

    /**
     * Delete Quota
     * @param int $iSurveyId
     */
    public function delquota(int $iSurveyId)
    {
        $this->requirePostRequest();

        $iSurveyId = sanitize_int($iSurveyId);
        $quotaId = Yii::app()->request->getQuery('quota_id');
        $this->checkPermissions($iSurveyId, 'delete');
        $this->checkValidQuotaId($iSurveyId, $quotaId);

        Quota::model()->deleteByPk($quotaId);
        QuotaLanguageSetting::model()->deleteAllByAttributes(array('quotals_quota_id' => $quotaId));
        QuotaMember::model()->deleteAllByAttributes(array('quota_id' => $quotaId));

        Yii::app()->user->setFlash('success', sprintf(gT("Quota with ID %s was deleted"), $quotaId));

        self::redirectToIndex($iSurveyId);
    }

    /**
     * Edit Quota
     * @param int iSurveyId
     */
    public function editquota(int $iSurveyId)
    {

        $iSurveyId = sanitize_int($iSurveyId);
        $oSurvey = Survey::model()->findByPk($iSurveyId);
        $quotaId = Yii::app()->request->getQuery('quota_id');

        $this->checkPermissions($iSurveyId, 'update');
        $this->checkValidQuotaId($iSurveyId, $quotaId);
        $aData = $this->getData($iSurveyId);
        $aViewUrls = array();


        /* @var Quota $oQuota */
        $oQuota = Quota::model()->findByPk($quotaId);

        if (App()->getRequest()->getPost('Quota')) {
            $attributes = (array) App()->getRequest()->getPost('Quota');
            unset($attributes['id']);
            $oQuota->attributes = $attributes;
            if ($oQuota->save()) {
                foreach ($_POST['QuotaLanguageSetting'] as $language => $settingAttributes) {
                    $oQuotaLanguageSetting = $oQuota->languagesettings[$language];
                    $oQuotaLanguageSetting->attributes = $settingAttributes;

                    //Clean XSS - Automatically provided by CI
                    $oQuotaLanguageSetting->quotals_message = html_entity_decode($oQuotaLanguageSetting->quotals_message, ENT_QUOTES, "UTF-8");
                    // Fix bug with FCKEditor saving strange BR types
                    $oQuotaLanguageSetting->quotals_message = fixCKeditorText($oQuotaLanguageSetting->quotals_message);

                    if (!$oQuotaLanguageSetting->save()) {
                        $oQuota->addErrors($oQuotaLanguageSetting->getErrors());
                    }
                }
                if (!$oQuota->getErrors()) {
                    Yii::app()->user->setFlash('success', gT("Quota saved"));
                    self::redirectToIndex($iSurveyId);
                }
            }
        }

        $aData['oQuota'] = $oQuota;
        $aData['aQuotaLanguageSettings'] = array();
        foreach ($oQuota->languagesettings as $languagesetting) {
            /* url is decoded before usage @see https://github.com/LimeSurvey/LimeSurvey/blob/8d8420a4efcf8e71c4fccbb6708648ace263ca80/application/views/admin/survey/editLocalSettings_view.php#L60 */
            $languagesetting['quotals_url'] = htmlspecialchars_decode($languagesetting['quotals_url']);
            $aData['aQuotaLanguageSettings'][$languagesetting->quotals_language] = $languagesetting;
        }

        $aViewUrls[] = 'editquota_view';

        $aData['sidemenu']['state'] = false;
        $aData['title_bar']['title'] = $oSurvey->currentLanguageSettings->surveyls_title . " (" . gT("ID") . ":" . $iSurveyId . ")";

        $aData['surveybar']['closebutton']['url'] = 'admin/quotas/sa/index/surveyid/' . $iSurveyId; // Close button
        $aData['surveybar']['savebutton']['form'] = 'frmeditgroup';
        $aData['topBar']['showSaveButton'] = true;

        Yii::app()->loadHelper('admin.htmleditor');
        $this->renderWrappedTemplate('quotas', $aViewUrls, $aData);
    }

    /**
     * Add new answer to quota
     *
     * @param int $iSurveyId
     * @param string $sSubAction
     * @return void
     */
    public function newanswer($iSurveyId, $sSubAction = 'newanswer')
    {
        $this->requirePostRequest();

        $iSurveyId = sanitize_int($iSurveyId);
        $oSurvey = Survey::model()->findByPk($iSurveyId);
        $quota_id = Yii::app()->request->getPost('quota_id');
        $this->checkPermissions($iSurveyId, 'update');
        $this->checkValidQuotaId($iSurveyId, $quota_id);
        $aData = $this->getData($iSurveyId);
        $aViewUrls = array();
        $quota = Quota::model()->findByPk($quota_id);
        $aData['oQuota'] = $quota;

        if (($sSubAction == "newanswer" || ($sSubAction == "new_answer_two" && !isset($_POST['quota_qid']))) && Permission::model()->hasSurveyPermission($iSurveyId, 'quotas', 'create')) {
            $result = $oSurvey->quotableQuestions;
            if (empty($result)) {
                $aViewUrls[] = 'newanswererror_view';
            } else {
                $aViewUrls[] = 'newanswer_view';
            }
        }

        if ($sSubAction == "new_answer_two" && isset($_POST['quota_qid']) && Permission::model()->hasSurveyPermission($iSurveyId, 'quotas', 'create')) {
            $oQuestion = Question::model()
                ->with('questionl10ns', array('language' => $oSurvey->language))
                ->findByPk(array('qid' => Yii::app()->request->getPost('quota_qid')));

            $aQuestionAnswers = self::getQuotaAnswers(Yii::app()->request->getPost('quota_qid'), $iSurveyId, Yii::app()->request->getPost('quota_id'));
            $x = 0;

            foreach ($aQuestionAnswers as $aQACheck) {
                if (isset($aQACheck['rowexists'])) {
                    $x++;
                }
            }

            reset($aQuestionAnswers);
            $aData['oQuestion'] = $oQuestion;
            $aData['question_answers'] = $aQuestionAnswers;
            $aData['x'] = $x;
            $aViewUrls[] = 'newanswertwo_view';
        }

        $aData['sidemenu']['state'] = false;
        $aData['title_bar']['title'] = $oSurvey->currentLanguageSettings->surveyls_title . " (" . gT("ID") . ":" . $iSurveyId . ")";
        $aData['surveybar']['closebutton']['url'] = 'admin/quotas/sa/index/surveyid/' . $iSurveyId; // Close button
        $aData['surveybar']['closebutton']['forbidden'][] = 'newanswer';

        $this->renderWrappedTemplate('quotas', $aViewUrls, $aData);
    }

    /**
     * New Quota
     * @param int iSurveyId
     */
    public function newquota(int $iSurveyId)
    {
        $iSurveyId = sanitize_int($iSurveyId);
        $oSurvey = Survey::model()->findByPk($iSurveyId);
        $this->checkPermissions($iSurveyId, 'create');
        $aData = $this->getData($iSurveyId);

        $aData['thissurvey'] = getSurveyInfo($iSurveyId);
        $aData['langs'] = $aData['aLangs'];
        $aData['baselang'] = $aData['sBaseLang'];

        $aData['sidemenu']['state'] = false;
        $aData['topBar']['showSaveButton'] = true;
        $aData['title_bar']['title'] = $oSurvey->currentLanguageSettings->surveyls_title . " (" . gT("ID") . ":" . $iSurveyId . ")";
        $aData['surveybar']['savebutton']['form'] = 'frmeditgroup';
        $aData['surveybar']['closebutton']['url'] = 'admin/quotas/sa/index/surveyid/' . $iSurveyId; // Close button

        $oQuota = new Quota();
        $oQuota->sid = $oSurvey->primaryKey;

        if (App()->getRequest()->getPost('Quota')) {
            $aAttributes = (array) App()->getRequest()->getPost('Quota');
            unset($aAttributes['id']);
            $oQuota->attributes = $aAttributes;
            if ($oQuota->save()) {
                foreach ($_POST['QuotaLanguageSetting'] as $language => $settingAttributes) {
                    $oQuotaLanguageSetting = new QuotaLanguageSetting();
                    $oQuotaLanguageSetting->attributes = $settingAttributes;
                    $oQuotaLanguageSetting->quotals_quota_id = $oQuota->primaryKey;
                    $oQuotaLanguageSetting->quotals_language = $language;

                    //Clean XSS - Automatically provided by CI
                    $oQuotaLanguageSetting->quotals_message = html_entity_decode($oQuotaLanguageSetting->quotals_message, ENT_QUOTES, "UTF-8");
                    // Fix bug with FCKEditor saving strange BR types
                    $oQuotaLanguageSetting->quotals_message = fixCKeditorText($oQuotaLanguageSetting->quotals_message);
                    $oQuotaLanguageSetting->save(false);

                    if (!$oQuotaLanguageSetting->validate()) {
                        $oQuota->addErrors($oQuotaLanguageSetting->getErrors());
                    }
                }
                if (!$oQuota->getErrors()) {
                    Yii::app()->user->setFlash('success', gT("New quota saved"));
                    self::redirectToIndex($iSurveyId);
                } else {
                    // if any of the parts fail to save we delete the quota and and try again
                    $oQuota->delete();
                }
            }
        }

        $aData['oQuota'] = $oQuota;
        $aData['oSurvey'] = $oSurvey;
        // create QuotaLanguageSettings
        foreach ($oSurvey->getAllLanguages() as $language) {
            $oQuotaLanguageSetting = new QuotaLanguageSetting();
            $oQuotaLanguageSetting->quotals_name = $oQuota->name;
            $oQuotaLanguageSetting->quotals_quota_id = $oQuota->primaryKey;
            $oQuotaLanguageSetting->quotals_language = $language;
            $oQuotaLanguageSetting->quotals_url = $oSurvey->languagesettings[$language]->surveyls_url;
            $siteLanguage = Yii::app()->language;
            // Switch language temporarily to get the default text in right language
            Yii::app()->language = $language;
            $oQuotaLanguageSetting->quotals_message = gT("Sorry your responses have exceeded a quota on this survey.");
            Yii::app()->language = $siteLanguage;
            $aData['aQuotaLanguageSettings'][$language] = $oQuotaLanguageSetting;
        }

        Yii::app()->loadHelper('admin.htmleditor');
        $this->renderWrappedTemplate('quotas', 'newquota_view', $aData);
    }

    /**
     * Get Quota Answers
     * @param integer $iQuestionId
     * @param integer $iSurveyId
     * @param integer $iQuotaId
     * @return array
     */
    public function getQuotaAnswers(int $iQuestionId, int $iSurveyId, int $iQuotaId)
    {
        $iQuestionId = sanitize_int($iQuestionId);
        $iSurveyId   = sanitize_int($iSurveyId);
        $iQuotaId    = sanitize_int($iQuotaId);
        $aData       = $this->getData($iSurveyId);
        $sBaseLang   = $aData['sBaseLang'];
        $this->checkPermissions($iSurveyId, 'read');
        $oSurvey = Survey::model()->findByPk($iSurveyId);

        $aQuestion = Question::model()
            ->with('questionl10ns', array('language' => $sBaseLang))
            ->findByPk(array('qid' => $iQuestionId));
        $aQuestionType = $aQuestion['type'];

        if ($aQuestionType == Question::QT_M_MULTIPLE_CHOICE) {
            $aResults = Question::model()
                ->with('questionl10ns', array('language' => $sBaseLang))
                ->findAllByAttributes(array('parent_qid' => $iQuestionId));
            $aAnswerList = array();

            foreach ($aResults as $oDbAnsList) {
                $tmparrayans = array('Title' => $aQuestion['title'], 'Display' => substr($oDbAnsList->questionl10ns[$sBaseLang]->question, 0, 40), 'code' => $oDbAnsList->title);
                $aAnswerList[$oDbAnsList->title] = $tmparrayans;
            }
        } elseif ($aQuestionType == Question::QT_G_GENDER) {
            $aAnswerList = array(
                'M' => array('Title' => $aQuestion['title'], 'Display' => gT("Male"), 'code' => 'M'),
                'F' => array('Title' => $aQuestion['title'], 'Display' => gT("Female"), 'code' => 'F'));
        } elseif ($aQuestionType == Question::QT_L_LIST || $aQuestionType == Question::QT_O_LIST_WITH_COMMENT || $aQuestionType == Question::QT_EXCLAMATION_LIST_DROPDOWN) {
            $aAnsResults = Answer::model()
                ->with('answerl10ns', array('language' => $sBaseLang))
                ->findAllByAttributes(array('qid' => $iQuestionId));

            $aAnswerList = array();

            foreach ($aAnsResults as $aDbAnsList) {
                $aAnswerList[$aDbAnsList['code']] = array('Title' => $aQuestion['title'], 'Display' => $aDbAnsList->answerl10ns[$sBaseLang]->answer, 'code' => $aDbAnsList['code']);
            }
        } elseif ($aQuestionType == Question::QT_A_ARRAY_5_POINT) {
            $aAnsResults = Question::model()
                ->with('questionl10ns', array('language' => $sBaseLang))
                ->findAllByAttributes(array('parent_qid' => $iQuestionId));

            $aAnswerList = array();

            foreach ($aAnsResults as $aDbAnsList) {
                for ($x = 1; $x < 6; $x++) {
                    $tmparrayans = array('Title' => $aQuestion['title'], 'Display' => substr($aDbAnsList->questionl10ns[$sBaseLang]->question, 0, 40) . ' [' . $x . ']', 'code' => $aDbAnsList['title']);
                    $aAnswerList[$aDbAnsList['title'] . "-" . $x] = $tmparrayans;
                }
            }
        } elseif ($aQuestionType == Question::QT_B_ARRAY_10_CHOICE_QUESTIONS) {
            $aAnsResults = Question::model()
                ->with('questionl10ns', array('language' => $sBaseLang))
                ->findAllByAttributes(array('parent_qid' => $iQuestionId));

            $aAnswerList = array();

            foreach ($aAnsResults as $aDbAnsList) {
                for ($x = 1; $x < 11; $x++) {
                    $tmparrayans = array('Title' => $aQuestion['title'], 'Display' => substr($aDbAnsList->questionl10ns[$sBaseLang]->question, 0, 40) . ' [' . $x . ']', 'code' => $aDbAnsList['title']);
                    $aAnswerList[$aDbAnsList['title'] . "-" . $x] = $tmparrayans;
                }
            }
        } elseif ($aQuestionType == Question::QT_Y_YES_NO_RADIO) {
            $aAnswerList = array(
                'Y' => array('Title' => $aQuestion['title'], 'Display' => gT("Yes"), 'code' => 'Y'),
                'N' => array('Title' => $aQuestion['title'], 'Display' => gT("No"), 'code' => 'N'));
        } elseif ($aQuestionType == Question::QT_I_LANGUAGE) {
            $slangs = $oSurvey->allLanguages;

            foreach ($slangs as $key => $value) {
                $tmparrayans = array('Title' => $aQuestion['title'], 'Display' => getLanguageNameFromCode($value, false), $value);
                $aAnswerList[$value] = $tmparrayans;
            }
        }

        if (empty($aAnswerList)) {
            return array();
        } else {
            // Now we mark answers already used in this quota as such
            $aExistsingAnswers = QuotaMember::model()->findAllByAttributes(array('sid' => $iSurveyId, 'qid' => $iQuestionId, 'quota_id' => $iQuotaId));
            foreach ($aExistsingAnswers as $aAnswerRow) {
                if (array_key_exists($aAnswerRow['code'], $aAnswerList)) {
                    $aAnswerList[$aAnswerRow['code']]['rowexists'] = '1';
                }
            }
            return $aAnswerList;
        }
    }

    /**
     * Renders template(s) wrapped in header and footer
     *
     * @param string       $sAction     Current action, the folder to fetch views from. Default is 'quotas'.
     * @param string|array $aViewUrls   View url(s)
     * @param array        $aData       Data to be passed on. Optional.
     * @param bool         $sRenderFile Default is false.
     */
    protected function renderWrappedTemplate($sAction = 'quotas', $aViewUrls = array(), $aData = array(), $sRenderFile = false)
    {
        App()->getClientScript()->registerScriptFile(App()->getConfig('adminscripts') . 'quotas.js');
        parent::renderWrappedTemplate($sAction, $aViewUrls, $aData, $sRenderFile);
    }
}
