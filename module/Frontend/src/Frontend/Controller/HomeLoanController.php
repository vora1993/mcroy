<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class HomeLoanController extends AbstractActionController
{
    public function indexAction()
    {
        $this->layout('layout/home-loan');
        return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 1));
    }

    public function stepAction() {
        $id = $this->params()->fromRoute('id');  // From RouteMatch
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $session = new Session('home_loan');
        $request = $this->getRequest();
        $response = $this->getResponse();
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $success = false;
        switch($id){
            case 1:
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $property_type = $post['property_type'];
                    $property_status = $post['property_status'];
                    $option_fee = $post['option_fee'];
                    $offer_opts = $post['offer_opts'];

                    if($property_type && $property_status && $option_fee) {
                        $session->offsetSet('property_type', $property_type);
                        $session->offsetSet('property_status', $property_status);
                        $session->offsetSet('option_fee', $option_fee);
                        $session->offsetSet('offer_opts', $offer_opts);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "step", "id" => 2), array('name' => "home_loan"));
                    } else {
                        $redirect = $router->assemble(array("action" => "step", "id" => 1), array('name' => "home_loan"));
                    }

                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            case 2:
                if(!$session->offsetExists('property_type') && !$session->offsetExists('property_status')) {
                    return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 1));
                }
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $existing_home_loans    = $post['existing_home_loans'];
                    $purchase_price         = $post['purchase_price'];
                    $loan_amount            = $post['loan_amount'];
                    $loan_tenure            = $post['loan_tenure'];
                    $loan_percent           = $post['loan_percent'];
                    $preferred_rate_package = $post['preferred_rate_package'];

                    if($loan_amount && $loan_tenure) {
                        $session->offsetSet('existing_home_loans', $existing_home_loans);
                        $session->offsetSet('purchase_price', $purchase_price);
                        $session->offsetSet('loan_amount', $loan_amount);
                        $session->offsetSet('loan_tenure', $loan_tenure);
                        $session->offsetSet('loan_percent', $loan_percent);
                        $session->offsetSet('preferred_rate_package', $preferred_rate_package);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "step", "id" => 3), array('name' => "home_loan"));
                    } else {
                        $redirect = $router->assemble(array("action" => "step", "id" => 2), array('name' => "home_loan"));
                    }

                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            case 3:
                $purchase_price = $session->offsetGet('purchase_price') ? $session->offsetGet('purchase_price') : 0;
                if(!$session->offsetExists('existing_home_loans') && $purchase_price <= 0) {
                    return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 2));
                }
                if($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 4));
                }
                $redirect = $router->assemble(array("action" => "step", "id" => 4), array('name' => "home_loan"));
            break;

            case 4:
                if(!$session->offsetExists('property_type') && !$session->offsetExists('property_status')) {
                    return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 1));
                }
                $purchase_price = $session->offsetGet('purchase_price') ? $session->offsetGet('purchase_price') : 0;
                if(!$session->offsetExists('existing_home_loans') && $purchase_price <= 0) {
                    return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 2));
                }
                if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    return $this->redirect()->toRoute("home_loan", array("action" => "step", "id" => 3));
                }

                $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_bank');
                $condition = array('property' => "home_loan", 'status' => 1);
                if($session->offsetExists('property_type')) {
                    $condition['type'] = $session->offsetGet('property_type');
                }
                if($session->offsetExists('property_status')) {
                    $condition['property_status'] = $session->offsetGet('property_status');
                }
                if($session->offsetExists('preferred_rate_package')) {
                    $condition['package'] = $session->offsetGet('preferred_rate_package');
                }
                if($session->offsetGet('no_lock_in_only') == 1) {
                    $condition['lock_in_year'] = 0;
                }
                $loans = $application_model_property_loan_bank->fetchFilter($condition);
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $loan_amount              = $post['loan_amount'];
                    $loan_tenure              = $post['loan_tenure'];
                    $total_interest_for_years = $post['total_interest_for_years'];
                    $preferred_rate_package   = $post['preferred_rate_package'];
                    $no_lock_in_only          = $post['no_lock_in_only'];

                    if($loan_amount && $loan_tenure) {
                        $session->offsetSet('loan_amount', $loan_amount);
                        $session->offsetSet('loan_tenure', $loan_tenure);
                        $session->offsetSet('total_interest_for_years', $total_interest_for_years);
                        $session->offsetSet('preferred_rate_package', $preferred_rate_package);
                        if($no_lock_in_only == 1) $session->offsetSet('no_lock_in_only', 1);
                        else $session->offsetSet('no_lock_in_only', 0);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "step", "id" => 4), array('name' => "home_loan"));
                    } else {
                        $redirect = $router->assemble(array("action" => "step", "id" => 2), array('name' => "home_loan"));
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
        if($session->offsetExists('purchase_price')) {
            $purchase_price = $session->offsetGet('purchase_price');
        }
        if($session->offsetExists('loan_amount')) {
            $loan_amount = $session->offsetGet('loan_amount');
        }
        if($session->offsetExists('loan_tenure')) {
            $loan_tenure = $session->offsetGet('loan_tenure');
        }
        if($session->offsetExists('loan_percent')) {
            $loan_percent = $session->offsetGet('loan_percent');
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
            "purchase_price"           => $purchase_price,
            "loan_amount"              => $loan_amount,
            "loan_tenure"              => $loan_tenure,
            "loan_percent"             => $loan_percent,
            "loans"                    => $loans,
            "total_interest_for_years" => $total_interest_for_years,
            "no_lock_in_only"          => $no_lock_in_only,
            "select"                   => $select,
            "redirect_url"             => $redirect
        );
    }

    public function selectAction() {
        $session = new Session('home_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            $success = false;

            if($session->offsetExists('select')) {
                $select = $session->offsetGet('select');
                $select_arr = $select;
                $current_count_select = count($select_arr);
            } else {
                $current_count_select = 0;
                $select_arr = array();
            }
            $max_count_select = 3;

            $success = false;
            $cr = "active";
            $ca = "";

            if($current_count_select <= $max_count_select) {
                if(!in_array($id, $select_arr)) {
                    array_push($select_arr, $id);
                    $success = true;
                    $cr = "";
                    $ca = "active";
                } else {
                    if(($key = array_search($id, $select_arr)) !== false) {
                        unset($select_arr[$key]);
                        $success = true;
                        $msg = $translator->translate("You have removed this bank select list");
                    }
                }
            } else {
                $msg = $translator->translate("Maximum 3 banks selected");
            }

            $session->offsetSet('select', $select_arr);
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success,"msg" => $msg , "cr" => $cr, "ca" => $ca) ) );
            return $response;
        }
        if($session->offsetExists('select')) $select = $session->offsetGet('select');
        return array("select" => $select);
    }

    public function loadSelectAction()
    {
        $basePath = $this->serviceLocator->get('viewhelpermanager')->get('basePath');
        $session = new Session('home_loan');
        $count = 0;
        $html = '<div class="row">';
        if ($session->offsetExists('select'))
        {
            $select = $session->offsetGet('select');
            if (count($select) > 0)
            {
                $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_bank');
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                foreach ($select as $id)
                {
                    $loan = $application_model_property_loan_bank->fetchRow(array("id" => $id));
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    // Image
                    $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
                    if (!file_exists($dir_logo))
                    {
                        $dir_logo = 'data/image/no-image-64.png';
                    }
                    $html .= '<div class="col-xs-4 drawercard-col">';
                    $html .= '<div class="drawercard-container filled" data-original-title="' . $loan->getTitle() . '" title="' . $loan->getTitle() . '">';
                    $html .= '<i class="fa fa-times" onclick="Loan.clear_select(this)" data-id="' .$loan->getId() . '"></i>';
                    $html .= '<a href=""><img src="' . $basePath($dir_logo) . '" alt="' . $bank->getName() .'" /></a>';
                    $html .= '</div>';
                    $html .= '<p>' . $loan->getTitle() . '</p>';
                    $html .= '</div>';
                }
                $count = count($select);
            }
        }
        $html .= '</div>';
        $response = $this->getResponse();
        $response->setContent(\Zend\Json\Json::encode(array("html" => $html, "count" => count($select))));
        return $response;
    }

    public function clearSelectAction()
    {
        $session = new Session('home_loan');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $success = true;
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            if ($session->offsetExists('select'))
            {
                $select = $session->offsetGet('select');
                $select_arr = $select;
                if ($id > 0)
                {
                    if (($key = array_search($id, $select_arr)) !== false)
                    {
                        unset($select_arr[$key]);
                    }
                } else
                {
                    unset($select_arr);
                    $select_arr = array();
                }
                $msg = $translator->translate("You have removed this bank selected list");
                $session->offsetSet('select', $select_arr);
                $count = count($select_arr);
            } else {
                $count = 0;
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("success" => $success, "msg" => $msg, "count" => $count)));
            return $response;
        }
    }

    public function applyAction() {
        $session = new Session('home_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $success = false;

            $id = $post['id'];
            if($id > 0) {
                $session->offsetSet('apply', $id);
                $success = true;
                $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
                $redirect = $router->assemble(array("action" => "property-loan", "seo" => $post['seo'], "step" => "step", "id" => 4), array('name' => "loan_application"));
            } else {
                $msg = $translator->translate("Please select at least 1 loan package so that we can email a copy of the package details to you");
            }

            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "msg" => $msg , "redirect" => $redirect) ) );
            return $response;
        }
        if($session->offsetExists('apply')) $select = $session->offsetGet('apply');
        return array("apply" => $apply);
    }

    public function applyXXXAction() { // May be compare
        $session = new Session('home_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $success = false;

            if($session->offsetExists('select')) {
                $select = $session->offsetGet('select');
                $select_arr = $select;
                $current_count_select = count($select_arr);
            } else {
                $current_count_select = 0;
                $select_arr = array();
            }

            if(count($select_arr) > 0) {
                $success = true;
                $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
                $redirect = $router->assemble(array("action" => "property-loan", "seo" => $post['seo'], "step" => "step", "id" => 4), array('name' => "loan_application"));
            } else {
                $msg = $translator->translate("Please select at least 1 loan package so that we can email a copy of the package details to you");
            }

            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "msg" => $msg , "redirect" => $redirect) ) );
            return $response;
        }
        if($session->offsetExists('select')) $select = $session->offsetGet('select');
        return array("select" => $select);
    }

    public function successAction() {

    }
}