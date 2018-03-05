<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class RefinancingCommercialController extends AbstractActionController
{
    public function indexAction()
    {
      $this->layout('layout/refinancing_commercial');
      return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 1));
    }

     public function stepAction() {
        $id = $this->params()->fromRoute('id');  // From RouteMatch
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $session = new Session('refinancing_commercial');
        $request = $this->getRequest();
        $response = $this->getResponse();
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $success = false;
        switch($id){
            case 1:
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $session->offsetSet('yes_or_no',1);
                    $property_type = $post['property_type'];
                    $current_bank_name = $post['current_bank_name'];
                    $remaining_loan_amount = $post['remaining_loan_amount'];
                    $current_interest_rate = $post['current_interest_rate'];
                    $loan_tenure = $post['loan_tenure'];
                    $locked_in = $post['locked_in'];

                    if($property_type && $current_bank_name && $remaining_loan_amount) {
                        $session->offsetSet('property_type', $property_type);
                        $session->offsetSet('current_bank_name', $current_bank_name);
                        $session->offsetSet('remaining_loan_amount', $remaining_loan_amount);
                        $session->offsetSet('current_interest_rate', $current_interest_rate);
                        $session->offsetSet('loan_tenure', $loan_tenure);
                        $session->offsetSet('locked_in', $locked_in);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "step", "id" => 2), array('name' => "refinancing_commercial"));
                    } else {
                        $redirect = $router->assemble(array("action" => "step", "id" => 1), array('name' => "refinancing_commercial"));
                    }

                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            case 2:
                if(!$session->offsetExists('property_type') && !$session->offsetExists('property_status')) {
                    return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 1));
                }
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $new_loan_tenure           = $post['new_loan_tenure'];
                    $preferred_rate_package = $post['preferred_rate_package'];

                    if($new_loan_tenure && $preferred_rate_package) {
                        $session->offsetSet('new_loan_tenure', $new_loan_tenure);
                        $session->offsetSet('preferred_rate_package', $preferred_rate_package);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "step", "id" => 3), array('name' => "refinancing_commercial"));
                    } else {
                        $redirect = $router->assemble(array("action" => "step", "id" => 2), array('name' => "refinancing_commercial"));
                    }

                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            case 3:
                if(!$session->offsetExists('preferred_rate_package') && !$session->offsetExists('new_loan_tenure') && $session->offsetExists('yes_or_no')==1) {
                    return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 2));
                }
                if($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 4));
                }
                $redirect = $router->assemble(array("action" => "step", "id" => 4), array('name' => "refinancing_commercial"));
            break;

            case 4:
                if(!$session->offsetExists('property_type') && !$session->offsetExists('property_status') && $session->offsetExists('yes_or_no')==1) {
                    return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 1));
                };
                if(!$session->offsetExists('preferred_rate_package') && !$session->offsetExists('new_loan_tenure') & $session->offsetExists('yes_or_no')==1) {
                    return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 2));
                }
                if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    return $this->redirect()->toRoute("refinancing_commercial", array("action" => "step", "id" => 3));
                }

                $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_bank');
                $condition = array('property' => "refinancing", 'status' => 1);
                if($session->offsetExists('property_type')) {
                    $condition['type'] = $session->offsetGet('property_type');
                }
                if($session->offsetExists('property_status')) {
                    $condition['property_status'] = $session->offsetGet('property_status');
                }
                if($session->offsetExists('preferred_rate_package')) {
                    if(!$session->offsetGet('preferred_rate_package')=='both')
                    {
                        $condition['package'] = $session->offsetGet('preferred_rate_package');
                    }                   
                }
                if($session->offsetGet('no_lock_in_only') == 1) {
                    $condition['lock_in_year'] = 0;
                }
                if($session->offsetGet('current_bank_name')) {
                    $condition['bank_id'] = $session->offsetGet('current_bank_name');
                }
                if($session->offsetGet('yes_or_no')==0) {
                    $condition=[];
                }
                $loans = $application_model_property_loan_bank->fetchFilter($condition);
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $remaining_loan_amount              = $post['remaining_loan_amount'];
                    $loan_tenure              = $post['loan_tenure'];
                    $total_interest_for_years = $post['total_interest_for_years'];
                    $current_interest_rate=$post['current_interest_rate'];
                    $preferred_rate_package   = $post['preferred_rate_package'];
                    $no_lock_in_only          = $post['no_lock_in_only'];
                    if($remaining_loan_amount && $loan_tenure) {
                        $session->offsetSet('remaining_loan_amount', $remaining_loan_amount);
                        $session->offsetSet('loan_amount', $loan_amount);
                        $session->offsetSet('new_loan_tenure', $loan_tenure);
                        $session->offsetSet('total_interest_for_years', $total_interest_for_years);
                        $session->offsetSet('total_interest_for_years', $total_interest_for_years);
                        $session->offsetSet('current_interest_rate', $current_interest_rate);
                        $session->offsetSet('preferred_rate_package', $preferred_rate_package);
                        if($no_lock_in_only == 1) $session->offsetSet('no_lock_in_only', 1);
                        else $session->offsetSet('no_lock_in_only', 0);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "step", "id" => 4), array('name' => "refinancing_commercial"));
                    } else {
                        $redirect = $router->assemble(array("action" => "step", "id" => 2), array('name' => "refinancing_commercial"));
                    }

                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            case 5:
                if($request->isPost()) {
                    $post = $request->getPost();
                    $message = $post['message'];

                    // Send email
                    $application_view_helper_send_email = $viewHelperManager->get('send_email');

                    if($session->offsetExists('loan_amount')) {
                        $loan_amount = $session->offsetGet('loan_amount');
                    }
                    if($session->offsetExists('loan_tenure')) {
                        $loan_tenure = $session->offsetGet('loan_tenure');
                    }

                    // Save to DB
                    $application_view_helper_auth = $viewHelperManager->get('auth');
                    $user = $application_view_helper_auth();
                    $user_id = $user ? $user->getId() : 0;

                    $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
                    $property_loan = new \Application\Entity\PropertyLoan;
                    $property_loan->setUserId($user_id);
                    $property_loan->setType('home_loan');
                    $property_loan->setPropertyType($session->offsetGet('property_type'));
                    $property_loan->setPropertyStatus($session->offsetGet('property_status'));
                    $property_loan->setOptionFee($session->offsetGet('option_fee'));
                    $property_loan->setOfferOpts($session->offsetGet('offer_opts'));
                    $property_loan->setExisting($session->offsetGet('existing_home_loans'));
                    $property_loan->setExisting($session->offsetGet('existing_home_loans'));
                    $property_loan->setLoanAmount($session->offsetGet('loan_amount'));
                    $property_loan->setLoanTenure($session->offsetGet('loan_tenure'));
                    $property_loan->setLoanPercent($session->offsetGet('loan_percent'));
                    $property_loan->setFixedRates($session->offsetGet('preferred_rate_package'));
                    $property_loan->setRemark($message);
                    $property_loan->setDateAdded(new Expression('NOW()'));
                    $property_loan->setStatus(1);
                    $added = $application_model_property_loan->insert($property_loan);
                    if($added) {
                        $property_loan_id = $added->getGeneratedValue();
                        $selected = $session->offsetGet('select');
                        if(count($selected) > 0) {
                            $application_model_property_loan_ref = $this->getServiceLocator()->get('application_model_property_loan_ref');
                            foreach ($selected as $package_id) {
                                $property_loan_ref = new \Application\Entity\PropertyLoanRef;
                                $property_loan_ref->setPropertyLoanId($property_loan_id);
                                $property_loan_ref->setPropertyPackageId($package_id);
                                $application_model_property_loan_ref->insert($property_loan_ref);
                            }
                        }

                        // Send email to Admin
                        //$html = '<p>Loan Amount: $<b>'.number_format($loan_amount).'</b></p>';
                        //$html .= '<p>Loan Tenure: <b>'.$loan_tenure.'</b>%</p>';
                        $html = '<p>Dear Admin,</p>';
                        $html .= '<p>You have one loan application (Home Loan)</p>';
                        $html .= '<p>Please check your admin for more information.</p>';
                        $html .= '<p>'.$message.'</p>';
                        $application_view_helper_send_email("You have a Home Loan application form", $html, 'Best regard');

                        // Referrals
                        if ($user_id > 0) {
                            $application_model_user_ref = $this->getServiceLocator()->get('application_model_user_ref');
                            $users_ref = $application_model_user_ref->fetchRow(array("user_id" => $user_id));
                            if ($users_ref) {
                                $ref = $users_ref->getRef();
                                $application_model_user = $this->getServiceLocator()->get('application_model_user');
                                $fuser = $application_model_user->fetchRow(array("ref" => $ref));
                                if ($fuser)
                                {
                                    $application = $added->getGeneratedValue();
                                    $referral = new \Application\Entity\Referral;
                                    $referral->setUserId($fuser->getId());
                                    $referral->setType('property_loan');
                                    $referral->setApplication($application);
                                    $referral->setCredit($setting->amt_business_loan);
                                    $referral->setDateAdded(new Expression('NOW()'));
                                    $referral->setStatus(2);
                                    $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                                    $application_model_referral->insert($referral);
                                }
                            }
                        }
                    }
                    // Unset session personal
                    $session->offsetUnset('home_loan');

                    $success = true;
                    $redirect = $router->assemble(array("action" => "success"), array('name' => "home_loan"));

                    $response = $this->getResponse();
                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "msg" => $msg , "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            default:
                return $this->redirect()->toRoute("page", array("action" => "error", "id" => 404));
            break;
        }
        if($session->offsetExists('property_type')) {
            $property_type = $session->offsetGet('property_type');
        }
        if($session->offsetExists('preferred_rate_package')) {
            $preferred_rate_package = $session->offsetGet('preferred_rate_package');
        }
        if($session->offsetExists('no_lock_in_only')) {
            $no_lock_in_only = $session->offsetGet('no_lock_in_only');
        }
        if($session->offsetExists('remaining_loan_amount')) {
            $remaining_loan_amount = $session->offsetGet('remaining_loan_amount');
        }
        if($session->offsetExists('loan_amount')) {
            $loan_amount = $session->offsetGet('loan_amount');
        }
        if($session->offsetExists('new_loan_tenure')) {
            $new_loan_tenure = $session->offsetGet('new_loan_tenure');
        }
        if($session->offsetExists('current_interest_rate')) {
            $current_interest_rate = $session->offsetGet('current_interest_rate');
        }
        if($session->offsetExists('select')) {
            $select = $session->offsetGet('select');
        }

        $session_user = new Session('user');
        $session_user->offsetSet('redirect', $redirect);

        return array(
            "step"                     => $id,
            "property_type"            => $property_type,
            "preferred_rate_package"   => $preferred_rate_package,
            "remaining_loan_amount"    => $remaining_loan_amount,
            "loan_amount"              => $loan_amount,
            "new_loan_tenure"          => $new_loan_tenure,
            "current_interest_rate"    => $current_interest_rate,
            "loans"                    => $loans,
            "total_interest_for_years" => $total_interest_for_years,
            "no_lock_in_only"          => $no_lock_in_only,
            "select"                   => $select,
            "redirect_url"             => $redirect
        );
    }

    public function selectNoAction() {
        $seo = $this->params()->fromPost('seo');
        $session = new Session('home_loan');
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $session->offsetSet('property', 'Purchasing');
            $session->offsetSet('property_type', '');
            $session->offsetSet('property_status', '');
            $session->offsetSet('yes_or_no',0);

            $session->offsetSet('loan_amount', 10000);
            $session->offsetSet('loan_tenure', 20);
            $session->offsetSet('loan_percent', 80);
            // $session->offsetSet('preferred_rate_package', 'Floating');
            // $session->offsetSet('existing_home_loans', 'Floating');
            $session->offsetSet('purchase_price', 10000);
            $redirect = $router->assemble(array("action" => "step", "seo" => $seo, "step" => "step", "id" => 3), array('name' => "refinancing_commercial"));
        }
        $response->setContent ( \Zend\Json\Json::encode ( array("redirect" => $redirect) ) );
        return $response;
    }
}