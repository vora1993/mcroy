<?php
namespace Frontend\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class BankAccountController extends AbstractActionController
{
    public function loadCompareAction()
    {
        $basePath = $this->getServiceLocator()->get('ViewHelperManager')->get('basePath');
        $session = new Session('bank_account');
        $count = 0;
        $html = '<div class="row">';
        if ($session->offsetExists('compare'))
        {
            $compare = $session->offsetGet('compare');
            if (count($compare) > 0)
            {
                $application_model_bank_account_package = $this->getServiceLocator()->get('application_model_bank_account_package');
                $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
                foreach ($compare as $key => $loan_id)
                {
                    $loan = $application_model_bank_account_package->fetchRow(array("id" => $loan_id));
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
    
    public function compareAction()
    {
        $session = new Session('bank_account');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $id = $post['id'];
            
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
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array(
                "success" => $success,
                "msg" => $msg,
                "cr" => $cr,
                "ca" => $ca))
            );
            return $response;
        }
        if ($session->offsetExists('compare')) $compare = $session->offsetGet('compare');
        
        $category_id = $session->offsetGet('category_id');
        $application_model_category = $this->getServiceLocator()->get('application_model_category');
        $category = $application_model_category->fetchRow(array("id" => $category_id));
        
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);
        $viewModel->setVariables(array("compare" => $compare, "category" => $category));
        return $viewModel;
    }
    
    public function clearCompareAction()
    {
        $session = new Session('bank_account');
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
    
    public function applyAction()
    {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session = new Session('bank_account');
        $request = $this->getRequest();
        if ($request->isPost())
        {
            $post = $request->getPost();
            $id = $post['id'];
            $category_id = $post["category_id"];
            
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_bank_account_package = $this->getServiceLocator()->get('application_model_bank_account_package');
            $loan = $application_model_bank_account_package->fetchRow(array("id" => $id));
            
            $bank_account = array(
                'loan' => $loan,
                'type' => "bank_account",
                "category_id" => $category_id
            );
            $session->offsetSet('loan', $bank_account);
            
            $redirect = $router->assemble(array("action" => "apply"), array('name' =>'bank_account'));
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("success" => true, "redirect" => $redirect)));
            return $response;
        }
        
        if ($this->getServiceLocator()->get('AuthService')->hasIdentity())
        {
            if ($session->offsetExists('loan'))
            {
                $loan = $session->offsetGet('loan');
                return array("loan" => $loan);
            }
            return $this->redirect()->toRoute("home");
        } else {
            $session_user = new Session('user');
            $session_user->offsetSet('redirect', $router->assemble(array("action" => "apply"), array('name' => 'bank_account')));
            return $this->redirect()->toRoute("frontend_user", array("action" => "auth"));
        }
    }
    
    public function applyFormAction()
    {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session = new Session('bank_account');
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
                $name = $user->getDisplayName();
                $email = $user->getEmail();
                
                $loan = $session->offsetGet('loan');
                $translator = $this->getServiceLocator()->get('translator');
                $application_model_bank_account = $this->getServiceLocator()->get('application_model_bank_account');
                $bank_account = new \Application\Entity\BankAccount;
                $bank_account->setUserId($post['user_id']);
                $bank_account->setLoanId($loan['loan_id']);
                $bank_account->setCategoryId($loan['category_id']);
                $bank_account->setType($post['type']);
                $bank_account->setName($name);
                $bank_account->setEmail($user->getEmail());
                $bank_account->setPhone($post['phone']);
                $bank_account->setCompanyName($post['company']);
                $bank_account->setRemark($post['message']);
                $bank_account->setDateAdded(new Expression('NOW()'));
                $bank_account->setStatus($post['status']);
                $added = $application_model_bank_account->insert($bank_account);
                if ($added)
                {
                    $success = true;
                    $redirect = $router->assemble(array("action" => "success"), array('name' => 'bank_account'));
                    
                    $id = $added->getGeneratedValue();
                    $bank_account = $application_model_bank_account->fetchRow(array("id" => $id));
                    if($bank_account) {
                        // Send email
                        $application_view_helper_send_email = $viewHelperManager->get('send_email');
                        $subject = $translator->translate("You have a application form from bank account");
                        $text = $translator->translate("Best regard");
                        
                        $html = '<table rules="all" style="border: 1px solid #f9f9f9;" cellpadding="8" cellspacing="8">';
                        $html .= '<tr><th colspan="2" align="center"><strong>'.$translator->translate("Information").'</strong></th></tr>';
                        if($name) $html .= '<tr><td><strong>'.$translator->translate("Name").'</strong></td><td>'.$name.'</td></tr>';
                        if($email) $html .= '<tr><td><strong>'.$translator->translate("Email").'</strong></td><td>'.$email.'</td></tr>';
                        if($post['phone']) $html .= '<tr><td><strong>'.$translator->translate("Phone").'</strong></td><td>'.$post['phone'].'</td></tr>';
                        if($post['company']) $html .= '<tr><td><strong>'.$translator->translate("Company Name").'</strong></td><td>'.$post['company'].'</td></tr>';
                        $html .= '<tr><th colspan="2" align="center"><strong>'.$translator->translate("Package").'</strong></th></tr>';
                        if($post['type']) $html .= '<tr><td><strong>'.$translator->translate("Type").'</strong></td><td>'.$post['type'].'</td></tr>';
                        if($loan['loan_id']) {
                            $application_model_bank_account_package = $this->getServiceLocator()->get('application_model_bank_account_package');
                            $bank_account_package = $application_model_bank_account_package->fetchRow(array("id" => $loan['loan_id']));
                            if($bank_account_package) {
                                $html .= '<tr><td><strong>'.$translator->translate("Loan").'</strong></td><td>'.$bank_account_package->getLoanTitle().'</td></tr>';
                            }
                        }
                        $html .= '<tr><th colspan="2" align="center"><strong>'.$translator->translate("Status").'</strong></th></tr>';
                        $html .= '<tr><td><strong>'.$translator->translate("Creation Date").'</strong></td><td>'.date("d-m-Y H:i A", strtotime($bank_account->getDateAdded())).'</td></tr>';
                        
                        $application_view_helper_status = $viewHelperManager->get('status');
                        $html .= '<tr><td><strong>'.$translator->translate("Status").'</strong></td><td>'.$application_view_helper_status($bank_account).'</td></tr>';
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
                                $referral->setType('bank_account');
                                $referral->setApplication($application);
                                $referral->setCredit($setting->amt_bank_account);
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
                $redirect = $router->assemble(array("action" => "home"));
            }
            $response = $this->getResponse();
            $response->setContent(\Zend\Json\Json::encode(array("success" => $success, "redirect" => $redirect)));
            return $response;
        }
        
    }
    
    public function successAction()
    {
        $session_user = new Session('user');
        $session_user->offsetUnset('redirect');
        
        $session = new Session('bank_account');
        $success = $session->offsetExists('success');
        if (empty($success)) return $this->redirect()->toRoute("home");
    }
    
}
