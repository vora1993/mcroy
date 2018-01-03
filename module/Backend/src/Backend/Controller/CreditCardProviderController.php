<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class CreditCardProviderController extends AbstractActionController
{
  public function indexAction() {
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $providers = $application_model_credit_card_provider->fetchAll();
    return array("providers" => $providers);
  }

  public function addAction() {
    $request = $this->getRequest();
    if ($request->isPost()) {
      $post = $request->getPost();
      $messages = array();
      $translator = $this->getServiceLocator()->get('translator');

      $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
      $provider = new \Application\Entity\CreditCardProvider;
      $provider->setName($post['name']);
      $provider->setDateAdded(new Expression('NOW()'));
      $provider->setDateModified(new Expression('NOW()'));
      $provider->setLogo($post['logo']);
      $provider->setStatus($post['status']);
      $added = $application_model_credit_card_provider->insert($provider);
      if($added) {
        $messages['success'] = true;
        $messages['msg'] = $translator->translate("Successfully added");

                // Logo
        $dir_provider = 'data/providers/';
        if($post['logo']) {
          $dir_logo = $dir_provider.$added->getGeneratedValue();
          if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

          $dir_tmp = $dir_provider.'/tmp/'.$post['logo'];
          $dir_new = $dir_logo.'/'.$post['logo'];
          if(file_exists($dir_tmp)) copy($dir_tmp, $dir_new);

          $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
          $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
          $application_view_helper_folder = $viewHelperManager->get('folder');

          $application_view_helper_resizeimage($dir_logo, $post['logo']);
          $application_view_helper_folder("delete", $dir_provider.'/tmp');
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
    $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');
    $provider = $application_model_credit_card_provider->fetchRow(array('id' => $id));
    if($provider) {
      $translator = $this->getServiceLocator()->get('translator');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();

        $error = 0;
        if(!$error) {
          $provider->setId($id);
          $provider->setName($post['name']);
          $provider->setDateModified(new Expression('NOW()'));
          $provider->setStatus($post['status']);

                    // Logo
          $dir_provider = 'data/providers/';
          if($post['logo'] !== $provider->getLogo()) {
            $dir_logo = $dir_provider.$id;
            if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

            $dir_tmp = $dir_provider.'/tmp/'.$post['logo'];
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
              $application_view_helper_folder("delete", $dir_provider.'/tmp');

              $provider->setLogo($new_logo_name);
            }
          }

          $edited = $application_model_credit_card_provider->update($provider);
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

      return array('provider' => $provider);
    } else {
      return $this->redirect()->toRoute("admin/bank");
    }
  }

  public function setStatusAction() {
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_credit_card_provider = $this->getServiceLocator()->get('application_model_credit_card_provider');

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
        $provider = $application_model_credit_card_provider->fetchRow(array('id' => $id));
        $provider->setId($id);
        $provider->setStatus($status);
        $provider->setDateModified(new Expression('NOW()'));

        $updated = $application_model_credit_card_provider->update($provider);
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
      $file_validator = new \Zend\Validator\File\Extension($valid_formats, true);
      $name = $file['name'];
      if(strlen($name)) {
        $dir = 'data/providers/tmp';
        if (!file_exists($dir)) {
          mkdir($dir, 0777, true);
        }

        $file_path = pathinfo($name);
        $ext = $file_path['extension'];

        if(in_array(strtolower($ext), $valid_formats)) {
          $newFilename = time(). '.' . $ext;
          $tmp = $file['tmp_name'];
          if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
            $messages = array(
              'success'  => true,
              'name'     => $newFilename,
              'src'      => '/data/providers/tmp/'.$newFilename,
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