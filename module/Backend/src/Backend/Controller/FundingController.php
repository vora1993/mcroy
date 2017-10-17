<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class FundingController extends AbstractActionController
{
    public function indexAction() {
        $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
        $loans = $application_model_business_loan->fetchAll(array("type" => "peertopeer_funding"));
        return array("loans" => $loans);
    }
    
    public function setStatusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
            
            $action = $this->params()->fromPost('action');
            switch ($action) {
                case 'approved':
                    $status = 1;
                break;
                
                case 'cancelled':
                    $status = 2;
                break;
                
                case 'rejected':
                    $status = 3;
                break;
                
                case 'pending':
                    $status = 0;
                break;
            }
            $ids = $this->params()->fromPost('ids');
            $error = 0;
            $result = array();
            foreach ($ids as $id) {
                $user = $application_model_business_loan->fetchRow(array('id' => $id));
                $user->setId($id);
                $user->setStatus($status);
                $user->setDateModified(new Expression('NOW()'));
                
                $updated = $application_model_business_loan->update($user);
                if($updated) {
                    if($status == 1) {
                        $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                        $referral = $application_model_referral->fetchRow(array("application" => $id));
                        if($referral && $referral->getStatus() == 0) {
                            $referral->setId($referral->getId());
                            $referral->setStatus(1);
                            $referral->setDateAdded(new Expression('NOW()'));
                            $application_model_referral->update($referral);
                        }
                    }
                } else {
                    $error = $error + 1;
                }
            }
            if(!$error) {
                $result['success'] = true;
                $result['msg'] = $translator->translate("Successfully updated");
            } else {
                $result['success'] = false;
                $result['msg'] = $translator->translate("Something error. Please check");
            }
        }
        return new JsonModel($result);
    }
}