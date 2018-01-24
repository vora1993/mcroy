<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;
use Zend\Db\Sql\Expression;

class PageController extends AbstractActionController
{
  public function errorAction() {
    $id = $this->params()->fromRoute('id');  // From RouteMatch
    switch($id){
      case 403:
      return array("value" => 403);
      break;

      case 404:
      return array("value" => 404);
      break;

      default:
      return array("value" => 404);
      break;
    }
  }

  public function viewAction() {
    $id = $this->params()->fromRoute('id');  // From RouteMatch
    $seo = $this->params()->fromRoute('seo');  // From RouteMatch

    $viewModel = new ViewModel();
    if($seo === 'contact-us') $viewModel->setTemplate('Frontend/contact-us.phtml');

    $application_model_page = $this->getServiceLocator()->get('application_model_page');
    $post = $application_model_page->fetchRow(array('seo' => $seo, 'status' => 1));

    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);

    return array("post" => $post, "posts" => $posts);
  }

  public function faqForBusinessLoanAction() {
    $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
    $faq = $application_model_faq->fetchRow(array("type" => "business_loan"));

    return array("faq" => $faq);
  }

  public function faqForPropertyLoanAction() {
    $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
    $faq = $application_model_faq->fetchRow(array("type" => "property_loan"));

    return array("faq" => $faq);
  }

  public function faqForGovernmentAssistedLoanAction() {
    $application_model_faq = $this->getServiceLocator()->get('application_model_faq');
    $faq = $application_model_faq->fetchRow(array("type" => "government_assisted_loan"));

    return array("faq" => $faq);
  }

  public function contactUsAction() {
    $seo = "contact-us";  // From RouteMatch
    $application_model_page = $this->getServiceLocator()->get('application_model_page');

    $request = $this->getRequest();
    if($request->isPost()) {
      $post = $request->getPost();
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');

        // Send email
      $application_view_helper_send_email = $viewHelperManager->get('send_email');
      $html = '<p>Name: <b>'.$post['contact_name'].'</b></p>';
      $html .= '<p>Email: <b>'.$post['contact_email'].'</b></p>';
      $html .= '<p>Phone: <b>'.$post['contact_phone'].'</b></p>';
      $html .= '<p>Message: <b>'.$post['contact_message'].'</b></p>';
      $application_view_helper_send_email($post['contact_subject'], $html, 'Best regard');

      $response = $this->getResponse();
      $response->setContent ( \Zend\Json\Json::encode ( array("success" => true) ) );
      return $response;
    }

    $post = $application_model_page->fetchRow(array('seo' => $seo, 'status' => 1));
    return array("post" => $post);
  }

  public function aboutUsAction() {
    $seo = "about-us";  // From RouteMatch
    $application_model_page = $this->getServiceLocator()->get('application_model_page');

    $application_model_widget = $this->getServiceLocator()->get('application_model_widget');
    $widget_1 = $application_model_widget->fetchRow(array("type" => "widget_1"));

    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);

    $post = $application_model_page->fetchRow(array('seo' => $seo, 'status' => 1));
    return array("post" => $post, "widget_1" => $widget_1, "posts" => $posts);
  }

  public function mortgageCalculatorAction() {

  }

  public function postQuestionAction() {
    $request = $this->getRequest();
    if($request->isPost()) {
      $post = $request->getPost();
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');

        // Send email
      $application_view_helper_send_email = $viewHelperManager->get('send_email');
      $html = '<p>Name: <b>'.$post['name'].'</b></p>';
      $html = '<p>Company: <b>'.$post['company'].'</b></p>';
      $html .= '<p>Email: <b>'.$post['email'].'</b></p>';
      $html .= '<p>Phone: <b>'.$post['phone'].'</b></p>';
      $html .= '<p>Message: <b>'.$post['message'].'</b></p>';
      $application_view_helper_send_email("Business Term Loan Question", $html, 'Best regard');

      $response = $this->getResponse();
      $response->setContent ( \Zend\Json\Json::encode ( array("success" => true, "msg" => "Post question successfully") ) );
      return $response;
    }
  }

  public function businessLoanCalculatorAction() {

  }

  public function crowfundingRepaymentCalculatorAction() {

  }

  public function businessLoanEligibilityCalculatorAction() {
    $request = $this->getRequest();
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $income_factor_addons = $application_model_setting->fetchAll(array('key' => array('base_average_noa_compare', 'base_net_profit_compare', 'base_annual_depreciation_compare', 'base_interest_expense_compare', 'income_factor')));

    if ($request->isPost())
    {
      $params = $request->getPost();
      $messages = array();
      $response = $this->getResponse();
      $data = $params;

      $model_business_loan_eligibility = $this->getServiceLocator()->get('application_model_business_loan_eligibility');
      $business_loan_eligibility = $model_business_loan_eligibility->fetchRow(array('token' => $params['_token']));

      if (!empty($business_loan_eligibility)){
        $id = $business_loan_eligibility->getId();
      } else {
        $business_loan_eligibility = new \Application\Entity\BusinessLoanEligibility;
        $business_loan_eligibility->setDateAdded(new Expression('NOW()'));
        $business_loan_eligibility->setToken($params['_token']);
      }

      $user_id = $user ? $user->getId() : NULL;
      $business_loan_eligibility->setUserId($user_id);
      $business_loan_eligibility->setData(\Zend\Json\Json::encode($data));
      $business_loan_eligibility->setDateModified(new Expression('NOW()'));

      if (!empty($id)){
        $business_loan_eligibility->setId($id);
        $added = $model_business_loan_eligibility->update($business_loan_eligibility);
      } else{
        $added = $model_business_loan_eligibility->insert($business_loan_eligibility);
      }

      if ($added) {
        $messages['success'] = true;
      } else {
        $messages['success'] = false;
        $messages['msg'] = $translator->translate("Something error. Please check");
      }

      $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
      return $response;
    }

    return array('income_factor_addons' => $income_factor_addons);
  }
}