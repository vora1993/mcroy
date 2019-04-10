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

    public function viewReferralSummaryAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
        $referrals = $application_model_referral->fetchAll();
        return array("referrals" => $referrals);
    }

    public function editReferralAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
        $referral = $application_model_referral->fetchRow(array('id' => $id));
        if($referral) {
            $translator = $this->getServiceLocator()->get('translator');

            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                $error = 0;
                if($referral->getStatus() == 4) {
                    $messages['success'] = false;
                    $messages['msg']     = $translator->translate("You can't paid multiple for this loan");
                    $error = $error + 1;
                }

                if(!$error) {
                    $referral->setId($id);
                    $referral->setCredit($post['credit']);
                    $referral->setDateModified(new Expression('NOW()'));
                    $referral->setStatus(4);
                    $edited = $application_model_referral->update($referral);
                    if($edited) {
                        // Update User Credit
                        $application_model_user_credit = $this->getServiceLocator()->get('application_model_user_credit');
                        $user_credit = new \Application\Entity\UserCredit;
                        $user_credit->setUserId($referral->getUserId());
                        $user_credit->setCredit($post['credit']);
                        $user_credit->setRefId($id);
                        $user_credit->setDateAdded(new Expression('NOW()'));
                        $application_model_user_credit->insert($user_credit);

                        $messages['success'] = true;
                        $messages['msg']     = $translator->translate("Successfully updated");
                    } else {
                        $messages['success'] = false;
                        $messages['msg']     = $translator->translate("Something error. Please check");
                    }
                }
                $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                return $response;
            }
            return array('referral' => $referral);
        } else {
            return $this->redirect()->toRoute("admin/application_loan", array("action" => "view-referral-summary"));
        }
    }

    public function setStatusReferralAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_referral = $this->getServiceLocator()->get('application_model_referral');

            $action = $this->params()->fromPost('action');
            switch ($action) {
                case 'paid':
                    $status = 4;
                break;
            }
            $ids = $this->params()->fromPost('ids');
            $error = 0;
            $result = array();
            foreach ($ids as $id) {
                $referral = $application_model_referral->fetchRow(array('id' => $id));
                $referral->setId($id);
                $referral->setStatus($status);

                $updated = $application_model_referral->update($referral);
                if(!$updated) $error = $error + 1;
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