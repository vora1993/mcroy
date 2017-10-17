<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class AlternativeFundingController extends AbstractActionController
{
    /**
     * Factoring
     */
    public function factoringAction()
    {
        $session = new Session('business_loan');
        $session->offsetUnset('success');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $loans = $application_model_business_loan_package->fetchAll(array("status" => 1, "type" => "company"));
            $html = '';
            if (count($loans) > 0)
            {
                $html .= '<div class="row filters-table-head hidden-sm hidden-xs">';
                $html .= '<div class="col-md-2" style="text-align:center"><span class="head-title-bank" data-field="bank"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Bank") . '</a></span></div>';
                $html .= '<div class="col-md-5"><div class="row">';
                $html .= '<div class="col-md-4" style="text-align:center"><span class="head-title-rate" data-field="rate"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Rate") . '</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-requirement" data-field="requirement"><a title="' .$translator->translate("Applicable") .'" href="javascript:;" onclick="Loan.sort(this)">' . $translator->translate("Min requirement") .'</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-monthly-instalments" data-field="month"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Monthly Instalments") . '</a></span></div>';
                $html .= '</div></div>';
                $html .= '<div class="col-md-5"><div class="row">';
                $html .= '<div class="col-md-4"><span class="head-title-interest" data-field="interest"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Total Interest Rate") . '</a></span></div>';
                $html .= '<div class="col-md-4">' . $translator->translate("Processing Fee") .'</div>';
                $html .= '<div class="col-md-4">' . $translator->translate("Penalty Fee") .'</div>';
                $html .= '</div></div>';
                $html .= '</div>';
                $loan_amount = $post['loan_amount'];
                $loan_tenure = $post['loan_tenure'];
                $int_rates = '';
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                $html .= '<div class="filters-table-body">';
                foreach ($loans as $k => $loan)
                {
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    if ($k == 0)
                    {
                        $html .= '<div class="filters-content sponsored">';
                    } else
                    {
                        $html .= '<div class="filters-content not-sponsored">';
                    }
                    $html .= '<div class="row-header"><h4 class="bank-title"  style="background-color: ' . $bank->getColor() .'"><a href="#">' . $loan->getLoanTitle() . '</a></h4>';
                    $html .= '</div>';
                    $html .= '<div class="row row-content">';
                    // Image
                    $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
                    if (!file_exists($dir_logo))
                    {
                        $dir_logo = 'data/image/no-image-64.png';
                    }
                    $html .= '<div class="col-md-2"><a href="#"><img src="' . $basePath($dir_logo) .
                        '" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></div>';
                    // Interest rate
                    $interest_rate = \Zend\Json\Json::decode($loan->getInterestRate());
                    if (count($interest_rate) > 0)
                    {
                        foreach ($interest_rate as $key => $value)
                        {
                            $condition = $value->condition;
                            $year = $value->year;
                            $condition = str_replace('{m}', $loan_amount, $condition);
                            $str = '$result = (bool)(' . $condition . ');';
                            eval($str);
                            if ($result)
                            {
                                $int_rates = $year->{$loan_tenure};
                            }
                        }
                    }
                    // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
                    // Where: r = decimal rate / 12.
                    $r = ($int_rates / 100) / 12;
                    $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) - 1)) * $loan_amount;
                    $total_amount_payable = $monthly_payment * $loan_tenure * 12;
                    $total_interest_payable = $total_amount_payable - $loan_amount;
                    $class = "";
                    if ($session->offsetExists('compare'))
                    {
                        $compare_arr = $session->offsetGet('compare');
                        if (count($compare_arr) > 0)
                        {
                            if (in_array($loan->getId(), $compare_arr))
                            {
                                $class = " active";
                            }
                        }
                    }
                    // Applicable
                    $min_sales_turnover = $post['min_sales_turnover'] ? $post['min_sales_turnover'] : 0;
                    $min_years_of_incorporation = $post['min_years_of_incorporation'] ? $post['min_years_of_incorporation'] : 0;
                    $loan_min_sales_turnover = $loan->getMinSalesTurnover() ? $loan->getMinSalesTurnover() : 0;
                    $loan_years_of_incorporation = $loan->getMinYearsOfIncorporation() ? $loan->getMinYearsOfIncorporation() : 0;
                    if ($min_sales_turnover >= $loan_min_sales_turnover && $min_years_of_incorporation >= $loan_years_of_incorporation)
                    {
                        $dir_c = 'assets/img/checked-yes.png';
                        $checked = "yes";
                    } else {
                        $dir_c = 'assets/img/checked-no.png';
                        $checked = "no";
                    }
                    $html .= '<div class="col-md-5"><div class="row">';
                    $html .= '<div class="col-xs-4 box__rate"><span class="rate" data-value="' . $int_rates .'"><b>' . $int_rates . '%</b>' . $translator->translate("Interest Rate") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__requirement"><span class="requirement" data-value="' .$checked . '"><img src="' . $basePath($dir_c) . '" /><br/>' . $translator->translate("Min requirement") . '</span></div>';
                    $html .= '<div class="col-xs-4 box__month"><span class="month" data-value="' . $monthly_payment .'"><b>$' . number_format($monthly_payment, 2) . '</b>' . $translator->translate("Per Month") .'</span></div>';
                    $html .= '</div></div>';
                    $html .= '<div class="col-md-5"><div class="row">';
                    $html .= '<div class="col-xs-4 box__interest"><span class="interest"><b>$' .number_format($total_interest_payable, 2) . '</b>' . $translator->translate("Interest Total") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__processing"><span class="processing" ><b>' .$loan->getProcessingFee() . '</b>' . $translator->translate("Processing Fee") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__penalty"><span class="penalty" ><b>0</b>' .$translator->translate("Penalty Fee") . '</span></div>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    $html .= '<div class="row row-footer">';
                    $html .= '<div class="col-md-12 summary-details">';
                    $html .= '<div class="row">
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("INT RATES") .'</strong></div>
                                <div class="col-md-8 line-top">' . $int_rates . '%</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("Max Tenor (Years)") .'</strong></div>
                                <div class="col-md-8 line-top">' . $loan->getMaxTenor() . '</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("Principle Loan Amount") .'</strong></div>
                                <div class="col-md-8 line-top">' . number_format($loan_amount) . '</div>
                            </div>
                        </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Max Loan Amount") .'</strong></div>
                            <div class="col-md-8 line-top">' . $loan->getMaxLoanAmount() . '</div></div>
                            <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Processing Fee") .'</strong></div>
                            <div class="col-md-8 line-top">' . $loan->getProcessingFee() . '</div>
                            </div>
                            <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Monthly Instalment") .'</strong></div>
                            <div class="col-md-8 line-top">$' . number_format($monthly_payment, 2) . '</div>
                            </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Annual Fee") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getAnnualFee() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Penalty Fee") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getPenaltyFee() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Interest Rate (per annum)") .'</strong></div>
                        <div class="col-md-8 line-top">' . $int_rates . '%</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Lock In Period") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getLockInPeriod() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Minimum Sales Turnover") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getMinSalesTurnover() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Loan Tenure") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan_tenure . ' years</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Minimum Years of Incorporation") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getMinYearsOfIncorporation() . '</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Amount Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_amount_payable, 2) .'</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Interest Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_interest_payable, 2) .'</div>
                        </div>
                        </div>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12 more-detail">
                        <div class="col-sm-5 col-xs-6 button-gr"><i class="fa fa-share-square-o"></i> Share</div>
                        <div class="col-sm-3 col-xs-6 button-gr button-gr-s">
                        <a href="javascript:void(0)" class="btn btn-more-detail">' . $translator->translate("Details") . '<i class="fa fa-angle-down"></i></a>
                        <a href="javascript:void(0)" class="btn btn-less-detail" style="display: none;">' .$translator->translate("Close") . '<i class="fa fa-angle-up"></i></a>';
                    $html .= '</div>';
                    $html .= '<div class="col-sm-4 col-xs-12 button-gr button-gr-s1">';
                    $html .= '<div class="col-md-6 col-xs-12 box__compare"><a href="javascript:;" onclick="Loan.compare(this)" data-id="' .$loan->getId() . '" class="compare' . $class . '" title="' . $translator->translate("Compare") . '"><i class="fa fa-random"></i><span>' . $translator->translate("Compare") . '</span></a></div>';
                    $html .= '<div class="col-md-6 col-xs-12 box__apply"><button type="button" onclick="Loan.apply(this)" data-id="' .$loan->getId() . '" class="btn yellow-gold btn-lg btn-block ladda-button" data-style="slide-up" title="' .$translator->translate("Apply") . '">' . $translator->translate("Apply Now") .' <i class="fa fa-angle-right"></i></button></div>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("html" => $html)));
            return $response;
        }
    }
    
    public function factoringApplyAction() {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = $request->getPost();
            $id = $post['id'];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];
            
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $loan = $application_model_business_loan_package->fetchRow(array("id" => $id));
            
            // Interest rate
            $interest_rate = \Zend\Json\Json::decode ( $loan->getInterestRate() );
            if(count($interest_rate) > 0 ) {
                foreach ($interest_rate as $key => $value) {
                    $condition = $value->condition;
                    $year = $value->year;
                            
                    $condition = str_replace('{m}', $loan_amount, $condition);
                    $str = '$result = (bool)('.$condition.');';
                    eval($str);
                    if($result) {
                        $int_rates = $year->{$loan_tenure};
                    }
                }
            }
            // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
            // Where: r = decimal rate / 12.
            $r = ($int_rates / 100) / 12;
            $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) -1 ) ) * $loan_amount;
                    
            $total_amount_payable = $monthly_payment * $loan_tenure * 12;
            $total_interest_payable = $total_amount_payable - $loan_amount;
            
            $alternative_funding = array(
                'loan_id'         => $id,
                'type'            => "factoring",
                'int_rate'        => $int_rates,
                'loan_amount'     => $loan_amount,
                'loan_tenure'     => $loan_tenure,
                'monthly_payment' => $monthly_payment,
            );
            $session->offsetSet('loan', $alternative_funding);
            $redirect = '/alternative-funding/factoring-apply';
            
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => true, "redirect" => $redirect) ) );
            return $response;  
        }
        if($session->offsetExists('loan')) {
            $loan = $session->offsetGet('loan');
            return array("loan" => $loan);
        }
        return $this->redirect()->toRoute("alternative_funding", array("action" => "factoring"));
    }
    
    public function factoringApplyFormAction() {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session = new Session('business_loan');
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $application_view_helper_setting = $viewHelperManager->get('setting');
        $user = $application_view_helper_auth();
        $setting = $application_view_helper_setting();
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = $request->getPost();
            $success = false;
            if($session->offsetExists('loan')) {
                $loan = $session->offsetGet('loan');
                $translator = $this->getServiceLocator()->get('translator');
                $application_model_alternative_funding = $this->getServiceLocator()->get('application_model_business_loan');
                $alternative_funding = new \Application\Entity\PersonalLoan;
                $alternative_funding->setUserId($post['user_id']);
                $alternative_funding->setLoanId($loan['loan_id']);
                $alternative_funding->setType($post['type']);
                $firstname = $post['firstname'];
                $lastname = $post['lastname'];
                $name = $firstname.' '.$lastname;
                $alternative_funding->setName($name);
                $alternative_funding->setEmail($post['email']);
                $alternative_funding->setPhone($post['phone']);
                $alternative_funding->setCompanyName($post['company_name']);
                $alternative_funding->setRemark($post['remark']);
                $alternative_funding->setIntRate($loan['int_rate']);
                $alternative_funding->setLoanAmount($loan['loan_amount']);
                $alternative_funding->setLoanTenure($loan['loan_tenure']);
                $alternative_funding->setMonthlyPayment($loan['monthly_payment']);
                $alternative_funding->setDateAdded(new Expression('NOW()'));
                $alternative_funding->setStatus(0);
                $added = $application_model_alternative_funding->insert($alternative_funding);
                if($added) {
                    $success = true;
                    $redirect = "/alternative-funding/success";
                    
                    // Send email
                    $application_view_helper_send_email = $viewHelperManager->get('send_email');
                    
                    $html = '<p>Name: <b>'.$name.'</b></p>';
                    $html .= '<p>Email: <b>'.$post['email'].'</b></p>';
                    $html .= '<p>Phone: <b>'.$post['phone'].'</b></p>';
                    if($post['company_name']) $html .= '<p>Company Name: <b>'.$post['company_name'].'</b></p>';
                    if($post['remark']) $html .= '<p>Remark: <b>'.$post['remark'].'</b></p>';
                    $application_view_helper_send_email("You have a factoring application form", $html, 'Best regard');
                    
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
                                $referral->setStatus(2);
                                $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                                $application_model_referral->insert($referral);
                            }
                        }
                    }
                    
                    // Unset session factoring
                    $session->offsetSet('success', 1);
                    $session->offsetUnset('loan');
                }
            } else {
                $redirect = "/alternative-funding/factoring-apply";
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
            return $response;
        }
        if($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            $application_view_helper_auth = $viewHelperManager->get('auth');
            $user = $application_view_helper_auth();
            
            if($session->offsetExists('loan')) {
                $loan = $session->offsetGet('loan');
                return array("loan" => $loan, "user" => $user);
            }
        } else {
            $session->offsetSet('redirect', '/alternative-funding/factoring-apply-form');
            return $this->redirect()->toRoute("frontend_user", array("action" => "auth"));    
        }
    }
    
    public function factoringCompareAction() {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];
            $success = false;
            
            $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
            $application_view_helper_setting = $viewHelperManager->get('setting');
            $max_loan_compare = $application_view_helper_setting()->max_loan_compare;
            if($session->offsetExists('compare')) {
                $compare = $session->offsetGet('compare');
                $compare_arr = $compare;
                $current_count_compare = count($compare_arr);    
            } else {
                $current_count_compare = 0;
                $compare_arr = array();
            }
            
            $success = false;
            $cr = "active";
            $ca = "";
            
            if(!in_array($id, $compare_arr)) {
                if($current_count_compare < $max_loan_compare) {
                    array_push($compare_arr, $id);
                    $success = true;
                    $cr = "";
                    $ca = "active";
                } else {
                    $msg = $translator->translate("Sorry you haven't added any bank to compare");
                }
            } else {
                if(($key = array_search($id, $compare_arr)) !== false) {
                    unset($compare_arr[$key]);
                    $success = true;
                    $msg = $translator->translate("You have removed this bank compare list");
                }
            }
            $session->offsetSet('compare', $compare_arr);
            $alternative_funding = array(
                'loan_amount'     => $loan_amount,
                'loan_tenure'     => $loan_tenure,
            );
            $session->offsetSet('loan', $alternative_funding);
            
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success,"msg" => $msg , "cr" => $cr, "ca" => $ca) ) );
            return $response;  
        }
        if($session->offsetExists('compare')) $compare = $session->offsetGet('compare');
        if($session->offsetExists('loan')) $loan = $session->offsetGet('loan');      
        return array("loan" => $loan, "compare" => $compare);
    }
    
    public function factoringLoadCompareAction() {
        $session = new Session('business_loan');
        $count = 0;
        
        $html = '<div class="row">';
        if($session->offsetExists('compare')) {
            $compare = $session->offsetGet('compare');
            if(count($compare) > 0) {
                $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath'); 
                $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                foreach ($compare as $key => $loan_id) {
                    $loan = $application_model_business_loan_package->fetchRow(array("id" => $loan_id));
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    // Image
                    $dir_logo = 'data/bank/'.$loan->getBankId().'/m_'.$bank->getLogo();
                    if(!file_exists($dir_logo)) {
                        $dir_logo = 'data/image/no-image-64.png';
                    } 
                    $html .= '<div class="col-xs-4 drawercard-col">';
                    $html .= '<div class="drawercard-container filled" data-original-title="'.$loan->getLoanTitle().'" title="'.$loan->getLoanTitle().'">';
                    $html .= '<i class="fa fa-times" onclick="Loan.clear_compare(this)" data-id="'.$loan->getId().'"></i>';
                    $html .= '<a href=""><img src="'.$basePath($dir_logo).'" alt="'.$bank->getName().'" /></a>';
                    $html .= '</div>';
                    $html .= '<p>'.$loan->getLoanTitle().'</p>';
                    $html .= '</div>';
                }
                $count = count($compare);
            }
        } 
        $html .= '</div>';
        $response = $this->getResponse();
        $response->setContent ( \Zend\Json\Json::encode ( array("html" => $html, "count" => count($compare)) ) );
        return $response; 
    }
    
    public function factoringClearCompareAction() {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $success = true;
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            
            if($session->offsetExists('compare')) {
                $compare = $session->offsetGet('compare');
                $compare_arr = $compare;  
                
                if($id > 0) {
                    if(($key = array_search($id, $compare_arr)) !== false) {
                        unset($compare_arr[$key]);
                    }
                } else {
                    unset($compare_arr);
                    $compare_arr = array();
                }
                $msg = $translator->translate("You have removed this bank compare list");
                $session->offsetSet('compare', $compare_arr);
                $count = count($compare_arr);
            } else {
                $count = 0;
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "msg" => $msg, "count" => $count) ) );
            return $response; 
        }
    }
    
    /**
     * PeerToPeer Funding
     */
    public function fundingAction()
    {
        $session = new Session('business_loan');
        $session->offsetUnset('success');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $loans = $application_model_business_loan_package->fetchAll(array("status" => 1, "type" => "company"));
            $html = '';
            if (count($loans) > 0)
            {
                $html .= '<div class="row filters-table-head hidden-sm hidden-xs">';
                $html .= '<div class="col-md-2" style="text-align:center"><span class="head-title-bank" data-field="bank"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Bank") . '</a></span></div>';
                $html .= '<div class="col-md-5"><div class="row">';
                $html .= '<div class="col-md-4" style="text-align:center"><span class="head-title-rate" data-field="rate"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Rate") . '</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-requirement" data-field="requirement"><a title="' .$translator->translate("Applicable") .'" href="javascript:;" onclick="Loan.sort(this)">' . $translator->translate("Min requirement") .'</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-monthly-instalments" data-field="month"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Monthly Instalments") . '</a></span></div>';
                $html .= '</div></div>';
                $html .= '<div class="col-md-5"><div class="row">';
                $html .= '<div class="col-md-4"><span class="head-title-interest" data-field="interest"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Total Interest Rate") . '</a></span></div>';
                $html .= '<div class="col-md-4">' . $translator->translate("Processing Fee") .'</div>';
                $html .= '<div class="col-md-4">' . $translator->translate("Penalty Fee") .'</div>';
                $html .= '</div></div>';
                $html .= '</div>';
                $loan_amount = $post['loan_amount'];
                $loan_tenure = $post['loan_tenure'];
                $int_rates = '';
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                $html .= '<div class="filters-table-body">';
                foreach ($loans as $k => $loan)
                {
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    if ($k == 0)
                    {
                        $html .= '<div class="filters-content sponsored">';
                    } else
                    {
                        $html .= '<div class="filters-content not-sponsored">';
                    }
                    $html .= '<div class="row-header"><h4 class="bank-title"  style="background-color: ' . $bank->getColor() .'"><a href="#">' . $loan->getLoanTitle() . '</a></h4>';
                    $html .= '</div>';
                    $html .= '<div class="row row-content">';
                    // Image
                    $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
                    if (!file_exists($dir_logo))
                    {
                        $dir_logo = 'data/image/no-image-64.png';
                    }
                    $html .= '<div class="col-md-2"><a href="#"><img src="' . $basePath($dir_logo) .
                        '" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></div>';
                    // Interest rate
                    $interest_rate = \Zend\Json\Json::decode($loan->getInterestRate());
                    if (count($interest_rate) > 0)
                    {
                        foreach ($interest_rate as $key => $value)
                        {
                            $condition = $value->condition;
                            $year = $value->year;
                            $condition = str_replace('{m}', $loan_amount, $condition);
                            $str = '$result = (bool)(' . $condition . ');';
                            eval($str);
                            if ($result)
                            {
                                $int_rates = $year->{$loan_tenure};
                            }
                        }
                    }
                    // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
                    // Where: r = decimal rate / 12.
                    $r = ($int_rates / 100) / 12;
                    $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) - 1)) * $loan_amount;
                    $total_amount_payable = $monthly_payment * $loan_tenure * 12;
                    $total_interest_payable = $total_amount_payable - $loan_amount;
                    $class = "";
                    if ($session->offsetExists('compare'))
                    {
                        $compare_arr = $session->offsetGet('compare');
                        if (count($compare_arr) > 0)
                        {
                            if (in_array($loan->getId(), $compare_arr))
                            {
                                $class = " active";
                            }
                        }
                    }
                    // Applicable
                    $min_sales_turnover = $post['min_sales_turnover'] ? $post['min_sales_turnover'] : 0;
                    $min_years_of_incorporation = $post['min_years_of_incorporation'] ? $post['min_years_of_incorporation'] : 0;
                    $loan_min_sales_turnover = $loan->getMinSalesTurnover() ? $loan->getMinSalesTurnover() : 0;
                    $loan_years_of_incorporation = $loan->getMinYearsOfIncorporation() ? $loan->getMinYearsOfIncorporation() : 0;
                    if ($min_sales_turnover >= $loan_min_sales_turnover && $min_years_of_incorporation >= $loan_years_of_incorporation)
                    {
                        $dir_c = 'assets/img/checked-yes.png';
                        $checked = "yes";
                    } else {
                        $dir_c = 'assets/img/checked-no.png';
                        $checked = "no";
                    }
                    $html .= '<div class="col-md-5"><div class="row">';
                    $html .= '<div class="col-xs-4 box__rate"><span class="rate" data-value="' . $int_rates .'"><b>' . $int_rates . '%</b>' . $translator->translate("Interest Rate") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__requirement"><span class="requirement" data-value="' .$checked . '"><img src="' . $basePath($dir_c) . '" /><br/>' . $translator->translate("Min requirement") . '</span></div>';
                    $html .= '<div class="col-xs-4 box__month"><span class="month" data-value="' . $monthly_payment .'"><b>$' . number_format($monthly_payment, 2) . '</b>' . $translator->translate("Per Month") .'</span></div>';
                    $html .= '</div></div>';
                    $html .= '<div class="col-md-5"><div class="row">';
                    $html .= '<div class="col-xs-4 box__interest"><span class="interest"><b>$' .number_format($total_interest_payable, 2) . '</b>' . $translator->translate("Interest Total") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__processing"><span class="processing" ><b>' .$loan->getProcessingFee() . '</b>' . $translator->translate("Processing Fee") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__penalty"><span class="penalty" ><b>0</b>' .$translator->translate("Penalty Fee") . '</span></div>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    $html .= '<div class="row row-footer">';
                    $html .= '<div class="col-md-12 summary-details">';
                    $html .= '<div class="row">
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("INT RATES") .'</strong></div>
                                <div class="col-md-8 line-top">' . $int_rates . '%</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("Max Tenor (Years)") .'</strong></div>
                                <div class="col-md-8 line-top">' . $loan->getMaxTenor() . '</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("Principle Loan Amount") .'</strong></div>
                                <div class="col-md-8 line-top">' . number_format($loan_amount) . '</div>
                            </div>
                        </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Max Loan Amount") .'</strong></div>
                            <div class="col-md-8 line-top">' . $loan->getMaxLoanAmount() . '</div></div>
                            <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Processing Fee") .'</strong></div>
                            <div class="col-md-8 line-top">' . $loan->getProcessingFee() . '</div>
                            </div>
                            <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Monthly Instalment") .'</strong></div>
                            <div class="col-md-8 line-top">$' . number_format($monthly_payment, 2) . '</div>
                            </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Annual Fee") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getAnnualFee() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Penalty Fee") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getPenaltyFee() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Interest Rate (per annum)") .'</strong></div>
                        <div class="col-md-8 line-top">' . $int_rates . '%</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Lock In Period") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getLockInPeriod() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Minimum Sales Turnover") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getMinSalesTurnover() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Loan Tenure") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan_tenure . ' years</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Minimum Years of Incorporation") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getMinYearsOfIncorporation() . '</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Amount Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_amount_payable, 2) .'</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Interest Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_interest_payable, 2) .'</div>
                        </div>
                        </div>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-12 more-detail">
                        <div class="col-sm-5 col-xs-6 button-gr"><i class="fa fa-share-square-o"></i> Share</div>
                        <div class="col-sm-3 col-xs-6 button-gr button-gr-s">
                        <a href="javascript:void(0)" class="btn btn-more-detail">' . $translator->translate("Details") . '<i class="fa fa-angle-down"></i></a>
                        <a href="javascript:void(0)" class="btn btn-less-detail" style="display: none;">' .$translator->translate("Close") . '<i class="fa fa-angle-up"></i></a>';
                    $html .= '</div>';
                    $html .= '<div class="col-sm-4 col-xs-12 button-gr button-gr-s1">';
                    $html .= '<div class="col-md-6 col-xs-12 box__compare"><a href="javascript:;" onclick="Loan.compare(this)" data-id="' .$loan->getId() . '" class="compare' . $class . '" title="' . $translator->translate("Compare") . '"><i class="fa fa-random"></i><span>' . $translator->translate("Compare") . '</span></a></div>';
                    $html .= '<div class="col-md-6 col-xs-12 box__apply"><button type="button" onclick="Loan.apply(this)" data-id="' .$loan->getId() . '" class="btn yellow-gold btn-lg btn-block ladda-button" data-style="slide-up" title="' .$translator->translate("Apply") . '">' . $translator->translate("Apply Now") .' <i class="fa fa-angle-right"></i></button></div>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("html" => $html)));
            return $response;
        }
    }
    
    public function fundingApplyAction() {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = $request->getPost();
            $id = $post['id'];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];
            
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $loan = $application_model_business_loan_package->fetchRow(array("id" => $id));
            
            // Interest rate
            $interest_rate = \Zend\Json\Json::decode ( $loan->getInterestRate() );
            if(count($interest_rate) > 0 ) {
                foreach ($interest_rate as $key => $value) {
                    $condition = $value->condition;
                    $year = $value->year;
                            
                    $condition = str_replace('{m}', $loan_amount, $condition);
                    $str = '$result = (bool)('.$condition.');';
                    eval($str);
                    if($result) {
                        $int_rates = $year->{$loan_tenure};
                    }
                }
            }
            // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
            // Where: r = decimal rate / 12.
            $r = ($int_rates / 100) / 12;
            $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) -1 ) ) * $loan_amount;
                    
            $total_amount_payable = $monthly_payment * $loan_tenure * 12;
            $total_interest_payable = $total_amount_payable - $loan_amount;
            
            $alternative_funding = array(
                'loan_id'         => $id,
                'type'            => "peertopeer_funding",
                'int_rate'        => $int_rates,
                'loan_amount'     => $loan_amount,
                'loan_tenure'     => $loan_tenure,
                'monthly_payment' => $monthly_payment,
            );
            $session->offsetSet('loan', $alternative_funding);
            $redirect = '/alternative-funding/funding-apply';
            
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => true, "redirect" => $redirect) ) );
            return $response;  
        }
        if($session->offsetExists('loan')) {
            $loan = $session->offsetGet('loan');
            return array("loan" => $loan);
        }
        return $this->redirect()->toRoute("alternative_funding", array("action" => "funding"));
    }
    
    public function fundingApplyFormAction() {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session = new Session('business_loan');
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $application_view_helper_setting = $viewHelperManager->get('setting');
        $user = $application_view_helper_auth();
        $setting = $application_view_helper_setting();
        $request = $this->getRequest();
        if($request->isPost()) {
            $post = $request->getPost();
            $success = false;
            if($session->offsetExists('loan')) {
                $loan = $session->offsetGet('loan');
                $translator = $this->getServiceLocator()->get('translator');
                $application_model_alternative_funding = $this->getServiceLocator()->get('application_model_business_loan');
                $alternative_funding = new \Application\Entity\PersonalLoan;
                $alternative_funding->setUserId($post['user_id']);
                $alternative_funding->setLoanId($loan['loan_id']);
                $alternative_funding->setType($post['type']);
                $firstname = $post['firstname'];
                $lastname = $post['lastname'];
                $name = $firstname.' '.$lastname;
                $alternative_funding->setName($name);
                $alternative_funding->setEmail($post['email']);
                $alternative_funding->setPhone($post['phone']);
                $alternative_funding->setCompanyName($post['company_name']);
                $alternative_funding->setRemark($post['remark']);
                $alternative_funding->setIntRate($loan['int_rate']);
                $alternative_funding->setLoanAmount($loan['loan_amount']);
                $alternative_funding->setLoanTenure($loan['loan_tenure']);
                $alternative_funding->setMonthlyPayment($loan['monthly_payment']);
                $alternative_funding->setDateAdded(new Expression('NOW()'));
                $alternative_funding->setStatus(0);
                $added = $application_model_alternative_funding->insert($alternative_funding);
                if($added) {
                    $success = true;
                    $redirect = "/alternative-funding/success";
                    
                    // Send email
                    $application_view_helper_send_email = $viewHelperManager->get('send_email');
                    
                    $html = '<p>Name: <b>'.$name.'</b></p>';
                    $html .= '<p>Email: <b>'.$post['email'].'</b></p>';
                    $html .= '<p>Phone: <b>'.$post['phone'].'</b></p>';
                    if($post['company_name']) $html .= '<p>Company Name: <b>'.$post['company_name'].'</b></p>';
                    if($post['remark']) $html .= '<p>Remark: <b>'.$post['remark'].'</b></p>';
                    $application_view_helper_send_email("You have a funding application form", $html, 'Best regard');
                    
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
                                $referral->setStatus(2);
                                $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                                $application_model_referral->insert($referral);
                            }
                        }
                    }
                    
                    // Unset session funding
                    $session->offsetSet('success', 1);
                    $session->offsetUnset('loan');
                }
            } else {
                $redirect = "/alternative-funding/funding-apply";
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
            return $response;
        }
        if($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            $application_view_helper_auth = $viewHelperManager->get('auth');
            $user = $application_view_helper_auth();
            
            if($session->offsetExists('loan')) {
                $loan = $session->offsetGet('loan');
                return array("loan" => $loan, "user" => $user);
            }
        } else {
            $session->offsetSet('redirect', '/alternative-funding/funding-apply-form');
            return $this->redirect()->toRoute("frontend_user", array("action" => "auth"));    
        }
    }
    
    public function fundingCompareAction() {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];
            $success = false;
            
            $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
            $application_view_helper_setting = $viewHelperManager->get('setting');
            $max_loan_compare = $application_view_helper_setting()->max_loan_compare;
            if($session->offsetExists('compare')) {
                $compare = $session->offsetGet('compare');
                $compare_arr = $compare;
                $current_count_compare = count($compare_arr);    
            } else {
                $current_count_compare = 0;
                $compare_arr = array();
            }
            
            $success = false;
            $cr = "active";
            $ca = "";
            
            if(!in_array($id, $compare_arr)) {
                if($current_count_compare < $max_loan_compare) {
                    array_push($compare_arr, $id);
                    $success = true;
                    $cr = "";
                    $ca = "active";
                } else {
                    $msg = $translator->translate("Sorry you haven't added any bank to compare");
                }
            } else {
                if(($key = array_search($id, $compare_arr)) !== false) {
                    unset($compare_arr[$key]);
                    $success = true;
                    $msg = $translator->translate("You have removed this bank compare list");
                }
            }
            $session->offsetSet('compare', $compare_arr);
            $alternative_funding = array(
                'loan_amount'     => $loan_amount,
                'loan_tenure'     => $loan_tenure,
            );
            $session->offsetSet('loan', $alternative_funding);
            
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success,"msg" => $msg , "cr" => $cr, "ca" => $ca) ) );
            return $response;  
        }
        if($session->offsetExists('compare')) $compare = $session->offsetGet('compare');
        if($session->offsetExists('loan')) $loan = $session->offsetGet('loan');      
        return array("loan" => $loan, "compare" => $compare);
    }
    
    public function fundingLoadCompareAction() {
        $session = new Session('business_loan');
        $count = 0;
        
        $html = '<div class="row">';
        if($session->offsetExists('compare')) {
            $compare = $session->offsetGet('compare');
            if(count($compare) > 0) {
                $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath'); 
                $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                foreach ($compare as $key => $loan_id) {
                    $loan = $application_model_business_loan_package->fetchRow(array("id" => $loan_id));
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    // Image
                    $dir_logo = 'data/bank/'.$loan->getBankId().'/m_'.$bank->getLogo();
                    if(!file_exists($dir_logo)) {
                        $dir_logo = 'data/image/no-image-64.png';
                    } 
                    $html .= '<div class="col-xs-4 drawercard-col">';
                    $html .= '<div class="drawercard-container filled" data-original-title="'.$loan->getLoanTitle().'" title="'.$loan->getLoanTitle().'">';
                    $html .= '<i class="fa fa-times" onclick="Loan.clear_compare(this)" data-id="'.$loan->getId().'"></i>';
                    $html .= '<a href=""><img src="'.$basePath($dir_logo).'" alt="'.$bank->getName().'" /></a>';
                    $html .= '</div>';
                    $html .= '<p>'.$loan->getLoanTitle().'</p>';
                    $html .= '</div>';
                }
                $count = count($compare);
            }
        } 
        $html .= '</div>';
        $response = $this->getResponse();
        $response->setContent ( \Zend\Json\Json::encode ( array("html" => $html, "count" => count($compare)) ) );
        return $response; 
    }
    
    public function fundingClearCompareAction() {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if($request->isPost()) {
            $success = true;
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            
            if($session->offsetExists('compare')) {
                $compare = $session->offsetGet('compare');
                $compare_arr = $compare;  
                
                if($id > 0) {
                    if(($key = array_search($id, $compare_arr)) !== false) {
                        unset($compare_arr[$key]);
                    }
                } else {
                    unset($compare_arr);
                    $compare_arr = array();
                }
                $msg = $translator->translate("You have removed this bank compare list");
                $session->offsetSet('compare', $compare_arr);
                $count = count($compare_arr);
            } else {
                $count = 0;
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "msg" => $msg, "count" => $count) ) );
            return $response; 
        }
    }
    
    public function successAction() {
        $session = new Session('business_loan');
        $success = $session->offsetExists('success');
        $session->offsetUnset('redirect');
        if(empty($success)) return $this->redirect()->toRoute("alternative_funding", array("action" => "factoring"));
    }
}