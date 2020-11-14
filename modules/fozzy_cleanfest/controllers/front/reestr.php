<?php

class fozzy_cleanfestreestrModuleFrontController extends ModuleFrontController
{
    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        parent::initContent();
        
        $return = array();
        
        if (((bool) Tools::isSubmit('reg_button')) == true) {
        $date_in = explode("-", Tools::GetValue('fiskal_date'));
        $date_to = $date_in[2]."-".$date_in[1]."-".$date_in[0];
        $data = array(
          'firstname' => Tools::GetValue('firstname'),
          'lastname' => Tools::GetValue('lastname'),
          'phone' => Tools::GetValue('phone'),
          'email' => Tools::GetValue('email'),
          'fiskal_num' => Tools::GetValue('fiskal_num'),
          'fiskal_date' => $date_to,
          'pravila' => Tools::GetValue('pravila')
        );
         $return = $this->module->add_fiskal($data);
        }
    //    $arr_question = $this->module->getSMSQuestionList();
    //    $arr_answer = $this->module->getSMSAnswerList();
        $this->context->smarty->assign(
            array(
                'action_path' => __PS_BASE_URI__.'reestr',
                'return' => $return,
            )
        );

        $this->setTemplate('module:fozzy_cleanfest/views/templates/front/form.tpl');
    }
}