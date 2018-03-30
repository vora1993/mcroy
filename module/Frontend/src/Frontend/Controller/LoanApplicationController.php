<?php
namespace Frontend\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class LoanApplicationController extends AbstractActionController
{
    public function businessLoanAction()
    {
        $session = new Session('business_loan');
        $session->offsetUnset('success');
        $request = $this->getRequest();
        $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
        $testimonials = $application_model_testimonial->fetchAll(array("status" => 1));
        if ($request->isPost())
        {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');

            $loans = $application_model_business_loan_package->fetchAll(array("status" => 1, "category_id" => $post['category_id']));

            //Load helper setting for tooltip
            $application_model_setting = $this->getServiceLocator()->get('application_model_setting');

            $settings = $application_model_setting->fetchAll();
            $obj = new \stdClass;
            foreach ($settings as $setting) {
                $key = $setting->getKey();
                $value = $setting->getValue();
                $obj->{$key} = $value;
            }

            $html = '';
            if (count($loans) > 0)
            {
                /*$html .= '<div class="row filters-table-head hidden-sm hidden-xs">';
                $html .= '<div class="col-md-2" style="text-align:center"><span class="head-title-bank" data-field="bank"><a href="javascript:;">' .$translator->translate("Bank") . '</a></span></div>';
                $html .= '<div class="col-md-5"><div class="row">';
                $html .= '<div class="col-md-4" style="text-align:center"><span class="head-title-rate" data-field="rate"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Rate") . '</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-requirement" data-field="requirement"><a title="' .$translator->translate("Applicable") .'" href="javascript:;">' . $translator->translate("Min requirement") .'</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-monthly-instalments" data-field="month"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Monthly Installment") . '</a></span></div>';
                $html .= '</div></div>';
                $html .= '<div class="col-md-5"><div class="row">';
                $html .= '<div class="col-md-4"><span class="head-title-interest" data-field="interest"><a href="javascript:;" onclick="Loan.sort(this)">' .$translator->translate("Total Interest Payable") . '</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-processing" data-field="processing"><a href="javascript:;">' . $translator->translate("Processing Fee") .'</a></span></div>';
                $html .= '<div class="col-md-4"><span class="head-title-penalty" data-field="penalty"><a href="javascript:;">' . $translator->translate("Penalty Fee") .'</a></span></div>';
                $html .= '</div></div>';
                $html .= '</div>';*/

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
                        //$html .= '<div class="filters-content sponsored">';
                        $html .= '<div class="filters-content not-sponsored">';
                    } else
                    {
                        $html .= '<div class="filters-content not-sponsored">';
                    }
                    $html .= '<div class="row-header"><h4 class="bank-title"  style="background-color: ' . $bank->getColor() .'"><a href="#">' . $loan->getLoanTitle() . '</a></h4>';
                    $html .= '</div>';
                    $html .= '<div class="row row-content">';

                    // Image
                    $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
                    if (!file_exists($dir_logo)) {
                        $dir_logo = 'data/image/no-image-64.png';
                    }
                    $html .= '<div class="col-md-2"><a href="#"><img src="' . $basePath($dir_logo) . '" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></div>';

                    // Interest rate
                    $int_rates=$loan->getIntRate();
                    if($loan->getInterestRate()) {
                        $interest_rate = \Zend\Json\Json::decode($loan->getInterestRate());
                        if (count($interest_rate) > 0) {
                            foreach ($interest_rate as $key => $value) {
                                if($value->condition!='' && $value->percentage!='')
                                {
                                    $condition = $value->condition;
                                    $percentage = $value->percentage;
                                    if($condition && $percentage) {
                                        $condition = str_replace('{m}', $loan_amount, $condition);
                                        $condition = str_replace(',','', $condition);
                                        $str = '$result = (bool)(' . $condition . ');';
                                        eval($str);
                                        if ($result) {
                                            //$int_rates = $year->{$loan_tenure};
                                            $int_rates = $percentage;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // Monthly payment = [ r + r / ( (1+r) ^ months -1) ] x principal loan amount
                    // Where: r = decimal rate / 12.
                    $r = ($int_rates / 100) / 12;
                    if($int_rates <= 0) $r = 1;
                    // $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) - 1)) * $loan_amount;
                    // $monthly_payment=($int_rates/100)*$loan_amount;
                    // $total_amount_payable =$loan_amount+ ($monthly_payment * $loan_tenure);
                    // $total_interest_payable = $total_amount_payable - $loan_amount;

                    //Only p2p module
                    $unit_rate='year';
                    $unit_loan_tenure='years';
                    if($post['seo']=='p2p-lending')
                    {
                        $unit_rate='month';
                        $unit_loan_tenure='month';
                        $loan_tenure_compare=$loan_tenure*12;
                        $monthly_payment=($loan_amount/$loan_tenure)+(($int_rates/100)*$loan_amount);;
                        $total_amount_payable =$loan_amount+ ($monthly_payment * $loan_tenure);
                        $total_interest_payable=$monthly_payment*$loan_tenure-$loan_amount;
                    }else
                    {
                        $loan_tenure_compare=$loan_tenure;
                        $monthly_payment = ($r + $r / (pow(1 + $r, ($loan_tenure * 12)) - 1)) * $loan_amount;
                        $total_amount_payable = $monthly_payment * $loan_tenure * 12;
                        $total_interest_payable = $total_amount_payable - $loan_amount;
                    }

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
                    $unit_interest_rate='';
                    if($post['seo']=='p2p-lending')
                    {
                        $get_max_tenure_compare=$get_max_tenure*12;
                        $unit_interest_rate='per month';
                    }else
                    {
                        $get_max_tenure_compare=$get_max_tenure;
                    }
                    $min_sales_turnover = $post['min_sales_turnover'] ? (int) $post['min_sales_turnover'] : 0;
                    $min_years_of_incorporation = $post['min_years_of_incorporation'] ? (int) $post['min_years_of_incorporation'] : 0;
                    $loan_min_sales_turnover = $loan->getMinTurnover() ? $loan->getMinTurnover() : 0;
                    $loan_years_of_incorporation = $loan->getMinYearsIncorporation() ? $loan->getMinYearsIncorporation() : 0;
                    if ($loan_amount <= $get_max_loan_amt && $loan_tenure_compare <= $get_max_tenure_compare && $min_sales_turnover >= $loan_min_sales_turnover && $min_years_of_incorporation >= $loan_years_of_incorporation)
                    {
                        $dir_c = 'assets/img/checked-yes.png';
                        $checked = "yes";
                    } else {
                        $dir_c = 'assets/img/checked-no.png';
                        $checked = "no";
                    }

                    $html .= '<div class="col-md-5"><div class="row">';
                    $html .= '<div class="col-xs-4 box__rate"><span class="rate" data-value="' . $int_rates.'"><b>' . $int_rates . '% '.$unit_interest_rate.'</b>' . $translator->translate("Interest Rate") .'</span></div>';
                    $arr_check=[
                        'no'=>0,
                        'yes'=>1
                    ];
                    $html .= '<div class="col-xs-4 box__requirement"><span class="requirement" data-value="' .$arr_check[$checked] . '"><img src="' . $basePath($dir_c) . '" /><br/>' . $translator->translate("Min requirement") . '</span></div>';
                    $html .= '<div class="col-xs-4 box__month"><span class="month" data-value="' . $monthly_payment .'"><b>$' . number_format($monthly_payment) . '</b>' . $translator->translate("Per Month") .'</span></div>';
                    $html .= '</div></div>';
                    $html .= '<div class="col-md-5"><div class="row">';
                    $html .= '<div class="col-xs-4 box__interest"><span class="interest" data-value="'.$total_interest_payable.'"><b>$' .number_format($total_interest_payable) . '</b>' . $translator->translate("Interest Total") .'</span></div>';
                    //$html .= '<div class="col-xs-4 box__processing"><span class="processing" data-value="'.$loan->getProcessingFee().'"><b>' .$loan->getProcessingFee() . '</b>' . $translator->translate("Processing Fee") .'</span></div>';
                    //$html .= '<div class="col-xs-4 box__penalty"><span class="penalty" data-value="'.$loan->getPrepaymentPenaltyFee().'"><b>'.$loan->getPrepaymentPenaltyFee().'</b>' .$translator->translate("Penalty Fee") . '</span></div>';
                    $html .= '<div class="col-xs-4 box__processing"><span class="processing"><b>' .$loan->getProcessingFee() . '</b>' . $translator->translate("Processing Fee") .'</span></div>';
                    $html .= '<div class="col-xs-4 box__penalty"><span class="penalty" data-value="'.str_replace('%','',$loan->getPrepaymentPenaltyFee()).'"><b>'.$loan->getPrepaymentPenaltyFee().'</b>' .$translator->translate("Penalty Fee") . '</span></div>';
                    $html .= '</div></div>';
                    if($loan->getPromotions()) $html .= '<div class="vourcher"><span>'.$loan->getPromotions().'</span></div>';
                    $html .= '</div>'; // End row-content

                    $html .= '<div class="row row-footer">';
                    $html .= '<div class="col-md-12 summary-details">';
                    $html .= '<div class="row">
                            <div class="col-md-4">
                                <div class="col-md-8"><strong>' . $translator->translate("INT RATES") .'</strong></div>
                                <div class="col-md-4">' . $int_rates . ' %</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-8"><strong>' . $translator->translate("Min Turnover") .'</strong>

                                        <a href="#" data-toggle="tooltip" data-placement="top" title="'.$obj->min_turnover.'"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></a>
                                </div>
                                <div class="col-md-4">$' . number_format($loan->getMinTurnover()) . '</div>
                            </div>
                            <div class="col-md-4">
                                <div class="col-md-8"><strong>' . $translator->translate("Principle Loan Amount") .'</strong></div>
                                <div class="col-md-4">$' . number_format($loan_amount) . '</div>
                            </div>
                        </div>';

                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-8"><strong>' . $translator->translate("Max Tenure") .'</strong>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="'.$obj->max_tenure.'"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></a>
                            </div>
                            <div class="col-md-4">' . $loan->getMaxTenure() .' Year</div></div>
                            <div class="col-md-4">
                            <div class="col-md-8"><strong>' . $translator->translate("Min Years Incorporation") .'</strong>
                                <a href="#" data-toggle="tooltip" data-placement="top" title="'.$obj->min_years_incorporation.'"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></a>
                            </div>
                            <div class="col-md-4">' . $loan->getMinYearsIncorporation() . ' Year</div>
                            </div>
                            <div class="col-md-4">
                            <div class="col-md-8"><strong>' . $translator->translate("Monthly Instalment") .'</strong></div>
                            <div class="col-md-4">$' . number_format(round($monthly_payment,2), 2) . '</div>
                            </div>
                        </div>';

                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Max Loan Amount") .'</strong></div>
                        <div class="col-md-4">$' . number_format($loan->getMaxLoanAmt()) . '</div>
                        </div>

                        <div class="col-md-4">
                        <div class="col-md-8 "><strong>' . $translator->translate("Penalty Fee") .'</strong></div>
                        <div class="col-md-4">' . $loan->getPrepaymentPenaltyFee() . '</div>
                        </div>

                        <div class="col-md-4">

                        <div class="col-md-8"><strong>' . $translator->translate("Interest Rate (per ".$unit_rate.")") .'</strong></div>

                        <div class="col-md-4">' . $int_rates . '%</div>
                        </div>
                        </div>';

                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Annual Fee") .'</strong>
                            <a href="#" data-toggle="tooltip" data-placement="top" title="'.$obj->annual_fee.'"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></a>
                        </div>
                        <div class="col-md-4">' . $loan->getAnnualFee() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-8 hide"><strong>' . $translator->translate("Bankruptcy") .'</strong></div>
                        <div class="col-md-4 hide">' . $loan->getBankruptcy() . '</div>
                        </div>
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Loan Tenure") .'</strong></div>
                        <div class="col-md-4">' . $loan_tenure .' '. $unit_loan_tenure.'</div>
                        </div>
                        </div>';
                    $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Lock In Period") .'</strong>
                            <a href="#" data-toggle="tooltip" data-placement="top" title="'.$obj->lock_in_period.'"><i class="fa fa-exclamation-circle" aria-hidden="true"></i></a></a>
                        </div>
                        <div class="col-md-4">' . $loan->getLockInPeriod() . '</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Total Amount Payable") .'</strong></div>
                        <div class="col-md-4">$' . number_format(round($total_interest_payable+$loan_amount,2), 2) .'</div>
                        </div>
                        </div>';

                    /*Restructuring of loan tenor.
                        $html .= '<div class="row">
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Restructuring Of Loan Tenor") .'</strong></div>
                        <div class="col-md-4">' . $loan->getRestructuringOfLoanTenor() . '</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                        <div class="col-md-8"><strong>' . $translator->translate("Total Interest Payable") .'</strong></div>
                        <div class="col-md-4">$' . number_format($total_interest_payable, 2) .'</div>
                        </div>
                        </div>';
                    */

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
                        <div class="col-md-8 col-sm-6 col-xs-12 button-gr button-gr-s">
                        <a href="javascript:void(0)" class="btn btn-more-detail">' . $translator->translate("Details") . '<i class="fa fa-angle-down"></i></a>
                        <a href="javascript:void(0)" class="btn btn-less-detail" style="display: none;">' .$translator->translate("Close") . '<i class="fa fa-angle-up"></i></a>';
                    $html .= '</div>';
                    $html .= '<div class="col-md-4 col-sm-6 col-xs-12 button-gr button-gr-s1">';
                    $html .= '<div class="col-md-6 col-xs-12 box__compare"><button type="button" onclick="Loan.compare(this)" data-id="' .$loan->getId() . '" class="btn btn-lg btn-block ladda-button compare' . $class . '" data-style="slide-up" title="' . $translator->translate("Compare") . '"><i class="fa fa-copy"></i><span>' . $translator->translate("Compare") . '</span></button></div>';
                    $html .= '<div class="col-md-6 col-xs-12 box__apply"><button type="button" onclick="Loan.apply(this)" data-id="' .$loan->getId() . '" class="btn yellow-gold btn-lg btn-block ladda-button" data-style="slide-up" title="' .$translator->translate("Apply") . '"><i class="fa fa-check-square-o"></i> ' . $translator->translate("Apply") .'</button></div>';
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
        $seo = $this->params()->fromRoute('seo');
        $application_model_category = $this->getServiceLocator()->get('application_model_category');
        $category = $application_model_category->fetchRow(array("seo" => $seo, "type" => "business_loan"));
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array("type" => "business_loan"));

        $application_model_bank_interest_rate = $this->getServiceLocator()->get('application_model_bank_interest_rate');
        $interest_rate = $application_model_bank_interest_rate->fetchAllSort(array("status" => 1,"display"=>$seo));
        // Count apply today
        $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
        $business_loan = $application_model_business_loan->fetchDate(array("category_id" => $category->getId()), date("d"), date("m"), date("Y"));

        return array("category" => $category, "faq" => $faq, "count" => count($business_loan),"seo"=>$seo,"interest_rate"=>$interest_rate, "testimonials" => $testimonials);
    }

    public function propertyLoanAction()
    {
        $seo = $this->params()->fromRoute('seo');
        $application_model_category = $this->getServiceLocator()->get('application_model_category');
        $category = $application_model_category->fetchRow(array("seo" => $seo, "type" => "property_loan"));

        $id = $this->params()->fromRoute('id') ? $this->params()->fromRoute('id') : 0;  // From RouteMatch
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $session = new Session('home_loan');
        $request = $this->getRequest();
        $response = $this->getResponse();
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $success = false;
        switch($id){
            case 1:
                $session->offsetUnset('apply');
                if ($request->isPost()) {
                    $post = $request->getPost();
                    // 1 2 3
                    $property        = $post['property'];
                    $property_type   = $post['property_type'];
                    $project_name    = $post['project_name'];
                    $property_status = $post['property_status'];
                    $option_fee      = $post['option_fee'];
                    $offer_opts      = $post['offer_opts'];
                    // 4 5
                    $existing_home_loans    = $post['existing_home_loans'];
                    $purchase_price         = $post['purchase_price'];
                    $loan_amount            = $post['loan_amount'];
                    $loan_tenure            = $post['loan_tenure'];
                    $loan_percent           = $post['loan_percent'];
                    $preferred_rate_package = $post['preferred_rate_package'];
                    $corporate_entity=$post['corporate_entity'];
                    if($property && $property_type && $property_status && $option_fee && $loan_amount && $loan_tenure) {
                        $session->offsetSet('property', $property);
                        $session->offsetSet('property_type', $property_type);
                        $session->offsetSet('project_name', $project_name);
                        $session->offsetSet('property_status', $property_status);
                        $session->offsetSet('option_fee', $option_fee);
                        $session->offsetSet('offer_opts', $offer_opts);

                        $session->offsetSet('existing_home_loans', $existing_home_loans);
                        $session->offsetSet('purchase_price', $purchase_price);
                        $session->offsetSet('loan_amount', $loan_amount);
                        $session->offsetSet('loan_tenure', $loan_tenure);
                        $session->offsetSet('loan_percent', $loan_percent);
                        $session->offsetSet('preferred_rate_package', $preferred_rate_package);
                        $session->offsetSet('corporate_entity', $corporate_entity);

                        $success = true;
                        return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 2));
                    } else {
                        return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 1));
                    }
                }
            break;

            case 2:
                $purchase_price = $session->offsetGet('purchase_price') ? $session->offsetGet('purchase_price') : 0;
                if(!$session->offsetExists('existing_home_loans') && $purchase_price <= 0) {
                    return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 1));
                }

                $loan_id = $session->offsetGet('apply');
                if($this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 3));
                }
                if($loan_id > 0) $redirect = $router->assemble(array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 4), array('name' => "loan_application"));
                else $redirect = $router->assemble(array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 3), array('name' => "loan_application"));
            break;

            case 3:
                if(!$session->offsetExists('property_type') && !$session->offsetExists('property_status')) {
                    return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 1));
                }
                $purchase_price = $session->offsetGet('purchase_price') ? $session->offsetGet('purchase_price') : 0;
                if(!$session->offsetExists('existing_home_loans') && $purchase_price <= 0) {
                    return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 2));
                }
                if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    //return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 3));
                }

                $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_package');
                // $condition = array('category_id' => $category->getId(), 'status' => 1);
                $condition = array('status' => 1);
                // if($session->offsetExists('property') && $session->offsetExists('property')!='') {
                //     $condition['property']=$session->offsetGet('property');
                // }
                $condition['property']='Purchasing';
                // $condition['type']=$session->offsetGet('property_type');
                if($session->offsetExists('corporate_entity') && $session->offsetGet('corporate_entity')!=''){
                    $condition['type_of_corporate']=$session->offsetGet('corporate_entity');
                }
                // if($session->offsetExists('property_type')) {
                //     $condition['type'] = $session->offsetGet('property_type');
                // }
                if($session->offsetExists('property_status') && $session->offsetGet('property_status')!='') {
                    $condition['property_status'] = $session->offsetGet('property_status');
                }
                if($session->offsetExists('preferred_rate_package') && $session->offsetGet('preferred_rate_package')!='') {
                    if($session->offsetGet('preferred_rate_package') === 'Fixed' || $session->offsetGet('preferred_rate_package') === 'Floating') {
                        $condition['package'] = $session->offsetGet('preferred_rate_package');
                    }
                }
                if($session->offsetGet('no_lock_in_only') == 1) {
                    $condition['lock_in_year'] = 0;
                }
                $loans = $application_model_property_loan_bank->fetchFilter($condition);
                if ($request->isPost()) {
                    $post = $request->getPost();
                    $loan_amount              = $post['loan_amount'];
                    $loan_tenure              = $post['loan_tenure'];
                    $loan_percent             = $post['loan_percent'];
                    $total_interest_for_years = $post['total_interest_for_years'];
                    $preferred_rate_package   = $post['preferred_rate_package'];
                    $no_lock_in_only          = $post['no_lock_in_only'];

                    if($loan_amount && $loan_tenure && $loan_percent) {
                        $session->offsetSet('loan_amount', $loan_amount);
                        $session->offsetSet('loan_tenure', $loan_tenure);
                        $session->offsetSet('loan_percent', $loan_percent);
                        $session->offsetSet('total_interest_for_years', $total_interest_for_years);
                        $session->offsetSet('preferred_rate_package', $preferred_rate_package);
                        if($no_lock_in_only == 1) $session->offsetSet('no_lock_in_only', 1);
                        else $session->offsetSet('no_lock_in_only', 0);

                        $success = true;
                        $redirect = $router->assemble(array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 3), array('name' => "loan_application"));
                    } else {
                        $redirect = $router->assemble(array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 2), array('name' => "loan_application"));
                    }

                    $response->setContent ( \Zend\Json\Json::encode ( array("success" => $success, "redirect" => $redirect) ) );
                    return $response;
                }
            break;

            case 4:
                if(!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
                    return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 2));
                }
                $loan_id = $session->offsetGet('apply');
                if($loan_id < 0) {
                    return $this->redirect()->toRoute("loan_application", array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 1));
                } else {
                    $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
                    $loan = $application_model_property_loan_package->fetchRow(array("id" => $loan_id));
                }

                if($request->isPost()) {
                    $post = $request->getPost();
                    $message = "";
                    $phone = $post['phone'];
                    if($phone) $message .= "<p>From phone: $phone</p>";
                    $company = $post['company'];
                    if($company) $message .= "<p>From phone: $company</p>";
                    $message .= '<p>'.$post['message'].'</p>';


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
                    $property_loan->setType('property_loan');
                    $property_loan->setCategoryId($post['category_id']);
                    $property_loan->setPropertyType($session->offsetGet('property_type'));
                    $property_loan->setProjectName($session->offsetGet('project_name'));
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
                        $loan_id = $session->offsetGet('apply');
                        if($loan_id > 0) {
                            $application_model_property_loan_ref = $this->getServiceLocator()->get('application_model_property_loan_ref');
                            $property_loan_ref = new \Application\Entity\PropertyLoanRef;
                            $property_loan_ref->setPropertyLoanId($property_loan_id);
                            $property_loan_ref->setPropertyPackageId($loan_id);
                            $application_model_property_loan_ref->insert($property_loan_ref);
                        }

                        // Send email to Admin
                        //$html = '<p>Loan Amount: $<b>'.number_format($loan_amount).'</b></p>';
                        //$html .= '<p>Loan Tenure: <b>'.$loan_tenure.'</b>%</p>';
                        $html = '<p>Dear Admin,</p>';
                        $html .= '<p>You have one loan application ('.$category->getName().')</p>';
                        $html .= '<p>Please check your admin for more information.</p>';
                        $html .= '<p>'.$message.'</p>';
                        $application_view_helper_send_email("You have a ".$category->getName()." application form", $html, 'Best regard');

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
                //return $this->redirect()->toRoute("page", array("action" => "error", "id" => 404));
                $session->offsetUnset('apply');
            break;
        }
        if($session->offsetExists('property_type')) {
            $property_type = $session->offsetGet('property_type');
        }
        if($session->offsetExists('project_name')) {
            $project_name = $session->offsetGet('project_name');
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
        if($session->offsetExists('apply')) {
            $apply = $session->offsetGet('apply');
        }

        $session_user = new Session('user');
        $session_user->offsetSet('redirect', $redirect);

        $application_model_property_cost_out_play = $this->getServiceLocator()->get('application_model_property_cost_out_play');
        $total_cost_outplay_value = $application_model_property_cost_out_play->fetchRow();

        return array(
            "category"                 => $category,
            "seo"                      => $seo,
            "step"                     => $id,
            "property_type"            => $property_type,
            "project_name"             => $project_name,
            "preferred_rate_package"   => $preferred_rate_package,
            "purchase_price"           => $purchase_price,
            "loan_amount"              => $loan_amount,
            "loan_tenure"              => $loan_tenure,
            "loan_percent"             => $loan_percent,
            "loans"                    => $loans,
            "total_interest_for_years" => $total_interest_for_years,
            "no_lock_in_only"          => $no_lock_in_only,
            "select"                   => $select,
            "apply"                    => $apply,
            "loan"                     => $loan,
            "redirect_url"             => $redirect,
            "total_cost_outplay_value" => $total_cost_outplay_value
        );
    }

    public function popupPropertyLoanAction()
    {
        $session = new Session('home_loan');
        if ($session->offsetExists('select')) {
            $select = $session->offsetGet('select');
            if (count($select) > 0) {
                $application_model_property_loan_package = $this->getServiceLocator()->get('application_model_property_loan_package');
                $property_loan_package = $application_model_property_loan_package->fetchAll(array("id" => $select));
            }
        }
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariables(array("loans" => $property_loan_package, "count" => count($select)));
        return $viewModel;
    }

    public function selectYesAction() {
        $seo = $this->params()->fromPost('seo');
        $session = new Session('home_loan');
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $request = $this->getRequest();
        $response = $this->getResponse();
        if ($request->isPost()) {
            $redirect = $router->assemble(array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 1), array('name' => "loan_application"));
        }
        $response->setContent ( \Zend\Json\Json::encode ( array("redirect" => $redirect) ) );
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

            $session->offsetSet('loan_amount', 10000);
            $session->offsetSet('loan_tenure', 20);
            $session->offsetSet('loan_percent', 80);
            // $session->offsetSet('preferred_rate_package', 'Floating');
            // $session->offsetSet('existing_home_loans', 'Floating');
            $session->offsetSet('purchase_price', 10000);

            $redirect = $router->assemble(array("action" => "property-loan", "seo" => $seo, "step" => "step", "id" => 3), array('name' => "loan_application"));
        }
        $response->setContent ( \Zend\Json\Json::encode ( array("redirect" => $redirect) ) );
        return $response;
    }

    public function bankAccountAction()
    {
        $session = new Session('bank_account');
        $session->offsetUnset('success');
        $request = $this->getRequest();

        $seo = $this->params()->fromRoute('seo');
        $application_model_category = $this->getServiceLocator()->get('application_model_category');
        $category = $application_model_category->fetchRow(array("seo" => $seo, "type" => "bank_account"));

        if ($request->isPost())
        {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
            $application_model_bank_account_package = $this->getServiceLocator()->get('application_model_bank_account_package');

            $category_id = $post['category_id'];
            $loan_amount = $post['loan_amount'];
            $loan_tenure = $post['loan_tenure'];

            $category = $application_model_category->fetchRow(array("id" => $category_id));
            $session->offsetSet('category_id', $category_id);

            $loans = $application_model_bank_account_package->fetchAll(array("status" => 1, "category_id" => $category_id));
            $html = '';
            if (count($loans) > 0)
            {
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                $html .= '<div class="filters-table-body">';
                foreach ($loans as $k => $loan) {
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

                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    //if ($k == 0) {
                        //$html .= '<div class="filters-content sponsored">';
                    //} else {
                        $html .= '<div class="filters-content not-sponsored">';
                    //}


                    $html .= '<div class="row-header"><h4 class="bank-title"  style="background-color: ' . $bank->getColor() .'"><a href="#">' . $loan->getLoanTitle() . '</a></h4></div>';
                    $html .= '<div class="row-content">';
                    // Image
                    $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
                    if (!file_exists($dir_logo))
                    {
                        $dir_logo = 'data/image/no-image-64.png';
                    }
                    $html .= '<ul>';
                    $html .= '<li><a href="#"><img src="' . $basePath($dir_logo) .'" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></li>';
                    if($category->getName() === 'Fixed Deposit') {
                        $month_interes=$post['month_interes'];
                        if($month_interes==0)
                        {
                            $month_interes=$loan->getTenor();
                        }
                        $interest_rate = $this->string_to_number($loan->getIntRate());
                        // A = P x (1 + r/n)nt
                        // I = A - P
                        $is_month_correct=0;
                        if($loan->getInterestRate()) {
                            $interest_rates = \Zend\Json\Json::decode($loan->getInterestRate());
                            if(count($interest_rates) > 0) {
                                foreach ($interest_rates as $value) {
                                    if($value->tier == $month_interes) {
                                        $interest_rate = $value->percentage;
                                        $is_month_correct=1;
                                    }
                                }
                            }
                        }
                        // if($is_month_correct==0) $month_interes=$loan->getTenor();

                        // Formula:
                        // A = P x (1 + r/n)nt
                        // I = A - P
                        // First, converting R percent to r a decimal
                        // r = R/100 = 0.35%/100 = 0.0035 per year.
                        // $r = $interest_rate / 100;
                        // //Putting time into years for simplicity,
                        // //3 months / 12 months/year = 0.25 years.
                        // $t = $loan_tenure / 12;
                        // // Solving our equation
                        // $A = $loan_amount * (1 + ($r * $t));
                        // $I = round($A - $loan_amount, 2);

                        $loan_amount_interes=$post['loan_amount_interes'];
                        $I=($interest_rate*$loan_amount_interes/100)*($month_interes/12);
                        $html  .= '<li class="box__interest_earned"><span class="interest_earned" data-value="'.number_format($I,2).'"><b>$'.number_format($I,2).'</b>' . $translator->translate("Interest Earned") .'</span></li>';
                        $html .= '<li class="box__initial_deposit_amount"><span class="initial_deposit_amount" data-value="' .$loan_amount_interes .'"><b>' . number_format($loan_amount_interes) . '</b>' . $translator->translate("Fixed Deposit Amount") .'</span></li>';
                        $html .= '<li class="box__tenor"><span class="tenor" data-value="' . $month_interes .'"><b>' . $month_interes . '</b>' . $translator->translate("Tenor") .'</span></li>';
                        $html .= '<li class="box__interest_rates"><span class="interest_rates" data-value="' . $this->string_to_number($interest_rate) .'"><b>' . $interest_rate . '%</b>' . $translator->translate("Interest Rates") .'</span></li>';
                    } else {
                        $html .= '<li class="box__initial_deposit_amount"><span class="initial_deposit_amount" data-value="' . $this->string_to_number($loan->getInitialDepositAmount()) .'"><b>' . $loan->getInitialDepositAmount() . '</b>' . $translator->translate("Initial Deposit Amount") .'</span></li>';
                        $html .= '<li class="box__minimum_balance"><span class="minimum_balance" data-value="' . $this->string_to_number($loan->getMinimumBalance()) .'"><b>' . $loan->getMinimumBalance() . '</b>' . $translator->translate("Minimum Balance") .'</span></li>';
                        $html .= '<li class="box__interest_rates"><span class="interest_rates" data-value="' . $this->string_to_number($loan->getIntRate()) .'"><b>' . $loan->getIntRate() . '</b>' . $translator->translate("Interest Rates") .'</span></li>';
                        $html .= '<li><span class="cheque_book_fees"><b>' . $loan->getChequeBookFees() . '</b>' . $translator->translate("Cheque Book Fees") .'</span></li>';
                        $html .= '<li><span class="internet_banking_fees"><b>' . $loan->getInternetBankingFees() . '</b>' . $translator->translate("Internet Banking Fees") .'</span></li>';
                    }
                    $html .= '</ul>';
                    $html .= '</div>'; // End row-content

                    $html .= '<div class="row-footer">';
                    $html .= '<div class="col-md-12 summary-details">';

                    $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Interest Rates").'</div>';
                    $html .= '<div class="col-md-6">';
                    if($loan->getInterestRate()) {
                        $interest_rates = \Zend\Json\Json::decode($loan->getInterestRate());
                        if(count($interest_rates) > 0) {
                            $html .= '<ul>';
                            foreach ($interest_rates as $value) {
                                if($category->getName() === 'Fixed Deposit') {
                                    $value_tier=$value->tier;
                                    $value_percentage=$value->percentage;
                                    if($value_tier=='') $value_tier=$loan->getTenor();
                                    if($value_percentage=='') $value_percentage=$interest_rate;
                                    $html .= '<li><label>'.$value_tier.' months -> </label><span> '.$value_percentage.' %</span></li>';
                                } else {
                                    $html .= '<li><label>'.$value->tier.'</label><span>'.$value->percentage.'</span></li>';
                                }
                            }
                            $html .= '</ul>';
                        }
                    }
                    $html .= '</div></div>';

                    /*$html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Citizenship").'</div><div class="col-md-6">'.$loan->getCitizenship().'</div></div>';
                    $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Age").'</div><div class="col-md-6">'.$loan->getAge().'</div></div>';*/
                    if($category->getName() === 'Fixed Deposit') $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Minimum Deposit").'</div><div class="col-md-6">'.$loan->getMinimumBalance().'</div></div>';
                    //$html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Annual Fee").'</div><div class="col-md-6">'.$loan->getAnnualFee().'</div></div>';
                    //$html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Service Fee").'</div><div class="col-md-6">'.$loan->getServiceFee().'</div></div>';
                    $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Highlight").'</div><div class="col-md-6">'.$loan->getHighlight().'</div></div>';
                    $html .= '</div>'; // End summary-details

                    $html .= '<div class="col-md-12 more-detail">';
                    $html .= '<div class="col-md-8 col-sm-6 col-xs-12 button-gr button-gr-s">
                        <a href="javascript:void(0)" class="btn btn-more-detail">' . $translator->translate("Details") . '<i class="fa fa-angle-down"></i></a>
                        <a href="javascript:void(0)" class="btn btn-less-detail" style="display: none;">' .$translator->translate("Close") . '<i class="fa fa-angle-up"></i></a>';
                    $html .= '</div>';

                    $html .= '<div class="col-md-4 col-sm-6 col-xs-12 button-gr button-gr-s1">';
                    $html .= '<div class="col-md-6 col-sm-6 col-xs-12 box__compare"><button type="button" onclick="Loan.compare(this)" data-id="' .$loan->getId() . '" class="btn btn-lg btn-block ladda-button compare' . $class . '" title="' . $translator->translate("Compare") . '"><i class="fa fa-copy"></i><span>' . $translator->translate("Compare") . '</span></button></div>';

                    $html .= '<div class="col-md-6 col-sm-6 col-xs-12 box__apply"><button type="button" onclick="Loan.apply(this)" data-id="' .$loan->getId() . '" class="btn yellow-gold btn-lg btn-block ladda-button" data-style="slide-up" title="' .$translator->translate("Apply") . '"><i class="fa fa-check-square-o"></i> ' . $translator->translate("Apply Now") .'</button></div>';
                    $html .= '</div>';

                    $html .= '</div></div>'; // End row-footer

                    if($loan->getPromotions()) $html .= '<div class="promotion"><span>'.$loan->getPromotions().'</span></div>';
                    $html .= '</div>';
                }
                $html .= '</div>';
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("html" => $html)));
            return $response;
        }
        if($category->getName() === 'Fixed Deposit'){
            $view_model = new ViewModel(array("category" => $category, "faq" => $faq));
            $view_model->setTemplate('frontend/bank-account/fixed-deposit.phtml');
        }
        $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
        $faq = $application_model_faq->fetchRow(array("type" => "bank_account"));
        $application_model_bank_interest_rate = $this->getServiceLocator()->get('application_model_bank_interest_rate');
        $interest_rate = $application_model_bank_interest_rate->fetchAllSort(array("status" => 1,"display"=>$seo));
        $view_model = new ViewModel(array("category" => $category, "faq" => $faq,"interest_rate"=>$interest_rate,"current_category"=>$category,"seo"=>$seo));
        return $view_model;
    }

    public function successAction()
    {
        $session_user = new Session('user');
        $session_user->offsetUnset('redirect');
        $session = new Session('property_loan');
        $success = $session->offsetExists('success');
        if (empty($success)) return $this->redirect()->toRoute("loan_application", array("action" => "index"));
    }


    public function applyAction() {
      $session = new Session('property_loan');
      $request = $this->getRequest();
      $property_loan = array();

      if ($request->isPost()){
        $post = $request->getPost();
        $id = $post['id'];
        $loan_amount = $post['loan_amount'];
        $loan_tenure = $post['loan_tenure'];
        $loan_percent             = $post['loan_percent'];
        $no_lock_in_only          = $post['no_lock_in_only'];
        $application_model_property_loan_bank = $this->getServiceLocator()->get('application_model_property_loan_package');
        $loan = $application_model_property_loan_bank->fetchRow(array("id" => $id));
        $property_loan = array(
              'loan_id' => $loan-> getId(),
              'loan_title' => $loan -> getTitle(),
              'type' => "property_loan",
              'bank_id' => $loan -> getBankId(),
              'int_rates' => $loan -> getIntYear2(),
              'lock_in_year' => $loan -> getLockInYear(),
              'loan_amount' => $loan_amount

          );
        $session->offsetSet('loan', $property_loan);
        $redirect = "/loan-application/apply";
        $response = $this->getResponse();
        $response->setContent(\Zend\Json\Json::encode(array("success"=> true, "redirect" => $redirect)));
        return $response;
      }

      if ($session->offsetExists('loan'))
      {
        $loan = $session->offsetGet('loan');
        return array("loan" => $loan);
      }
      return array("loan" => $property_loan);
    }

    public function applyFormAction(){
      $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
      $session = new Session('property_loan');
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_view_helper_auth = $viewHelperManager->get('auth');
      $application_view_helper_setting = $viewHelperManager->get('setting');
      $user = $application_view_helper_auth();
      $user_id = $user ? $user->getId() : 0;
      $setting = $application_view_helper_setting();
      $seo = $this->params()->fromRoute('seo');
      $application_model_category = $this->getServiceLocator()->get('application_model_category');
      $category = $application_model_category->fetchRow(array("type" => "property_loan"));
      $request = $this->getRequest();
      if ($request->isPost())
      {
        $post = $request->getPost();
        $message = "";
        $phone = $post['phone'];
        if($phone) $message .= "<p>From phone: $phone</p>";
        $company = $post['company'];
        if($company) $message .= "<p>From phone: $company</p>";
        $message .= '<p>'.$post['message'].'</p>';

        // Send email
        $application_view_helper_send_email = $viewHelperManager->get('send_email');

        if($session->offsetExists('loan_amount')) {
          $loan_amount = $session->offsetGet('loan_amount');
        }

        if($session->offsetExists('loan_tenure')) {
          $loan_tenure = $session->offsetGet('loan_tenure');
        }

        if($session->offsetExists('loan_percent')) {
          $loan_percent = $session->offsetGet('loan_percent');
        } else{
          $loan_percent = 80;
        }

        $success = false;

        if ($session->offsetExists('loan'))
        {
          $loan = $session->offsetGet('loan');
          $translator = $this->getServiceLocator()->get('translator');
          $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
          $property_loan = new \Application\Entity\PropertyLoan;
          $property_loan->setUserId($user_id);
          $property_loan->setType('property_loan');
          $property_loan->setCategoryId($category->getId());
          $property_loan->setPropertyType($session->offsetGet('property_type'));
          $property_loan->setProjectName($session->offsetGet('project_name'));
          $property_loan->setPropertyStatus($session->offsetGet('property_status'));
          $property_loan->setOptionFee($session->offsetGet('option_fee'));
          $property_loan->setOfferOpts($session->offsetGet('offer_opts'));
          $property_loan->setExisting($session->offsetGet('existing_home_loans'));
          $property_loan->setExisting($session->offsetGet('existing_home_loans'));
          $property_loan->setLoanAmount($session->offsetGet('loan_amount'));
          $property_loan->setLoanTenure($session->offsetGet('loan_tenure'));
          $property_loan->setLoanPercent($loan_percent);
          $property_loan->setFixedRates($session->offsetGet('preferred_rate_package'));
          $property_loan->setRemark($message);
          $property_loan->setDateAdded(new Expression('NOW()'));
          $property_loan->setStatus(1);
          $added = $application_model_property_loan->insert($property_loan);

          if ($added)
          {
            $id = $added->getGeneratedValue();
            $property_loan = $application_model_property_loan->fetchRow(array("id" => $id));

            // Send email to Admin
            $html = '<p>Dear Admin,</p>';
            $html .= '<p>You have one loan application ('.$category->getName().')</p>';
            $html .= '<p>Please check your admin for more information.</p>';
            $html .= '<p>'.$message.'</p>';
            $application_view_helper_send_email("You have a ".$category->getName()." application form", $html, 'Best regard');

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
                  $referral->setType('property_loan');
                  $referral->setApplication($application);
                  $referral->setCredit($setting->amt_business_loan);
                  $referral->setDateAdded(new Expression('NOW()'));
                  $referral->setStatus(0);
                  $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
                  $application_model_referral->insert($referral);
                }
              }
            }

            // Unset session property
            $session->offsetSet('success', 1);
            $session->offsetUnset('property_loan');

            $success = true;
            $redirect = $router->assemble(array("action" => "success"), array('name' => "loan_application"));
          }
        } else {
          $redirect = $router->assemble(array("action" => "apply"), array('name' => 'loan_application'));
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
        $session_user->offsetSet('redirect', $router->assemble(array("action" => "apply-form"), array('name' => 'loan_application')));
        return $this->redirect()->toRoute("frontend_user", array("action" => "auth"));
      }
    }

    // public function bankAccountFilterAction()
    // {
    //     $session = new Session('bank_account');
    //     $session->offsetUnset('success');
    //     $request = $this->getRequest();

    //     $seo = $this->params()->fromRoute('seo');
    //     $application_model_category = $this->getServiceLocator()->get('application_model_category');
    //     $category = $application_model_category->fetchRow(array("seo" => $seo, "type" => "bank_account"));

    //     if ($request->isPost())
    //     {
    //         $post = $request->getPost();
    //         $messages = array();
    //         $translator = $this->getServiceLocator()->get('translator');
    //         $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
    //         $application_model_bank_account_package = $this->getServiceLocator()->get('application_model_bank_account_package');

    //         $category_id = $post['category_id'];
    //         $loan_amount = $post['loan_amount'];
    //         $loan_tenure = $post['loan_tenure'];
    //         $month_interes=$post['month_interes'];

    //         $category = $application_model_category->fetchRow(array("id" => $category_id));
    //         $session->offsetSet('category_id', $category_id);

    //         $loans = $application_model_bank_account_package->fetchAll(array("status" => 1, "category_id" => $category_id));
    //         $html = '';
    //         if (count($loans) > 0)
    //         {
    //             $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    //             $html .= '<div class="filters-table-body">';
    //             foreach ($loans as $k => $loan) {
    //                 $class = "";
    //                 if ($session->offsetExists('compare'))
    //                 {
    //                     $compare_arr = $session->offsetGet('compare');
    //                     if (count($compare_arr) > 0)
    //                     {
    //                         if (in_array($loan->getId(), $compare_arr))
    //                         {
    //                             $class = " active";
    //                         }
    //                     }
    //                 }

    //                 $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
    //                 //if ($k == 0) {
    //                     //$html .= '<div class="filters-content sponsored">';
    //                 //} else {
    //                 if($loan->getCategoryAccount()==$month_interes)
    //                 {
    //                     $html .= '<div class="filters-content not-sponsored">';
    //                     //}
    //                     $html .= '<div class="row-header"><h4 class="bank-title"  style="background-color: ' . $bank->getColor() .'"><a href="#">' . $loan->getLoanTitle() . '</a></h4></div>';
    //                     $html .= '<div class="row-content">';
    //                     // Image
    //                     $dir_logo = 'data/bank/' . $loan->getBankId() . '/m_' . $bank->getLogo();
    //                     if (!file_exists($dir_logo))
    //                     {
    //                         $dir_logo = 'data/image/no-image-64.png';
    //                     }
    //                     $html .= '<ul>';
    //                     $html .= '<li><a href="#"><img src="' . $basePath($dir_logo) .'" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></li>';
    //                     if($category->getName() === 'Fixed Deposit') {
    //                         $interest_rate = $this->string_to_number($loan->getIntRate());
    //                         // A = P x (1 + r/n)nt
    //                         // I = A - P
    //                         if($loan->getInterestRate()) {
    //                             $interest_rates = \Zend\Json\Json::decode($loan->getInterestRate());
    //                             if(count($interest_rates) > 0) {
    //                                 foreach ($interest_rates as $value) {
    //                                     if($value->tier == $this->string_to_number($loan_tenure)) {
    //                                         $interest_rate = $value->percentage;
    //                                     }
    //                                 }
    //                             }
    //                         }
    //                         // Formula:
    //                         // A = P x (1 + r/n)nt
    //                         // I = A - P
    //                         // First, converting R percent to r a decimal
    //                         // r = R/100 = 0.35%/100 = 0.0035 per year.
    //                         // $r = $interest_rate / 100;
    //                         // //Putting time into years for simplicity,
    //                         // //3 months / 12 months/year = 0.25 years.
    //                         // $t = $loan_tenure / 12;
    //                         // // Solving our equation
    //                         // $A = $loan_amount * (1 + ($r * $t));
    //                         // $I = round($A - $loan_amount, 2);
    //                         $loan_amount_interes=$post['loan_amount_interes'];
    //                         $I=($interest_rate*$loan_amount_interes)/100;

    //                         $html .= '<li class="box__initial_deposit_amount"><span class="initial_deposit_amount" data-value="' . $this->string_to_number($loan->getInitialDepositAmount()) .'"><b>' . $loan->getInitialDepositAmount() . '</b>' . $translator->translate("Fixed Deposit Amount") .'</span></li>';
    //                         $html .= '<li class="box__tenor"><span class="tenor" data-value="' . $this->string_to_number($loan->getCategoryAccount()) .'"><b>' . $loan->getCategoryAccount() . '</b>' . $translator->translate("Tenor") .'</span></li>';
    //                         $html .= '<li class="box__interest_rates"><span class="interest_rates" data-value="' . $this->string_to_number($interest_rate) .'"><b>' . $interest_rate . '%</b>' . $translator->translate("Interest Rates") .'</span></li>';
    //                         $html .= '<li class="box__interest_earned"><span class="interest_earned" data-value="'.$I.'"><b>'.$I.'$</b>' . $translator->translate("Interest Earned") .'</span></li>';
    //                     }
    //                     $html .= '</ul>';
    //                     $html .= '</div>'; // End row-content

    //                     $html .= '<div class="row-footer">';
    //                     $html .= '<div class="col-md-12 summary-details">';

    //                     $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Interest Rates").'</div>';
    //                     $html .= '<div class="col-md-6">';
    //                     if($loan->getInterestRate()) {
    //                         $interest_rates = \Zend\Json\Json::decode($loan->getInterestRate());
    //                         if(count($interest_rates) > 0) {
    //                             $html .= '<ul>';
    //                             foreach ($interest_rates as $value) {
    //                                 if($category->getName() === 'Fixed Deposit') {
    //                                     $html .= '<li><label>'.$value->tier.'</label><span>'.$value->percentage.' %</span></li>';
    //                                 } else {
    //                                     $html .= '<li><label>'.$value->tier.'</label><span>'.$value->percentage.'</span></li>';
    //                                 }
    //                             }
    //                             $html .= '</ul>';
    //                         }
    //                     }
    //                     $html .= '</div></div>';

    //                     /*$html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Citizenship").'</div><div class="col-md-6">'.$loan->getCitizenship().'</div></div>';
    //                     $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Age").'</div><div class="col-md-6">'.$loan->getAge().'</div></div>';*/
    //                     if($category->getName() === 'Fixed Deposit') $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Minimum Deposit").'</div><div class="col-md-6">'.$loan->getMinimumBalance().'</div></div>';
    //                     $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Annual Fee").'</div><div class="col-md-6">'.$loan->getAnnualFee().'</div></div>';
    //                     $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Service Fee").'</div><div class="col-md-6">'.$loan->getServiceFee().'</div></div>';
    //                     $html .= '<div class="row"><div class="col-md-6">'.$translator->translate("Highlight").'</div><div class="col-md-6">'.$loan->getHighlight().'</div></div>';

    //                     $html .= '</div>'; // End summary-details

    //                     $html .= '<div class="col-md-12 more-detail">';
    //                     $html .= '<div class="col-md-8 col-sm-6 col-xs-12 button-gr button-gr-s">
    //                         <a href="javascript:void(0)" class="btn btn-more-detail">' . $translator->translate("Details") . '<i class="fa fa-angle-down"></i></a>
    //                         <a href="javascript:void(0)" class="btn btn-less-detail" style="display: none;">' .$translator->translate("Close") . '<i class="fa fa-angle-up"></i></a>';
    //                     $html .= '</div>';

    //                     $html .= '<div class="col-md-4 col-sm-6 col-xs-12 button-gr button-gr-s1">';
    //                     $html .= '<div class="col-md-6 col-sm-6 col-xs-12 box__compare"><button type="button" onclick="Loan.compare(this)" data-id="' .$loan->getId() . '" class="btn btn-lg btn-block ladda-button compare' . $class . '" title="' . $translator->translate("Compare") . '"><i class="fa fa-copy"></i><span>' . $translator->translate("Compare") . '</span></button></div>';
    //                     $html .= '<div class="col-md-6 col-sm-6 col-xs-12 box__apply"><button type="button" onclick="Loan.apply(this)" data-id="' .$loan->getId() . '" class="btn yellow-gold btn-lg btn-block ladda-button" data-style="slide-up" title="' .$translator->translate("Apply") . '"><i class="fa fa-check-square-o"></i> ' . $translator->translate("Apply Now") .'</button></div>';
    //                     $html .= '</div>';

    //                     $html .= '</div></div>'; // End row-footer

    //                     if($loan->getPromotions()) $html .= '<div class="promotion"><span>'.$loan->getPromotions().'</span></div>';
    //                     $html .= '</div>';
    //                 }
    //             $html .= '</div>';
    //             }
    //     }
    //         $response = $this->getResponse();
    //         $response->setContent(\Zend\Json\Json::encode(array("html" => $html)));
    //         return $response;
    //     }
    //     if($category->getName() === 'Fixed Deposit'){
    //         $view_model = new ViewModel(array("category" => $category, "faq" => $faq));
    //         $view_model->setTemplate('frontend/bank-account/fixed-deposit.phtml');
    //     }
    //     $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
    //     $faq = $application_model_faq->fetchRow(array("type" => "bank_account"));
    //     $application_model_bank_interest_rate = $this->getServiceLocator()->get('application_model_bank_interest_rate');
    //     $interest_rate = $application_model_bank_interest_rate->fetchAllSort(array("status" => 1,"display"=>$seo));
    //     $view_model = new ViewModel(array("category" => $category, "faq" => $faq,"interest_rate"=>$interest_rate,"current_category"=>$category,"seo"=>$seo));
    //     return $view_model;
    // }

    private function string_to_number($string) {
        if($string) {
            $number = preg_replace("/[^0-9\.]{1,4}/", '', $string); // return 1234
        } else {
            $number = 0;
        }
        return (float) $number;
    }
}
