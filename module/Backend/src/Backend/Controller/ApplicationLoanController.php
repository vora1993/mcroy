<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class ApplicationLoanController extends AbstractActionController
{
    public function businessLoanAction() {
        $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
        $loans = $application_model_business_loan->fetchAll(array("type" => "business_loan"));
        return array("loans" => $loans);
    }
    
    public function propertyLoanAction() {
        $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
        $loans = $application_model_property_loan->fetchAll(array("type" => "property_loan"));
        return array("loans" => $loans);
    }
}