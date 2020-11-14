<?php

class ecm_smssendersmsquestionModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();

        if (((bool) Tools::isSubmit('submitSMSAnswer')) == true) {
            $this->module->addSMSQuestionList();
        }
        $arr_question = $this->module->getSMSQuestionList();
        $arr_answer = $this->module->getSMSAnswerList();
        $this->context->smarty->assign(
            array(
                'action_path' => __PS_BASE_URI__.'smsquestion',
                'arr_question' => $arr_question,
                'arr_answer' => $arr_answer,
                'id_order' => $_GET['id_order']
            )
        );

        $this->setTemplate('module:ecm_smssender/views/templates/front/smsquestionlist.tpl');
    }
}