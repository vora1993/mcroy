<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class CreditCardController extends AbstractActionController
{
    public function indexAction()
    {
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
      $credit_cards = $application_model_credit_card->fetchAll();
      $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
      $banks = $application_model_bank->fetchAll();
      return array("credit_cards" => $credit_cards, "banks" => $banks);
    }
}