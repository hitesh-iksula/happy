<?php

class Payu_PayuCheckout_Block_Shared_Redirect extends Mage_Core_Block_Abstract
{
    protected function _toHtml()
    {
        $shared = $this->getOrder()->getPayment()->getMethodInstance();

        $form = new Varien_Data_Form();
        $form->setAction($shared->getPayuCheckoutSharedUrl())
            ->setId('payucheckout_shared_checkout')
            ->setName('payucheckout_shared_checkout')
            ->setMethod('POST')
            ->setUseContainer(true);
        foreach ($shared->getFormFields() as $field=>$value) {
            $form->addField($field, 'hidden', array('name'=>$field, 'value'=>$value));
        }

        $html  = '<html><head><meta id="viewport" name="viewport" content="initial-scale=1.0; maximum-scale=1.0; user-scalable=0;"></head>';
        $html .= '<body style="text-align: center; padding-top: 80px;">';
        $html .= $this->__('You will be redirected to PayuCheckout in a few seconds.');
        $html .= $form->toHtml();
        $html .= '<script type="text/javascript">document.getElementById("payucheckout_shared_checkout").submit();</script>';
        $html .= '</body></html>';

        return $html;
    }
}