<?php
namespace Frontend\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class PersonalLoanController extends AbstractActionController
{
    public function indexAction()
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
            $loans = $application_model_business_loan_package->fetchAll(array("status" => 1, "type" => "bank"));
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
                $html .= '<div class="col-md-4"><span class="head-title-processing" data-field="processing"><a href="javascript:;" onclick="Loan.sort(this)">' . $translator->translate("Processing Fee") .'</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-penalty" data-field="penalty"><a href="javascript:;" onclick="Loan.sort(this)">' . $translator->translate("Penalty Fee") .'</a></span></div>';
                $html .= '</div></div>';
                $html .= '</div>';
                
                $loan_amount = $post['loan_amount'] ? (int) $post['loan_amount'] : 0;
                $loan_tenure = $post['loan_tenure'] ? (int) $post['loan_tenure'] : 0;
                
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
                    $html .= '<div class="col-md-2"><a href="#"><img src="' . $basePath($dir_logo) .'" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></div>';
                    
                    // Interest rate
                    $interest_rate = \Zend\Json\Json::decode($loan->getInterestRate());
                    if (count($interest_rate) > 0)
                    {
                        foreach ($interest_rate as $key => $value)
                        {
                            $condition = $value->condition;
                            $percentage = $value->percentage;
                            $condition = str_replace('{m}', $loan_amount, $condition);
                            $str = '$result = (bool)(' . $condition . ');';
                            eval($str);
                            if ($result)
                            {
                                //$int_rates = $year->{$loan_tenure};
                                $int_rates = $percentage;
                            }
                        }
                    }
                    // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
                    // Where: r = decimal rate / 12.
                    $r = ($int_rates / 100) / 12;
                    if($int_rates <= 0) $r = 1;
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
                    $get_max_loan_amt = $loan->getMaxLoanAmt() ? $loan->getMaxLoanAmt() : 0;
                    $get_max_tenure = $loan->getMaxTenure() ? $loan->getMaxTenure() : 0;
                    $min_sales_turnover = $post['min_sales_turnover'] ? (int) $post['min_sales_turnover'] : 0;
                    $min_years_of_incorporation = $post['min_years_of_incorporation'] ? (int) $post['min_years_of_incorporation'] : 0;
                    $loan_min_sales_turnover = $loan->getMinTurnover() ? $loan->getMinTurnover() : 0;
                    $loan_years_of_incorporation = $loan->getMinYearsIncorporation() ? $loan->getMinYearsIncorporation() : 0;
                    if ($loan_amount <= $get_max_loan_amt && $loan_tenure <= $get_max_tenure && $min_sales_turnover >= $loan_min_sales_turnover && $min_years_of_incorporation >= $loan_years_of_incorporation)
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
                    $html .= '<div class="col-xs-4 box__interest"><span class="interest" data-value="'.$total_interest_payable.'"><b>$' .number_format($total_interest_payable, 2) . '</b>' . $translator->translate("Interest Total") .'</span></div>';
                    //$html .= '<div class="col-xs-4 box__processing"><span class="processing" data-value="'.$loan->getProcessingFee().'"><b>' .$loan->getProcessingFee() . '</b>' . $translator->translate("Processing Fee") .'</span></div>';
                    //$html .= '<div class="col-xs-4 box__penalty"><span class="penalty" data-value="'.$loan->getPrepaymentPenaltyFee().'"><b>'.$loan->getPrepaymentPenaltyFee().'</b>' .$translator->translate("Penalty Fee") . '</span></div>';
                    $html .= '<div class="col-xs-4 box__processing"><span class="processing"><b>' .$loan->getProcessingFee() . '</b>' . $translator->translate("Processing Fee") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__penalty"><span class="penalty"><b>'.$loan->getPrepaymentPenaltyFee().'</b>' .$translator->translate("Penalty Fee") . '</span></div>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    $html .= '<div class="row row-footer">';
                    $html .= '<div class="col-md-12 summary-details">';
                    $html .= '<div class="row">
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("INT RATES") .'</strong></div>
                                <div class="col-md-8 line-top">' . $loan->getIntRate() . '</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("Min Turnover") .'</strong></div>
                                <div class="col-md-8 line-top">' . $loan->getMinTurnover() . '</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-4 line-top"><strong>' . $translator->translate("Principle Loan Amount") .'</strong></div>
                                <div class="col-md-8 line-top">' . number_format($loan_amount) . '</div>
                            </div>
                        </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Max Tenure") .'</strong></div>
                            <div class="col-md-8 line-top">' . $loan->getMaxTenure() . '</div></div>
                            <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Min Years Incorporation") .'</strong></div>
                            <div class="col-md-8 line-top">' . $loan->getMinYearsIncorporation() . '</div>
                            </div>
                            <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>' . $translator->translate("Monthly Instalment") .'</strong></div>
                            <div class="col-md-8 line-top">$' . number_format($monthly_payment, 2) . '</div>
                            </div>
                        </div>';
                        
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Max Loan Amount") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getMaxLoanAmt() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Min Age") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getMinAge() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Interest Rate (per annum)") .'</strong></div>
                        <div class="col-md-8 line-top">' . $int_rates . '%</div>
                        </div>
                        </div>';
                        
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Annual Fee") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getAnnualFee() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Bankruptcy") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getBankruptcy() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Loan Tenure") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan_tenure . ' years</div>
                        </div>
                        </div>';
                        
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Lock In Period") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getLockInPeriod() . '</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Amount Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_amount_payable, 2) .'</div>
                        </div>
                        </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Restructuring Of Loan Tenor") .'</strong></div>
                        <div class="col-md-8 line-top">' . $loan->getRestructuringOfLoanTenor() . '</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Interest Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_interest_payable, 2) .'</div>
                        </div>
                        </div>';
                    /*    
                    $html .= '<div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-4 line-top"><strong>' . $translator->translate("Total Interest Payable") .'</strong></div>
                        <div class="col-md-8 line-top">$' . number_format($total_interest_payable, 2) .'</div>
                        </div>
                        </div>';*/
                        
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
    
    public function applyAction()
    {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $post = $request->getPost();
            $id = $post['id'];
            $category_id = $post["category_id"];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            $loan = $application_model_business_loan_package->fetchRow(array("id" => $id));
            // Interest rate
            $interest_rate = \Zend\Json\Json::decode($loan->getInterestRate());
            if (count($interest_rate) > 0)
            {
                foreach ($interest_rate as $key => $value)
                {
                    /*$condition = $value->condition;
                    $year = $value->year;
                    $condition = str_replace('{m}', $loan_amount, $condition);
                    $str = '$result = (bool)(' . $condition . ');';
                    eval($str);
                    if ($result)
                    {
                        $int_rates = $year->{$loan_tenure};
                    }*/
                    $condition = $value->condition;
                    $percentage = $value->percentage;
                    $condition = str_replace('{m}', $loan_amount, $condition);
                    $str = '$result = (bool)(' . $condition . ');';
                    eval($str);
                    if ($result) {
                        $int_rates = $percentage;
                    }
                }
            }
            // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
            // Where: r = decimal rate / 12.
            $r = ($int_rates / 100) / 12;
            $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) - 1)) * $loan_amount;
            $total_amount_payable = $monthly_payment * $loan_tenure * 12;
            $total_interest_payable = $total_amount_payable - $loan_amount;
            $personal_loan = array(
                'loan_id' => $id,
                'type' => "business_loan",
                "category_id" => $category_id,
                'int_rate' => $int_rates,
                'loan_amount' => $loan_amount,
                'loan_tenure' => $loan_tenure,
                'monthly_payment' => $monthly_payment,
            );
            $session->offsetSet('loan', $personal_loan);
            $redirect = $router->assemble(array("action" => "apply"), array('name' =>'personal_loan'));
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("success" => true, "redirect" => $redirect)));
            return $response;
        }
        if ($session->offsetExists('loan'))
        {
            $loan = $session->offsetGet('loan');
            return array("loan" => $loan);
        }
        return $this->redirect()->toRoute("personal_loan", array("action" => "index"));
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
        if ($this->getServiceLocator()->get('AuthService')->hasIdentity())
        {
            $application_view_helper_auth = $viewHelperManager->get('auth');
            $user = $application_view_helper_auth();
            if ($session->offsetExists('loan'))
            {
                $loan = $session->offsetGet('loan');
                return array("loan" => $loan, "user" => $user);
            }
        } else {
            $session_user = new Session('user');
            $session_user->offsetSet('redirect', $router->assemble(array("action" => "apply-form"), array('name' => 'personal_loan')));
            return $this->redirect()->toRoute("frontend_user", array("action" => "auth"));
        }
    }
    
    public function successAction()
    {
        $session_user = new Session('user');
        $session_user->offsetUnset('redirect');
        
        $session = new Session('business_loan');
        $success = $session->offsetExists('success');
        if (empty($success)) return $this->redirect()->toRoute("personal_loan", array("action" => "index"));
    }
    
    public function compareAction()
    {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];
            $success = false;
            $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
            $application_view_helper_setting = $viewHelperManager->get('setting');
            $max_loan_compare = $application_view_helper_setting()->max_loan_compare;
            if ($session->offsetExists('compare'))
            {
                $compare = $session->offsetGet('compare');
                $compare_arr = $compare;
                $current_count_compare = count($compare_arr);
            } else
            {
                $current_count_compare = 0;
                $compare_arr = array();
            }
            $success = false;
            $cr = "active";
            $ca = "";
            if (!in_array($id, $compare_arr))
            {
                if ($current_count_compare < $max_loan_compare)
                {
                    array_push($compare_arr, $id);
                    $success = true;
                    $cr = "";
                    $ca = "active";
                } else
                {
                    $msg = $translator->translate("Sorry you haven't added any bank to compare");
                }
            } else
            {
                if (($key = array_search($id, $compare_arr)) !== false)
                {
                    unset($compare_arr[$key]);
                    $success = true;
                    $msg = $translator->translate("You have removed this bank compare list");
                }
            }
            $session->offsetSet('compare', $compare_arr);
            $personal_loan = array(
                'loan_amount' => $loan_amount,
                'loan_tenure' => $loan_tenure,
                );
            $session->offsetSet('loan', $personal_loan);
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array(
                "success" => $success,
                "msg" => $msg,
                "cr" => $cr,
                "ca" => $ca)));
            return $response;
        }
        if ($session->offsetExists('compare'))
            $compare = $session->offsetGet('compare');
        if ($session->offsetExists('loan'))
            $loan = $session->offsetGet('loan');
        
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariables(array("loan" => $loan, "compare" => $compare));
        return $viewModel;
    }
    
    public function loadCompareAction()
    {
        $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
        $session = new Session('business_loan');
        $count = 0;
        $html = '<div class="row">';
        if ($session->offsetExists('compare'))
        {
            $compare = $session->offsetGet('compare');
            if (count($compare) > 0)
            {
                $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                foreach ($compare as $key => $loan_id)
                {
                    $loan = $application_model_business_loan_package->fetchRow(array("id" => $loan_id));
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    // Image
                    $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
                    if (!file_exists($dir_logo))
                    {
                        $dir_logo = 'data/image/no-image-64.png';
                    }
                    $html .= '<div class="col-xs-4 drawercard-col">';
                    $html .= '<div class="drawercard-container filled" data-original-title="' . $loan->getLoanTitle() . '" title="' . $loan->getLoanTitle() . '">';
                    $html .= '<i class="fa fa-times" onclick="Loan.clear_compare(this)" data-id="' .$loan->getId() . '"></i>';
                    $html .= '<a href=""><img src="' . $basePath($dir_logo) . '" alt="' . $bank->getName() .'" /></a>';
                    $html .= '</div>';
                    $html .= '<p>' . $loan->getLoanTitle() . '</p>';
                    $html .= '</div>';
                }
                $count = count($compare);
            }
        }
        $html .= '</div>';
        $response = $this->getResponse();
        $response->setContent(\Zend\Json\Json::encode(array("html" => $html, "count" =>
                count($compare))));
        return $response;
    }
    
    public function clearCompareAction()
    {
        $session = new Session('business_loan');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $success = true;
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            if ($session->offsetExists('compare'))
            {
                $compare = $session->offsetGet('compare');
                $compare_arr = $compare;
                if ($id > 0)
                {
                    if (($key = array_search($id, $compare_arr)) !== false)
                    {
                        unset($compare_arr[$key]);
                    }
                } else
                {
                    unset($compare_arr);
                    $compare_arr = array();
                }
                $msg = $translator->translate("You have removed this bank compare list");
                $session->offsetSet('compare', $compare_arr);
                $count = count($compare_arr);
            } else
            {
                $count = 0;
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array(
                "success" => $success,
                "msg" => $msg,
                "count" => $count)));
            return $response;
        }
    }
}
