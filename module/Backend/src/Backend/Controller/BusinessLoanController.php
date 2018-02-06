<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class BusinessLoanController extends AbstractActionController
{
    public function indexAction() {
        $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
        $loans = $application_model_business_loan_package->fetchAll(array("status" => array(0,1,2,3)));
        return array("loans" => $loans);
    }
    
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            if($post['int_rate']<=0)
            {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Interest Rate must greater than 0");
                $response = $this->getResponse();
                $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                return $response;
            }
            
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $loan = new \Application\Entity\Loan;
            $loan->setBankId($post['bank_id']);
            $loan->setType($post['type']);
            $loan->setCategoryId($post['category_id']);
            $loan->setLoanTitle($post['loan_title']);
            if($post['promotions']) $loan->setPromotions($this->clearHtml($post['promotions']));
            $loan->setBenefit($post['benefit']);
            $loan->setMaxLoanAmount($post['max_loan_amount']);
            $loan->setMaxTenor($post['max_tenor']);
            $loan->setProcessingFee($post['processing_fee']);
            $loan->setAnnualFee($post['annual_fee']);
            $loan->setPenaltyFee($post['penalty_fee']);
            $loan->setLockInPeriod($post['lock_in_period']);
            $loan->setMinSalesTurnover($post['min_sales_turnover']);
            $loan->setMinYearsOfIncorporation($post['min_years_of_incorporation']);
            $loan->setUrl($post['url']);
            $loan->setDateAdded(new Expression('NOW()'));
            $loan->setIntRate($post['int_rate']);
            $loan->setMaxTenure($post['max_tenure']);
            $loan->setMaxLoanAmt($post['max_loan_amt']);
            $loan->setPrepaymentPenaltyFee($post['prepayment_penalty_fee']);
            $loan->setRestructuringOfLoanTenor($post['restructuring_of_loan_tenor']);
            $loan->setMinTurnover($post['min_turnover']);
            $loan->setMinYearsIncorporation($post['min_years_incorporation']);
            $loan->setMinAge($post['min_age']);
            $loan->setBankruptcy($post['bankruptcy']);
            $loan->setStatus($post['status']);
            
            // Calculate
            $interest_rate = $post['interest-rate'];
            if(count($interest_rate) > 0) {
                $loan_tenure = array();
                foreach ($interest_rate as $key => $value) {
                    $condition = $value['condition'];
                    $percentage = $value['percentage'];
                    $loan_tenure[] = array(
                        'condition'  => $condition,
                        'percentage' => $percentage
                    );
                }
                $loan->setInterestRate(\Zend\Json\Json::encode($loan_tenure));
            }
            
            $added = $application_model_business_loan_package->insert($loan);
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
        $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
        $loan = $application_model_business_loan_package->fetchRow(array('id' => $id));
        if($loan) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                if($post['int_rate']<=0)
                {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Interest Rate must greater than 0");
                    $response = $this->getResponse();
                    $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                    return $response;
                }
                $error = 0;
                if(!$error) {
                    $loan->setId($id);
                    $loan->setBankId($post['bank_id']);
                    $loan->setType($post['type']);
                    $loan->setCategoryId($post['category_id']);
                    $loan->setLoanTitle($post['loan_title']);
                    if($post['promotions']) $loan->setPromotions($this->clearHtml($post['promotions']));
                    $loan->setBenefit($post['benefit']);
                    $loan->setMaxLoanAmount($post['max_loan_amount']);
                    $loan->setMaxTenor($post['max_tenor']);
                    $loan->setProcessingFee($post['processing_fee']);
                    $loan->setAnnualFee($post['annual_fee']);
                    $loan->setPenaltyFee($post['penalty_fee']);
                    $loan->setLockInPeriod($post['lock_in_period']);
                    $loan->setMinSalesTurnover($post['min_sales_turnover']);
                    $loan->setMinYearsOfIncorporation($post['min_years_of_incorporation']);
                    $loan->setUrl($post['url']);
                    $loan->setIntRate($post['int_rate']);
                    $loan->setMaxTenure($post['max_tenure']);
                    $loan->setMaxLoanAmt($post['max_loan_amt']);
                    $loan->setPrepaymentPenaltyFee($post['prepayment_penalty_fee']);
                    $loan->setRestructuringOfLoanTenor($post['restructuring_of_loan_tenor']);
                    $loan->setMinTurnover($post['min_turnover']);
                    $loan->setMinYearsIncorporation($post['min_years_incorporation']);
                    $loan->setMinAge($post['min_age']);
                    $loan->setBankruptcy($post['bankruptcy']);
                    $loan->setDateModified(new Expression('NOW()'));
                    $loan->setStatus($post['status']);
                    
                    // Calculate
                    $interest_rate = $post['interest-rate'];
                    if(count($interest_rate) > 0) {
                        $loan_tenure = array();
                        foreach ($interest_rate as $key => $value) {
                            $condition = $value['condition'];
                            $percentage = $value['percentage'];
                            $loan_tenure[] = array(
                                'condition'  => $condition,
                                'percentage' => $percentage
                            );
                        }
                        $loan->setInterestRate(\Zend\Json\Json::encode($loan_tenure));
                    }
                    
                    $edited = $application_model_business_loan_package->update($loan);
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
            
            return array('loan' => $loan);
        } else {
            return $this->redirect()->toRoute("admin/view-business-loan"); 
        }
    }
    
    public function faqAction() {
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array('type' => 'business_loan'));
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
    
    public function faq6Action() {
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array('type' => 'government_assisted_loan'));
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
    
    public function statusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_bank_account_package = $this->getServiceLocator()->get('application_model_bank_account_package');
            $action = $this->params()->fromPost('action');
            switch ($action) {
                case 'active':
                    $status = 1;
                break;
                
                case 'trash':
                    $status = 4;
                break;
                
                case 'deactive':
                    $status = 0;
                break;
            }
            $ids = $this->params()->fromPost('ids');
            $error = 0;
            $result = array();
            foreach ($ids as $id) {
                $user = $application_model_bank_account_package->fetchRow(array('id' => $id));
                $user->setId($id);
                $user->setStatus($status);
                $user->setDateModified(new Expression('NOW()'));
                $updated = $application_model_bank_account_package->update($user);
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
    //Status action for list of business term loan
    public function statusBusinessAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $action = $this->params()->fromPost('action');
            switch ($action) {
                case 'active':
                    $status = 1;
                break;
                
                case 'trash':
                    $status = 4;
                break;
                
                case 'deactive':
                    $status = 0;
                break;
            }
            $ids = $this->params()->fromPost('ids');
            $error = 0;
            $result = array();
            foreach ($ids as $id) {
                $user = $application_model_business_loan_package->fetchRow(array('id' => $id));
                $user->setId($id);
                $user->setStatus($status);
                $user->setDateModified(new Expression('NOW()'));
                $updated = $application_model_business_loan_package->update($user);
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
    
    public function businessTermLoanAction() {
        $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
        $loans = $application_model_business_loan->fetchAll(array("type" => "business_term_loan"));
        return array("loans" => $loans);
    }
    
    public function individualAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
        $loan = $application_model_business_loan->fetchRow(array('id' => $id)); 
        return array('loan' => $loan);
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
            $id = $this->params()->fromPost('id');
            $credit = $this->params()->fromPost('credit');
            $result = array();
                
            $business_loan = $application_model_business_loan->fetchRow(array('id' => $id));
            $business_loan->setId($id);
            $business_loan->setStatus($status);
            $business_loan->setDateModified(new Expression('NOW()'));
                
            $updated = $application_model_business_loan->update($business_loan);
            if($updated) {
                if($action === 'approved') {
                    $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                    $referral = $application_model_referral->fetchRow(array("type" => "business_loan", "application" => $id));
                    if($referral && $referral->getStatus() == 0) {
                        $referral->setId($referral->getId());
                        $referral->setStatus($status);
                        if($credit > 0) $referral->setCredit($credit);
                        $referral->setDateAdded(new Expression('NOW()'));
                        $application_model_referral->update($referral);
                    }
                }
                $result['success'] = true;
                $result['msg'] = $translator->translate("Successfully updated");
            } else {
                $result['success'] = false;
                $result['msg'] = $translator->translate("Something error. Please check");
            }
        }
        return new JsonModel($result);
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
        $categories     = $application_model_category->fetchAll(array("type" => "business_loan"))->toArray();
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
            $category->setType("business_loan");
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
    
    function clearHtml($html) {
        $html = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $html);
        $html = preg_replace("/<div>(.*?)<\/div>/", "$1", $html);
        return $html;
    }
}