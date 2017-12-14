<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class BankController extends AbstractActionController
{
    public function indexAction() {
        $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
        $banks = $application_model_bank->fetchAll();
        return array("banks" => $banks);
    }

    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');

            $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
            $bank = new \Application\Entity\Bank;
            $bank->setName($post['name']);
            $bank->setDateAdded(new Expression('NOW()'));
            $bank->setLogo($post['logo']);
            $bank->setColor($post['color']);
            $bank->setStatus($post['status']);
            $added = $application_model_bank->insert($bank);
            if($added) {
                $messages['success'] = true;
                $messages['msg'] = $translator->translate("Successfully added");

                // Logo
                $dir_bank = 'data/bank/';
                if($post['logo']) {
                    $dir_logo = $dir_bank.$added->getGeneratedValue();
                    if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

                    $dir_tmp = $dir_bank.'/tmp/'.$post['logo'];
                    $dir_new = $dir_logo.'/'.$post['logo'];
                    if(file_exists($dir_tmp)) copy($dir_tmp, $dir_new);

                    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
                    $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                    $application_view_helper_folder = $viewHelperManager->get('folder');

                    $application_view_helper_resizeimage($dir_logo, $post['logo']);
                    $application_view_helper_folder("delete", $dir_bank.'/tmp');
                }
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
        $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
        $bank = $application_model_bank->fetchRow(array('id' => $id));
        if($bank) {
            $translator = $this->getServiceLocator()->get('translator');

            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();

                $error = 0;
                if(!$error) {
                    $bank->setId($id);
                    $bank->setName($post['name']);
                    $bank->setDateModified(new Expression('NOW()'));
                    $bank->setStatus($post['status']);

                    // Logo
                    $dir_bank = 'data/bank/';
                    if($post['logo'] !== $bank->getLogo()) {
                        $dir_logo = $dir_bank.$id;
                        if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

                        $dir_tmp = $dir_bank.'/tmp/'.$post['logo'];
                        $dir_new = $dir_logo.'/'.$post['logo'];
                        if(file_exists($dir_tmp)) {
                            copy($dir_tmp, $dir_new);

                            $ext = pathinfo($post['logo'], PATHINFO_EXTENSION);
                            $new_logo_name = $id.'.'.$ext;
                            rename($dir_new, $dir_logo.'/'.$new_logo_name);

                            $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
                            $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                            $application_view_helper_folder = $viewHelperManager->get('folder');

                            $application_view_helper_resizeimage($dir_logo, $new_logo_name);
                            $application_view_helper_folder("delete", $dir_bank.'/tmp');

                            $bank->setLogo($new_logo_name);
                        }
                    }
                    $bank->setColor($post['color']);

                    $edited = $application_model_bank->update($bank);
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

            return array('bank' => $bank);
        } else {
            return $this->redirect()->toRoute("admin/bank");
        }
    }

    public function setStatusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_bank = $this->getServiceLocator()->get('application_model_bank');

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
                $user = $application_model_bank->fetchRow(array('id' => $id));
                $user->setId($id);
                $user->setStatus($status);
                $user->setDateModified(new Expression('NOW()'));

                $updated = $application_model_bank->update($user);
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


    /**
     * Property Loan page
     */
    public function viewPropertyLoanAction() {
        $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_property_loan_bank');
        $loans = $application_model_business_loan_package->fetchAll();
        return array("loans" => $loans);
    }

    public function addPropertyLoanAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');

            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_property_loan_bank');
            $loan = new \Application\Entity\PropertyLoanPackage;
            $loan->setBankId($post['bank_id']);
            $loan->setType($post['type']);;
            $loan->setLoanTitle($post['loan_title']);
            $loan->setPromotions($this->clearHtml($post['promotions']));
            $loan->setBenefit($post['benefit']);
            $loan->setMaxLoanAmount($post['max_loan_amount']);
            $loan->setMaxTenor($post['max_tenor']);
            $loan->setLockInPeriod($post['lock_in_period']);
            $loan->setMinSalesTurnover($post['min_sales_turnover']);
            $loan->setMinYearsOfIncorporation($post['min_years_of_incorporation']);
            $loan->setDateAdded(new Expression('NOW()'));
            $loan->setStatus($post['status']);
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

    public function editPropertyLoanAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_property_loan_bank');
        $loan = $application_model_business_loan_package->fetchRow(array('id' => $id));
        if($loan) {
            $translator = $this->getServiceLocator()->get('translator');

            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();

                $error = 0;
                if(!$error) {
                    $loan->setId($id);
                    $loan->setBankId($post['bank_id']);
                    $loan->setType($post['type']);
                    $loan->setLoanTitle($post['loan_title']);
                    $loan->setPromotions($this->clearHtml($post['promotions']));
                    $loan->setBenefit($post['benefit']);
                    $loan->setMaxLoanAmount($post['max_loan_amount']);
                    $loan->setMaxTenor($post['max_tenor']);
                    $loan->setLockInPeriod($post['lock_in_period']);
                    $loan->setMinSalesTurnover($post['min_sales_turnover']);
                    $loan->setMinYearsOfIncorporation($post['min_years_of_incorporation']);
                    $loan->setDateModified(new Expression('NOW()'));
                    $loan->setStatus($post['status']);

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
            return $this->redirect()->toRoute("admin/view-property-loan");
        }
    }

    public function statusPropertyLoanAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_property_loan_bank');

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

    /**
     * Interest Rate page
     */
    public function viewInterestRateAction() {
        $id = $this->params()->fromRoute('id');  // From RouteMatch
        switch ($id) {
            case 1:
                $model = "application_model_business_loan_package";
            break;

            case 2:
                $model = "application_model_property_loan_bank";
            break;

            default:
                $id = 1;
                $model = "application_model_business_loan_package";
            break;
        }
        $application_model_business_loan_package = $this->getServiceLocator()->get($model);
        $loans = $application_model_business_loan_package->fetchAll(array("status" => 1));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
            $application_view_helper_setting = $viewHelperManager->get('setting');
            $setting = $application_view_helper_setting();

            $loan_id = $post['id'];
            $type = $post['type'];
            switch ($type) {
                case 1:
                    $model = "application_model_business_loan_package";
                break;

                case 2:
                    $model = "application_model_property_loan_bank";
                break;

                default:
                    $model = "application_model_business_loan_package";
                break;
            }
            $application_model_business_loan_package = $this->getServiceLocator()->get($model);
            $bank = $application_model_business_loan_package->fetchRow(array("id" => $loan_id));
            $max_loan_tenure = $setting->max_loan_tenure;

            $success = false;
            $html = "";
            if($bank) {
                $interest_rate = \Zend\Json\Json::decode($bank->getInterestRate());
                if(count($interest_rate) > 0) {
                    $html .= '<table class="table table-bordered table-striped table-condensed flip-content">';
                    $html .= '<thead class="flip-content">';
                    $html .= '<th>'.$translator->translate("Condition").'</th>';
                    for($i=1; $i<=$max_loan_tenure; $i++) {
                        $html .= '<th>'.$i.' '.$translator->translate("Year").'</th>';
                    }
                    if($type == 2) $html .= '<th>'.$translator->translate("Thereafter").'</th>';
                    $html .= '</thead><tbody>';
                    foreach ($interest_rate as $key => $value) {
                        $year = $value->year;
                        $html .= '<tr>';
                        $html .= '<td>'.$value->condition.'</td>';
                        for($i=1; $i<=$max_loan_tenure; $i++) {
                            $html .= '<td>'.$year->{$i}.'%</td>';
                        }
                        if($type == 2) $html .= '<td>'.$year->thereafter.'%</td>';
                        $html .= '</tr>';
                    }
                    $html .= '</tbody></table>';
                }
                $messages['success'] = true;
            } else {
                $messages['success'] = false;
            }
            $messages['html'] = $html;

            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("id" => $id, "loans" => $loans);
    }

    public function updateInterestRateAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_setting = $viewHelperManager->get('setting');
        $setting = $application_view_helper_setting();

        $request = $this->getRequest();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();

            $type = $post['type'];
            if($type == 2) {
                $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_property_loan_bank');
                $max_loan_tenure = 4;
            } else {
                $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
                $max_loan_tenure = $setting->max_loan_tenure;
            }
            $loan = $application_model_business_loan_package->fetchRow(array("id" => $post['id']));

            $error = 0;
            if(!$error) {
                $loan->setId($post['id']);
                $interest_rate = $post['interest-rate'];
                if(count($interest_rate) > 0) {
                        $loan_tenure = array();
                        $year = array();
                        foreach ($interest_rate as $key => $value) {
                            $condition = $value['condition'];
                            $percentage = $value['percentage'];
                            /*for($i=1; $i<=$max_loan_tenure; $i++) {
                                $year[$i] = $value[$i];
                            }
                            if($value['thereafter']) $year['thereafter'] = $value['thereafter'];
                            $loan_tenure[] = array(
                                'condition' => $condition,
                                'year'      => $year
                            );*/
                            $loan_tenure[] = array(
                                'condition'  => $condition,
                                'percentage' => $percentage
                            );
                        }
                        $loan->setInterestRate(\Zend\Json\Json::encode($loan_tenure));
                    }
                    $loan->setDateModified(new Expression('NOW()'));

                    $edited = $application_model_business_loan_package->update($loan);
                    if($edited) {
                        $messages['success'] = true;
                        $messages['msg']     = $translator->translate("Successfully updated");
                    } else {
                        $messages['success'] = false;
                        $messages['msg']     = $translator->translate("Something error. Please check");
                    }
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }

    public function updateInterestRateBusinessLoanAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_business_loan_package');
        $bank = $application_model_business_loan_package->fetchRow(array('id' => $id));
        return array("type" => 1, "bank" => $bank);
    }

    public function updateInterestRatePropertyLoanAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_business_loan_package = $this->getServiceLocator()->get('application_model_property_loan_bank');
        $bank = $application_model_business_loan_package->fetchRow(array('id' => $id));
        return array("type" => 2, "bank" => $bank);
    }

    /**
     * Referrals
     */
    public function viewReferralSummaryAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
        $referrals = $application_model_referral->fetchAll();
        return array("referrals" => $referrals);
    }

    public function setStatusReferralAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_referral = $this->getServiceLocator()->get('application_model_referral');

            $action = $this->params()->fromPost('action');
            switch ($action) {
                case 'paid':
                    $status = 4;
                break;
            }
            $ids = $this->params()->fromPost('ids');
            $error = 0;
            $result = array();
            foreach ($ids as $id) {
                $referral = $application_model_referral->fetchRow(array('id' => $id));
                $referral->setId($id);
                $referral->setStatus($status);

                $updated = $application_model_referral->update($referral);
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

    public function editReferralAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
        $referral = $application_model_referral->fetchRow(array('id' => $id));
        if($referral) {
            $translator = $this->getServiceLocator()->get('translator');

            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                $error = 0;
                if($referral->getStatus() == 4) {
                    $messages['success'] = false;
                    $messages['msg']     = $translator->translate("You can't paid multiple for this loan");
                    $error = $error + 1;
                }

                if(!$error) {
                    $referral->setId($id);
                    $referral->setCredit($post['credit']);
                    $referral->setDateModified(new Expression('NOW()'));
                    $referral->setStatus(4);
                    $edited = $application_model_referral->update($referral);
                    if($edited) {
                        // Update User Credit
                        $application_model_user_credit = $this->getServiceLocator()->get('application_model_user_credit');
                        $user_credit = new \Application\Entity\UserCredit;
                        $user_credit->setUserId($referral->getUserId());
                        $user_credit->setCredit($post['credit']);
                        $user_credit->setRefId($id);
                        $user_credit->setDateAdded(new Expression('NOW()'));
                        $application_model_user_credit->insert($user_credit);

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
            return array('referral' => $referral);
        } else {
            return $this->redirect()->toRoute("admin/bank", array("action" => "view-referral-summary"));
        }
    }

    public function changeLogoAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $file = $this->params()->fromFiles('file');
            $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp");
            $name = $file['name'];
			if(strlen($name)) {
                $dir = 'data/bank/tmp';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }

                list($txt, $ext) = explode(".", $name);
                if(in_array($ext, $valid_formats)) {
                    $newFilename = time(). '.' . $ext;
                    $tmp = $file['tmp_name'];
                    if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
                        $messages = array(
                            'success'  => true,
                            'name'     => $newFilename,
                            'src'      => '/data/bank/tmp/'.$newFilename,
                            'msg'      => $translator->translate("Upload logo successful"),
                        );
                    } else {
                        $messages = array('success' => false, 'msg' => $translator->translate("Something error. Please check"));
                    }
                } else {
                    $messages = array('success' => false, 'msg' => $translator->translate("Invalid file formats"));
                }
            } else {
                $messages = array('success' => false, 'msg' => $translator->translate("Please select photo"));
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