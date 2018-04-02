<?php
namespace Frontend\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class LoanController extends AbstractActionController
{
    public function businessLoanAction()
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
                    $html .= '<div class="col-md-2"><a href="#"><img src="' . $basePath($dir_logo) .
                        '" alt="' . $loan->getLoanTitle() . '" class="logo" /></a></div>';
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
                            $condition = str_replace(',','', $condition);
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


}
