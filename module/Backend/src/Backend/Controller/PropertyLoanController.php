<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class PropertyLoanController extends AbstractActionController
{
    public function indexAction() {
        $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
        $loans = $application_model_property_loan_package->fetchAll();
        return array("loans" => $loans);
    }
    
    public function homeLoanAction() {
        $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
        $loans = $application_model_property_loan->fetchAll(array("type" => "home_loan"));
        return array("loans" => $loans);
    }
    
    public function refinancingAction() {
        $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
        $loans = $application_model_property_loan->fetchAll(array("type" => "refinancing"));
        return array("loans" => $loans);
    }

    public function totalCostsOutlayAction()
    {
        $application_model_property_cost_out_play = $this->getServiceLocator()->get('application_model_property_cost_out_play');
        $loans = $application_model_property_cost_out_play->fetchRow();
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $loans->setMortgageStampDuty($post['mortgage_stamp_duty']);
            $loans->setValuationFee($post['valuation_fee']);
            $loans->setLegalFee($post['legal_fee']);
            $loans->setFireInsurance($post['fire_insurance']);
            $updated = $application_model_property_cost_out_play->update($loans);
            if($updated) {
                $messages['success'] = true;
                $messages['msg'] = $translator->translate("Successfully updated");
            } else {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Something error. Please check");
            }
            
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("loans"=>$loans);
    }
    
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            if((int)$post['int_year_2']<=0)
            {
                $response = $this->getResponse();
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Interest 2 year must is greater than 0");
                $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                return $response;
            }
            
            $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
            $loan = new \Application\Entity\PropertyLoanPackage;
            $loan->setTitle($post['title']);
            $loan->setPromotions($post['promotions']);
            $loan->setMinLoanAmount($post['min_loan_amount']);
            $loan->setCategoryId($post['category_id']);
            $loan->setProperty($post['property']);
            $loan->setBankId($post['bank_id']);
            $loan->setDateAdded(new Expression('NOW()'));
            if($post['check_property_buy']=='residential')
            {
                $loan->setType($post['property_type_residential']);
            }else
            {
                $loan->setType($post['property_type']);
            }            
            $loan->setTypeOfCorporate($post['type_of_corporate']);
            $loan->setPropertyStatus($post['property_status']);
            $loan->setPackage($post['property_package']);
            if($post['property_package'] === 'Floating') {
                $loan->setFloatingType($post['floating_type']);
            } else {
                $loan->setFloatingType('');
            }
            if($post['lock_in_year'] > 0){
                $lock_in_year = $post['lock_in_year'];
            } else {
                $lock_in_year = 0;
            }
            $loan->setLockInYear($lock_in_year);
            $loan->setLegalSubsidy($post['legal_subsidy']);
            $loan->setLegalFeeSubsidy($post['legal_fee_subsidy']);
            $loan->setValuationSubsidy($post['valuation_subsidy']);
            $loan->setFireInsuranceSubsidy($post['fire_insurance_subsidy']);
            $loan->setSubsidyComment($post['subsidy_comment']);
            $loan->setClawback($post['clawback']);
            $loan->setValuationFee($post['valuation_fee']);
            $loan->setLatePaymentFee($post['late_payment_fee']);
            $loan->setEarlyRepaymentFee($post['early_repayment_fee']);
            $loan->setCancellationFee($post['cancellation_fee']);
            $loan->setPreferredFire($post['preferred_fire']);
            $loan->setAdminFee($post['admin_fee']);
            $loan->setIntYear1($post['int_year_1']);
            $loan->setRemarkYear1($post['remark_year_1']);
            $loan->setIntYear2($post['int_year_2']);
            $loan->setRemarkYear2($post['remark_year_2']);
            $loan->setIntYear3($post['int_year_3']);
            $loan->setRemarkYear3($post['remark_year_3']);
            $loan->setIntYear4($post['int_year_4']);
            $loan->setRemarkYear4($post['remark_year_4']);
            $loan->setRemark($post['remark']);
            $loan->setStatus($post['status']);
            $added = $application_model_property_loan_package->insert($loan);
            if($added) {
                $messages['success'] = true;
                $messages['msg'] = $translator->translate("Successfully added");
            } else {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Something error. Please check");
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }
    
    public function editAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
        $loan = $application_model_property_loan_package->fetchRow(array('id' => $id));
        if($loan) {
            $translator = $this->getServiceLocator()->get('translator');
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                if((int)$post['int_year_2']<=0)
                {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Interest 2 year must is greater than 0");
                    $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                    return $response;
                }
                $loan->setTitle($post['title']);
                $loan->setPromotions($post['promotions']);
                $loan->setMinLoanAmount($post['min_loan_amount']);
                $loan->setProperty($post['property']);
                $loan->setBankId($post['bank_id']);
                $loan->setCategoryId($post['category_id']);
                $loan->setDateModified(new Expression('NOW()'));
                if($post['check_property_buy']=='residential')
                {
                    $loan->setType($post['property_type_residential']);
                }else
                {
                    $loan->setType($post['property_type']);
                }  
                // $loan->setType($post['property_type']);
                $loan->setTypeOfCorporate($post['type_of_corporate']);
                $loan->setPropertyStatus($post['property_status']);
                $loan->setPackage($post['property_package']);
                if($post['property_package'] === 'Floating') {
                    $loan->setFloatingType($post['floating_type']);
                } else {
                    $loan->setFloatingType('');
                }
                if($post['lock_in_year'] > 0){
                    $lock_in_year = $post['lock_in_year'];
                } else {
                    $lock_in_year = 0;
                }
                $loan->setLockInYear($lock_in_year);
                $loan->setLegalSubsidy($post['legal_subsidy']);
                $loan->setLegalFeeSubsidy($post['legal_fee_subsidy']);
                $loan->setValuationSubsidy($post['valuation_subsidy']);
                $loan->setFireInsuranceSubsidy($post['fire_insurance_subsidy']);
                $loan->setSubsidyComment($post['subsidy_comment']);
                $loan->setClawback($post['clawback']);
                $loan->setValuationFee($post['valuation_fee']);
                $loan->setLatePaymentFee($post['late_payment_fee']);
                $loan->setEarlyRepaymentFee($post['early_repayment_fee']);
                $loan->setCancellationFee($post['cancellation_fee']);
                $loan->setPreferredFire($post['preferred_fire']);
                $loan->setAdminFee($post['admin_fee']);
                $loan->setIntYear1($post['int_year_1']);
                $loan->setRemarkYear1($post['remark_year_1']);
                $loan->setIntYear2($post['int_year_2']);
                $loan->setRemarkYear2($post['remark_year_2']);
                $loan->setIntYear3($post['int_year_3']);
                $loan->setRemarkYear3($post['remark_year_3']);
                $loan->setIntYear4($post['int_year_4']);
                $loan->setRemarkYear4($post['remark_year_4']);
                $loan->setRemark($post['remark']);
                $loan->setStatus($post['status']);
                $updated = $application_model_property_loan_package->update($loan);
                if($updated) {
                    $messages['success'] = true;
                    $messages['msg'] = $translator->translate("Successfully updated");
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Something error. Please check");
                }
                
                $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                return $response;
            }
            
            return array('loan' => $loan);
        } else {
            return $this->redirect()->toRoute("admin/property_loan"); 
        }
    }
    
    public function individualAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
        $loan = $application_model_property_loan->fetchRow(array('id' => $id)); 
        return array('loan' => $loan);
    }
    
    public function faqForCommercialAction() {
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array('type' => 'faq_for_commercial'));
        if($faq) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $id = $faq->getId();
                $post = $request->getPost();
                $error = 0;
                if(!$error) {
                    $faq->setId($id);
                    $_faq = $post['faq'];
                    if(count($_faq) > 0) {
                        $loan_tenure = array();
                        foreach ($_faq as $key => $value) {
                            $question = $value['question'];
                            $answer = $value['answer'];
                            $loan_tenure[] = array(
                                'question'  => $question,
                                'answer' => $answer
                            );
                        }
                        $faq->setQuestion(\Zend\Json\Json::encode($loan_tenure));
                        $faq->setAnswer(\Zend\Json\Json::encode($loan_tenure));
                    }
                    
                    $edited = $application_model_faq->update($faq);
                    if($edited) {
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
            
            return array('faq' => $faq);
        } 
    }

    public function faqForResidentialAction() {
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array('type' => 'faq_for_residential'));
        if($faq) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $id = $faq->getId();
                $post = $request->getPost();
                $error = 0;
                if(!$error) {
                    $faq->setId($id);
                    $_faq = $post['faq'];
                    if(count($_faq) > 0) {
                        $loan_tenure = array();
                        foreach ($_faq as $key => $value) {
                            $question = $value['question'];
                            $answer = $value['answer'];
                            $loan_tenure[] = array(
                                'question'  => $question,
                                'answer' => $answer
                            );
                        }
                        $faq->setQuestion(\Zend\Json\Json::encode($loan_tenure));
                        $faq->setAnswer(\Zend\Json\Json::encode($loan_tenure));
                    }
                    
                    $edited = $application_model_faq->update($faq);
                    if($edited) {
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
            
            return array('faq' => $faq);
        } 
    }

    public function faqForAlternativeAction() {
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array('type' => 'faq_for_alternative'));
        if($faq) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $id = $faq->getId();
                $post = $request->getPost();
                $error = 0;
                if(!$error) {
                    $faq->setId($id);
                    $_faq = $post['faq'];
                    if(count($_faq) > 0) {
                        $loan_tenure = array();
                        foreach ($_faq as $key => $value) {
                            $question = $value['question'];
                            $answer = $value['answer'];
                            $loan_tenure[] = array(
                                'question'  => $question,
                                'answer' => $answer
                            );
                        }
                        $faq->setQuestion(\Zend\Json\Json::encode($loan_tenure));
                        $faq->setAnswer(\Zend\Json\Json::encode($loan_tenure));
                    }
                    
                    $edited = $application_model_faq->update($faq);
                    if($edited) {
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
            
            return array('faq' => $faq);
        } 
    }
    
    /**
     * Category
     */
    public function categoryAction() {
        $application_model_category = $this->getServiceLocator()->get('application_model_category');
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator  = $this->getServiceLocator()->get('translator');
            $source      = $this->params()->fromPost('source');
            $destination = $this->params()->fromPost('destination', 0);
            
            $category = $application_model_category->fetchRow(array('id' => $source));
            $category->setId($source);
            $category->setParentId($destination);
            $application_model_category->update($category);
            
            $ordering       = \Zend\Json\Json::decode($this->params()->fromPost('order'));
	        $rootOrdering   = \Zend\Json\Json::decode($this->params()->fromPost('rootOrder'));
            
            if($ordering) {
                foreach ($ordering as $order => $item_id) {
                    $order = $order + 1;
                    $itemToOrder = $application_model_category->fetchRow(array('id' => $item_id));
                    if($itemToOrder) {
                        $itemToOrder->setId($item_id);
                        $itemToOrder->setSortOrder($order);
                        $application_model_category->update($itemToOrder);
                    }
                }
            } else {
                foreach($rootOrdering as $order => $item_id){
                    $order = $order + 1;
                    $itemToOrder = $application_model_category->fetchRow(array('id' => $item_id));
    	            if($itemToOrder){
                        $itemToOrder->setId($item_id);
                        $itemToOrder->setSortOrder($order);
    	                $application_model_category->update($itemToOrder);
                    }
                }
    	    }
            return $response->setContent ( \Zend\Json\Json::encode ( array("success" => true, "msg" => $translator->translate("Successfully update")) ) );
        }
        $categories     = $application_model_category->fetchAll(array("type" => "property_loan"))->toArray();
        $category_html  = $this->buildCategory($categories);
        
        return array("category_html" => $category_html);
    }
    
    public function buildCategory($categories, $id_parent = 0) {
        $html = "";
        $translator = $this->getServiceLocator()->get('translator');
        $category_tmp = array();
        $categories = $categories;
        foreach ($categories as $key => $item) {
            if ((int) $item['parent_id'] == (int) $id_parent) {
                $category_tmp[] = $item;
                unset($categories[$key]);
            }
        }
        if ($category_tmp) 
        {
            $html .= '<ol class="dd-list" id="accordion">';
            foreach ($category_tmp as $item) 
            {
                $id = $item['id'];
                $label = $item['name'];
                $sort_order = $item['sort_order'];
                $html .= "<li class='dd-item nested-list-item panel' style='border: 0;' data-order='{$sort_order}' data-id='{$id}'>";
                $html .= "<div class='dd-handle nested-list-handle'><span class='glyphicon glyphicon-move'></span></div>";
                $html .= "<div class='nested-list-content'>{$label}<div class='pull-right'><a href='#collapse_{$id}' class='accordion-toggle accordion-toggle-styled collapsed' data-toggle='collapse' data-parent='#accordion'><i class='fa fa-edit'></i></a></div></div>";
                $html .= "<div id='collapse_{$id}' class='panel-collapse collapse'>";
                $html .= "<div class='panel-body'>";
                $html .= "<div class='row'><div class='col-md-12'><label class='control-label'>Navigation Label</label>";
                $html .= "<input type='text' class='form-control' name='label_title' value='{$label}'></div></div>";
                $html .= "<div class='row' style='margin-top: 15px;'><div class='col-md-6 text-left'><button type='button' class='btn red-thunderbird ladda-button' onclick='edit_category(this, {$id})' data-style='expand-left'><i class='fa fa-check'></i> ".$translator->translate("Update")."</button></div><div class='col-md-6 text-right'><button type='button' class='btn purple ladda-button' onclick='remove_category(this, {$id})' data-style='expand-left'><i class='fa fa-trash'></i> ".$translator->translate("Remove")."</button></div></div>";
                $html .= "</div></div>";
                $html .= $this->buildCategory($categories, $id);
                $html .= "</li>";
            }
            $html .= '</ol>';
        }
        
        return $html;
    }
    
    public function createCategoryAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_category_group = $this->getServiceLocator()->get('application_model_category_group');
            
            $name = $this->params()->fromPost('name');
            $category_group = $application_model_category_group->fetchRow(array('name' => $name));
            if($category_group) {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("The category name $name conflicts with another category name. Please try another");
            } else {
                $category_group = new \Application\Entity\CategoryGroup;
                $category_group->setName($name);
                $category_group->setIsDefault(0);
                $category_group->setSortOrder(2);
                $category_group->setDateModified(new Expression('NOW()'));
                $category_group->setStatus(1);
                    
                $added = $application_model_category_group->insert($category_group);
                if($added) {
                    $messages['success'] = true;
                    $messages['msg'] = $translator->translate("Successfully added");
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Something error. Please check");
                }
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function addCategoryAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_category = $this->getServiceLocator()->get('application_model_category');            
            $name     = $this->params()->fromPost('name');

            $category = new \Application\Entity\Category;
            $category->setName($name);
            $category->setSeo('');
            $category->setSortOrder(0);
            $category->setType("property_loan");
            $category->setDateAdded(new Expression('NOW()'));
            $category->setParentId(0);
            $category->setStatus(1);
                    
            $added = $application_model_category->insert($category);
            if($added) {
                $messages['success'] = true;
                $messages['msg'] = $translator->translate("Successfully added");
            } else {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Something error. Please check");
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function editCategoryAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_category = $this->getServiceLocator()->get('application_model_category');
            
            $category_id   = $this->params()->fromPost('category_id');
            $category_name = $this->params()->fromPost('category_name');
            
            $category = $application_model_category->fetchRow(array('id' => $category_id));
            if($category) {
                $category->setId($category_id);
                $category->setName($category_name);
                $category->setDateModified(new Expression('NOW()'));
                    
                $updated = $application_model_category->update($category);
                if($updated) {
                    $messages['success'] = true;
                    $messages['msg'] = $translator->translate("Successfully updated");
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Something error. Please check");
                }
            } else {
                $messages['success'] = false;
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function removeCategoryAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_category = $this->getServiceLocator()->get('application_model_category');
            
            $category_id   = $this->params()->fromPost('category_id');
            $deleted = $application_model_category->delete(array('id' => $category_id));
            if($deleted) {
                $messages['success'] = true;
                $messages['msg'] = $translator->translate("Successfully removed");
            } else {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Something error. Please check");
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }

    public function commercialAndIndustrialAction()
    {
        $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
        $loans = $application_model_property_loan_package->fetchAll();
        return array("loans" => $loans);
    }

    public function residentialAction()
    {
        $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
        $loans = $application_model_property_loan_package->fetchAll();
        return array("loans" => $loans);
    }
}