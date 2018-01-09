<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

class CreditCardController extends AbstractActionController
{
  public function indexAction()
  {
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll(array('status' => 1));
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll(array('status' => 1));
    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);
    $application_model_slider = $this->getServiceLocator()->get('application_model_slider');
    $sliders = $application_model_slider->fetchAll(array('status' => array(0,1,2,3), 'type' => 1));
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $providers = $application_model_credit_card_provider->fetchAll(array('status' => 1));
    return array("credit_cards" => $credit_cards, "banks" => $banks, 'posts' => $posts, 'sliders' => $sliders, 'providers' => $providers);
  }

  public function selectAction() {
    $session = new Session('credit_card');
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
            $msg = $translator->translate("You have removed this credit card select list");
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

  public function filterAction()
  {
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');

    $request = $this->getRequest();
    if($request->isPost()) {
      $params = $request->getPost();
      $provider_ids = !empty($params['provider_ids']) ? $params['provider_ids'] : array();
      $query_arr = array('bank_id' => $params['bank_ids'], 'status' => 1);
      switch ($params['category_id']) {
        case 'points':
          $query_arr['points'] = '1';
          break;
        case 'air-miles':
          $query_arr['air_miles'] = '1';
          break;
        case 'cash-back':
          $query_arr['cash_back'] = '1';
          break;
        case 'discount':
          $query_arr['discount'] = '1';
          break;
      }

      $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
      $credit_cards = $application_model_credit_card->filter($query_arr, implode("|", $provider_ids));
      $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
      $banks = $application_model_bank->fetchAll(array('status' => 1));

      $htmlView = new ViewModel(array("credit_cards" => $credit_cards, "banks" => $banks, 'page' => $params['category_id']));
      $htmlOutput = $htmlView
        ->setTerminal(true)
        ->setTemplate('credit_card_details');
      return $htmlOutput;
    }
  }

  public function loadSelectAction()
  {
    $basePath = $this->serviceLocator->get('viewhelpermanager')->get('basePath');
    $session = new Session('credit_card');
    $count = 0;
    $html = '<div class="row">';
    if ($session->offsetExists('select'))
    {
      $select = $session->offsetGet('select');
      if (count($select) > 0)
      {
        $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
        $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
        foreach ($select as $id)
        {
          $credit_card = $application_model_credit_card->fetchRow(array("id" => $id));
          $bank = $application_model_bank->fetchRow(array("id" => $credit_card->getBankId()));
                    // Image
          $dir_logo = 'data/credit_cards/' . $credit_card->getId() . '/m_' . $credit_card->getLogo();
          if (!file_exists($dir_logo))
          {
            $dir_logo = '/assets/img/credit-card.png';
          }
          $html .= '<div class="col-xs-4 drawercard-col">';
          $html .= '<span class="drawercard-container filled" data-original-title="empty product holder" title="" id="ms-product-drawer-'. $credit_card->getId() .'" data-cid="'. $credit_card->getId() .'">';
          $html .= '<i class="fa fa-times" onclick="CreditCard.clear_select(this)" data-id="' .$credit_card->getId() . '"></i>';
          $html .= '<a href="#"><img src="' . $basePath($dir_logo) . '" alt="' . $credit_card->getName() .'" /></a>';
          $html .= '</span>';
          $html .= '<span class="drawercard-title">';
          $html .= '<a href="#">' . $credit_card->getName() . '</a>';
          $html .= '</span>';
          $html .= '</div>';
        }
        $count = count($select);
      }
    }
    $countValue = $count;
    while( $countValue < 3) {
      $html .= '<div class="col-xs-4 drawercard-col">';
      $html .= '<div class="drawercard-container" data-original-title="empty product holder" title="" id="ms-product-drawer-0" data-cid="">';
      $html .= '</div>';
      $html .= '</div>';
      $countValue += 1;
    }

    $html .= '</div>';
    $response = $this->getResponse();
    $response->setContent(\Zend\Json\Json::encode(array("html" => $html, "count" => count($select))));
    return $response;
  }

  public function clearSelectAction()
  {
    $session = new Session('credit_card');
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
        $msg = $translator->translate("You have removed this credit card selected list");
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

  public function popupCreditCardAction()
  {
    $session = new Session('credit_card');
    if ($session->offsetExists('select')) {
      $select = $session->offsetGet('select');
      if (count($select) > 0) {
        $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
        $credit_cards = $application_model_credit_card->fetchAll(array("id" => $select));
      }
    }

    $viewModel = new ViewModel();
    $viewModel->setTerminal(true);
    $viewModel->setVariables(array("credit_cards" => $credit_cards, "count" => count($select)));
    return $viewModel;
  }

  public function clearCompareAction()
  {
    $session = new Session('credit_card');
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
        $msg = $translator->translate("You have removed this credit card select list");
        $session->offsetSet('select', $select_arr);
        $count = count($select_arr);
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

  public function compareAction()
  {
    $session = new Session('credit_card');
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll();
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll();

    if ($session->offsetExists('select'))
    {
      $select = $session->offsetGet('select');
      if (count($select) > 0)
      {
        $credit_cards_compare = $application_model_credit_card->fetchAll(array("id" => $select));
      }
    }

    return array("credit_cards_compare" => $credit_cards_compare, "credit_cards" => $credit_cards);
  }

  public function pointsAction()
  {
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll(array('points' => '1'));
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll();
    $application_model_slider = $this->getServiceLocator()->get('application_model_slider');
    $sliders = $application_model_slider->fetchAll(array('status' => array(0,1,2,3), 'type' => 1));
    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $providers = $application_model_credit_card_provider->fetchAll(array('status' => 1));
    return array("credit_cards" => $credit_cards, "banks" => $banks, "sliders" => $sliders, 'posts' => $posts, 'providers' => $providers);
  }

  public function discountAction()
  {
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll(array('discount' => '1'));
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll();
    $application_model_slider = $this->getServiceLocator()->get('application_model_slider');
    $sliders = $application_model_slider->fetchAll(array('status' => array(0,1,2,3), 'type' => 1));
    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $providers = $application_model_credit_card_provider->fetchAll(array('status' => 1));
    return array("credit_cards" => $credit_cards, "banks" => $banks, "sliders" => $sliders, 'posts' => $posts, 'providers' => $providers);
  }

  public function airMilesAction()
  {
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll(array('air_miles' => '1'));
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll();
    $application_model_slider = $this->getServiceLocator()->get('application_model_slider');
    $sliders = $application_model_slider->fetchAll(array('status' => array(0,1,2,3), 'type' => 1));
    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $providers = $application_model_credit_card_provider->fetchAll(array('status' => 1));
    return array("credit_cards" => $credit_cards, "banks" => $banks, "sliders" => $sliders, 'posts' => $posts, 'providers' => $providers);
  }

  public function cashBackAction()
  {
    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll(array('cashback' => '1'));
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll();
    $application_model_slider = $this->getServiceLocator()->get('application_model_slider');
    $sliders = $application_model_slider->fetchAll(array('status' => array(0,1,2,3), 'type' => 1));
    $application_model_post = $this->getServiceLocator()->get('application_model_post');
    $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 8);
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $providers = $application_model_credit_card_provider->fetchAll(array('status' => 1));
    return array("credit_cards" => $credit_cards, "banks" => $banks, "sliders" => $sliders, 'posts' => $posts, 'providers' => $providers);
  }

  public function addItemCompare()
  {
    $session = new Session('credit_card');
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
            $msg = $translator->translate("You have removed this credit card select list");
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
}
