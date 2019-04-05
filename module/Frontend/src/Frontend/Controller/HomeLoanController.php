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
                    $session->offsetSet('yes_or_no',1);
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
                    $redirect='/user/auth';
                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => true, "redirect" => $redirect) ) );
                    return $response;
                }
                $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_bank');
                // $condition = array('property' => "home_loan", 'status' => 1);
                $condition = array('property' => "Purchasing", 'status' => 1);
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
                if($session->offsetGet('yes_or_no')==0) {
                    $condition=[];
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
                        // $redirect = $router->assemble(array("action" => "apply"), array('name' => "home_loan"));
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

        $application_model_property_cost_out_play = $this->getServiceLocator()->get('application_model_property_cost_out_play');
        $total_cost_outplay_value = $application_model_property_cost_out_play->fetchRow();

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
            "redirect_url"             => $redirect,
            "total_cost_outplay_value" => $total_cost_outplay_value
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
            $redirect = $router->assemble(array("action" => "step", "seo" => $seo, "step" => "step", "id" => 3), array('name' => "home_loan"));
        }
        $response->setContent ( \Zend\Json\Json::encode ( array("redirect" => $redirect) ) );
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

        if ($session->offsetExists('select'))
          {
            $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_bank');
            $loans = $application_model_property_loan_bank->fetchAll(array("id" => $session->offsetGet('select')));
          }

        $personal_loans = array();


        foreach ($loans as $loan){
          $loan_amount = (float) $session['loan_amount'];
          $total_interest_for_years = (float) $session['total_interest_for_years'];
          $total_interest_for_years = (float) $total_interest_for_years > 0 ? $total_interest_for_years : 2;
          $int_rates = (float) $loan -> getIntYear2();
          $r = ($int_rates / 100) / 12;

          $monthly_payment = ($r + $r / (pow(1 + $r, ($total_interest_for_years * 12)) -1 ) ) * $loan_amount;

          $personal_loan = array(
              'loan_id' => $loan-> getId(),
              'title' => $loan -> getTitle(),
              'type' => "business_loan",
              'bank_id' => $loan -> getBankId(),
              'int_year_2' => $loan -> getIntYear2(),
              'lock_in_year' => $loan -> getLockInYear(),
              'monthly_payment' => $monthly_payment
          );
          array_push($personal_loans, $personal_loan);
        }

        return array("loans" => $personal_loans);
    }


    public function applyFormAction()
    {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session = new Session('business_loan');
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $application_view_helper_setting = $viewHelperManager->get('setting');
        $user = $application_view_helper_auth();
        $setting = $application_view_helper_setting();
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $post = $request->getPost();
            $success = false;
            if ($session->offsetExists('loan'))
            {
                $loan = $session->offsetGet('loan');
                $translator = $this->getServiceLocator()->get('translator');
                $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
                $personal_loan = new \Application\Entity\PersonalLoan;
                $personal_loan->setUserId($post['user_id']);
                $personal_loan->setLoanId($loan['loan_id']);
                $personal_loan->setCategoryId($loan['category_id']);
                $personal_loan->setType($post['type']);
                $firstname = $post['firstname'];
                $lastname = $post['lastname'];
                $name = $firstname . ' ' . $lastname;
                $personal_loan->setName($name);
                $personal_loan->setEmail($post['email']);
                $personal_loan->setPhone($post['phone']);
                $personal_loan->setCompanyName($post['company_name']);
                $personal_loan->setRemark($post['remark']);
                $personal_loan->setIntRate($loan['int_rate']);
                $personal_loan->setLoanAmount($loan['loan_amount']);
                $personal_loan->setLoanTenure($loan['loan_tenure']);
                $personal_loan->setMonthlyPayment($loan['monthly_payment']);
                $personal_loan->setDateAdded(new Expression('NOW()'));
                $personal_loan->setStatus($post['status']);
                $added = $application_model_business_loan->insert($personal_loan);
                if ($added)
                {
                    $success = true;
                    $redirect = $router->assemble(array("action" => "success"), array('name' => 'personal_loan'));

                    $id = $added->getGeneratedValue();
                    $business_loan = $application_model_business_loan->fetchRow(array("id" => $id));
                    if($business_loan) {
                        // Send email
                        $application_view_helper_send_email = $viewHelperManager->get('send_email');
                        $subject = $translator->translate("You have a application form from business loan");
                        $text = $translator->translate("Best regard");

                        $html = '<table rules="all" style="border: 1px solid #f9f9f9;" cellpadding="8" cellspacing="8">';
                        $html .= '<tr><th colspan="2" align="center"><strong>'.$translator->translate("Information").'</strong></th></tr>';
                        if($name) $html .= '<tr><td><strong>'.$translator->translate("Name").'</strong></td><td>'.$name.'</td></tr>';
                        if($post['email']) $html .= '<tr><td><strong>'.$translator->translate("Email").'</strong></td><td>'.$post['email'].'</td></tr>';
                        if($post['phone']) $html .= '<tr><td><strong>'.$translator->translate("Phone").'</strong></td><td>'.$post['phone'].'</td></tr>';
                        if($post['company_name']) $html .= '<tr><td><strong>'.$translator->translate("Company Name").'</strong></td><td>'.$post['company_name'].'</td></tr>';
                        $html .= '<tr><th colspan="2" align="center"><strong>'.$translator->translate("Package").'</strong></th></tr>';
                        if($post['type']) $html .= '<tr><td><strong>'.$translator->translate("Type").'</strong></td><td>'.$post['type'].'</td></tr>';
                        if($loan['loan_id']) {
                            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
                            $business_loan_package = $application_model_business_loan_package->fetchRow(array("id" => $loan['loan_id']));
                            if($business_loan_package) {
                                $html .= '<tr><td><strong>'.$translator->translate("Loan").'</strong></td><td>'.$business_loan_package->getLoanTitle().'</td></tr>';
                            }
                        }
                        if($loan['int_rate']) $html .= '<tr><td><strong>'.$translator->translate("Interest Rate").'</strong></td><td>'.$loan['int_rate'].'%</td></tr>';
                        if($loan['loan_amount']) $html .= '<tr><td><strong>'.$translator->translate("Loan Amount").'</strong></td><td>'.number_format($loan['loan_amount']).'$</td></tr>';
                        if($loan['loan_tenure']) $html .= '<tr><td><strong>'.$translator->translate("Loan Tenure").'</strong></td><td>'.$loan['loan_tenure'].' years</td></tr>';
                        if($loan['monthly_payment']) $html .= '<tr><td><strong>'.$translator->translate("Monthly Payment").'</strong></td><td>'.number_format($loan['monthly_payment']).'$ per month</td></tr>';
                        $html .= '<tr><th colspan="2" align="center"><strong>'.$translator->translate("Status").'</strong></th></tr>';
                        $html .= '<tr><td><strong>'.$translator->translate("Creation Date").'</strong></td><td>'.date("d-m-Y H:i A", strtotime($business_loan->getDateAdded())).'</td></tr>';

                        $application_view_helper_status = $viewHelperManager->get('status');
                        $html .= '<tr><td><strong>'.$translator->translate("Status").'</strong></td><td>'.$application_view_helper_status($business_loan).'</td></tr>';
                        $html .= '</table>';

                        $application_view_helper_send_email($subject, $html, $text);
                    }

                    // Referrals
                    $user_id = $user ? $user->getId() : 0;
                    if ($user_id > 0)
                    {
                        $application_model_user_ref = $this->getServiceLocator()->get('application_model_user_ref');
                        $users_ref = $application_model_user_ref->fetchRow(array("user_id" => $user_id));
                        if ($users_ref)
                        {
                            $ref = $users_ref->getRef();
                            $application_model_user = $this->getServiceLocator()->get('application_model_user');
                            $fuser = $application_model_user->fetchRow(array("ref" => $ref));
                            if ($fuser)
                            {
                                $application = $added->getGeneratedValue();
                                $referral = new \Application\Entity\Referral;
                                $referral->setUserId($fuser->getId());
                                $referral->setType('business_loan');
                                $referral->setApplication($application);
                                $referral->setCredit($setting->amt_business_loan);
                                $referral->setDateAdded(new Expression('NOW()'));
                                $referral->setStatus(0);
                                $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                                $application_model_referral->insert($referral);
                            }
                        }
                    }

                    // Unset session personal
                    $session->offsetSet('success', 1);
                    $session->offsetUnset('loan');
                }
            } else {
                $redirect = $router->assemble(array("action" => "apply"), array('name' => 'personal_loan'));
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("success" => $success, "redirect" => $redirect)));
            return $response;
        }
        $loan = $session->offsetGet('loan');
        if ($this->getServiceLocator()->get('AuthService')->hasIdentity())
        {
            $application_view_helper_auth = $viewHelperManager->get('auth');
            $user = $application_view_helper_auth();
            if ($session->offsetExists('loan'))
            {
                return array("loan" => $loan, "user" => $user);
            }
        } else {
            $session_user = new Session('user');
            $session_user->offsetSet('redirect', $router->assemble(array("action" => "apply-form"), array('name' => 'personal_loan')));
            return $this->redirect()->toRoute("frontend_user", array("action" => "auth"));
        }
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