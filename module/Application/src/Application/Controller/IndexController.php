<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

class IndexController extends AbstractActionController
{
    public function indexAction()
    {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
            
            $loans = $application_model_business_loan_package->fetchAll(array("status" => 1));
            $html = "";
            if(count($loans) > 0) {
                $loan_amount = $post['loan_amount'];
                $loan_tenure = $post['loan_tenure'];
                $int_rates   = '';
                
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                foreach ($loans as $loan) {
                    $bank = $application_model_bank->fetchRow(array("id" => $loan->getBankId()));
                    $html .= '<div class="filters-content">';
                    $html .= '<div class="row row-header"><div class="col-md-12">
                            <h4 class="bank-title"><a href="#">'.$loan->getLoanTitle().'</a></h4><p style="text-align: left;">'.$loan->getPromotions().'</p></div></div>';
                    $html .= '<div class="row row-content">';
                    
                    // Image
                    $dir_logo = 'data/bank/'.$loan->getBankId().'/m_'.$bank->getLogo();
                    if(!file_exists($dir_logo)) {
                        $dir_logo = 'data/image/no-image-64.png';
                    } 
                    $html .= '<div class="col-md-2"><a href="#"><img src="'.$dir_logo.'" alt="'.$loan->getLoanTitle().'" class="logo" /></a></div>';
                    
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
                    
                    $html .= '<div class="col-md-10"><div class="row">';
                    $html .= '<div class="col-md-3 box__rate"><span class="rate"><b>'.$int_rates.'%</b>'.$translator->translate("Interest Rate").'</span></div>';
                    $html .= '<div class="col-md-3 box__loan"><span class="loan"><b>'.$loan_tenure.'</b>'.$translator->translate("Loan Tenure").'</span></div>';
                    $html .= '<div class="col-md-3 box__month"><span class="month"><b>$'.number_format($monthly_payment, 2).'</b>'.$translator->translate("Per Month").'</span></div>';
                    $html .= '<div class="col-md-3 box__apply"><a href="javascript:;" class="btn red-thunderbird btn-block">'.$translator->translate("Apply Now").' <i class="fa fa-angle-right"></i></a></div>';
                    $html .= '</div></div>';
                    $html .= '</div>';
                    
                    $html .= '<div class="row row-footer">';
                    $html .= '<div class="col-md-12 summary-details">';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("INT RATES").'</strong></div>
                            <div class="col-md-8 line-top">'.$int_rates.'%</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Max Tenor (Years)").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getMaxTenor().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Principle Loan Amount").'</strong></div>
                            <div class="col-md-8 line-top">'.number_format($loan_amount).'</div>
                        </div>
                    </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Max Loan Amount").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getMaxLoanAmount().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Processing Fee").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getProcessingFee().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Monthly Instalment").'</strong></div>
                            <div class="col-md-8 line-top">$'.number_format($monthly_payment, 2).'</div>
                        </div>
                    </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Annual Fee").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getAnnualFee().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Penalty Fee").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getPenaltyFee().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Interest Rate (per annum)").'</strong></div>
                            <div class="col-md-8 line-top">'.$int_rates.'%</div>
                        </div>
                    </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Lock In Period").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getLockInPeriod().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Minimum Sales Turnover").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getMinSalesTurnover().'</div>
                        </div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Loan Tenure").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan_tenure.' years</div>
                        </div>
                    </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Minimum Years of Incorporation").'</strong></div>
                            <div class="col-md-8 line-top">'.$loan->getMinYearsOfIncorporation().'</div>
                        </div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Total Amount Payable").'</strong></div>
                            <div class="col-md-8 line-top">$'.number_format($total_amount_payable, 2).'</div>
                        </div>
                    </div>';
                    
                    $html .= '<div class="row">
                        <div class="col-md-4"></div>
                        <div class="col-md-4"></div>
                        <div class="col-md-4">
                            <div class="col-md-4 line-top"><strong>'.$translator->translate("Total Interest Payable").'</strong></div>
                            <div class="col-md-8 line-top">$'.number_format($total_interest_payable, 2).'</div>
                        </div>
                    </div>';
                    
                    $html .= '</div>';
                    $html .= '<div class="col-md-12 more-detail">
                                <a href="javascript:void(0)" class="btn btn-more-detail">'.$translator->translate("More Details").'<i class="fa fa-angle-down"></i></a>
                                <a href="javascript:void(0)" class="btn btn-less-detail" style="display: none;">'.$translator->translate("Less Details").'<i class="fa fa-angle-up"></i></a>
                            </div>';
                    $html .= '</div>';
                    
                    $html .= '</div>';
                }
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( array("html" => $html) ) );
            return $response;
        }
    }
}
