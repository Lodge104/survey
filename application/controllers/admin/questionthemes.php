<?php

class questionthemes extends Survey_Common_Action
{
    /**
     * @param string $id
     *
     * @throws CHttpException
     */
    public function toggleVisibility($id)
    {
        if (!Permission::model()->hasGlobalPermission('templates', 'update')) {
            throw new CHttpException(403, gT("We are sorry but you don't have permissions to do this."));
        }
        $aQuestionThemes = QuestionTheme::model()->findAllByAttributes([], 'id = :id', ['id' => $id]);

        /** @var QuestionTheme $oQuestionTheme */
        foreach ($aQuestionThemes as $oQuestionTheme) {
            if ($oQuestionTheme->visible == 'Y') {
                $oQuestionTheme->setAttribute('visible', 'N');
            } else {
                $oQuestionTheme->setAttribute('visible', 'Y');
            }
            $oQuestionTheme->save();
        }
    }
}
