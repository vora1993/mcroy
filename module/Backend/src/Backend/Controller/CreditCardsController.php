<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class CreditCardsController extends AbstractActionController
{
  public function indexAction() {
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_cards = $application_model_credit_card->fetchAll();
    return array("credit_cards" => $credit_cards);
  }

  public function addAction() {
    $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
    $banks = $application_model_bank->fetchAll();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $post = $request->getPost();
      $messages = array();
      $translator = $this->getServiceLocator()->get('translator');

      $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
      $credit_card = new \Application\Entity\CreditCard;
      $credit_card->setName($post['name']);
      $credit_card->setDateAdded(new Expression('NOW()'));
      $credit_card->setDateModified(new Expression('NOW()'));
      $credit_card->setDataAttributes(\Zend\Json\Json::encode($post['data']));
      $credit_card->setColor($post['color']);
      $credit_card->setStatus($post['status']);
      $credit_card->setLogo($post['logo']);
      $added = $application_model_credit_card->insert($credit_card);
      if($added) {
        $messages['success'] = true;
        $messages['msg'] = $translator->translate("Successfully added");

                // Logo
        $dir_credit_card = 'data/credit_cards/';
        if($post['data']['logo']) {
          $dir_logo = $dir_credit_card.$added->getGeneratedValue();
          if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

          $dir_tmp = $dir_credit_card.'/tmp/'.$post['logo'];
          $dir_new = $dir_logo.'/'.$post['logo'];
          if(file_exists($dir_tmp)) copy($dir_tmp, $dir_new);

          $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
          $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
          $application_view_helper_folder = $viewHelperManager->get('folder');

          $application_view_helper_resizeimage($dir_logo, $post['logo']);
          $application_view_helper_folder("delete", $dir_credit_card.'/tmp');
        }
      } else {
        $messages['success'] = false;
        $messages['msg'] = $translator->translate("Something error. Please check");
      }
      $response = $this->getResponse();
      $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
      return $response;
    }

    return array("banks" => $banks);
  }

  public function editAction() {
    $id = $this->params()->fromRoute('id');
    $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');
    $credit_card = $application_model_credit_card->fetchRow(array('id' => $id));

    if($credit_card) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_bank = $this->getServiceLocator()->get('application_model_bank');
      $banks = $application_model_bank->fetchAll();
      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();

        $error = 0;
        if(!$error) {
          $credit_card->setId($id);
          $credit_card->setName($post['name']);
          $credit_card->setDateModified(new Expression('NOW()'));
          $credit_card->setStatus($post['status']);
          $credit_card->setDataAttributes(\Zend\Json\Json::encode($post['data']));
                    // Logo
          $dir_credit_card = 'data/credit_cards/';
          if($post['logo'] !== $credit_card->getLogo()) {
            $dir_logo = $dir_credit_card.$id;
            if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

            $dir_tmp = $dir_credit_card.'/tmp/'.$post['logo'];
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
              $application_view_helper_folder("delete", $dir_credit_card.'/tmp');

              $credit_card->setLogo($new_logo_name);
            }
          }
          $credit_card->setColor($post['color']);

          $edited = $application_model_credit_card->update($credit_card);
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

      return array('credit_card' => $credit_card, 'banks' => $banks);
    } else {
      return $this->redirect()->toRoute("admin/credit_cards");
    }
  }

  public function setStatusAction() {
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_credit_card = $this->getServiceLocator()->get('application_model_credit_card');

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
        $user = $application_model_credit_card->fetchRow(array('id' => $id));
        $user->setId($id);
        $user->setStatus($status);
        $user->setDateModified(new Expression('NOW()'));

        $updated = $application_model_credit_card->update($user);
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
        $dir = 'data/credit_cards/tmp';
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
              'src'      => '/data/credit_cards/tmp/'.$newFilename,
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