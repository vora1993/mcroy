<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class InfographicController extends AbstractActionController
{
    public function indexAction() {
        $application_model_infographic = $this->getServiceLocator()->get('application_model_infographic');
        $infographics = $application_model_infographic->fetchAll();
        return array("infographics" => $infographics);
    }
    
    public function addAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            
            $application_model_infographic = $this->getServiceLocator()->get('application_model_infographic');
            $infographic = new \Application\Entity\Infographic;
            $infographic->setCreatedBy($user->getId());
            $infographic->setTitle($post['title']);
            $infographic->setDateAdded(new Expression('NOW()'));
            $infographic->setImage($post['image']);
            $infographic->setPdf($post['pdf']);
            $infographic->setStatus($post['status']);
            $added = $application_model_infographic->insert($infographic);
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
    
    public function editAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        
        $id = $this->params()->fromRoute('id');
        $application_model_infographic = $this->getServiceLocator()->get('application_model_infographic');
        $infographic = $application_model_infographic->fetchRow(array('id' => $id));
        if($infographic) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                
                $infographic->setId($id);
                $infographic->setModifiedBy($user->getId());
                $infographic->setTitle($post['title']);
                $infographic->setDateModified(new Expression('NOW()'));
                $infographic->setStatus($post['status']);
                $infographic->setImage($post['image']);
                $infographic->setPdf($post['pdf']);   
                    
                $updated = $application_model_infographic->update($infographic);
                if($updated) {
                    $messages['success'] = true;
                    $messages['msg']     = $translator->translate("Successfully updated");
                } else {
                    $messages['success'] = false;
                    $messages['msg']     = $translator->translate("Something error. Please check");
                }
                
                $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                return $response;
            }
            
            return array('infographic' => $infographic);
        } else {
            return $this->redirect()->toRoute("admin/infographic"); 
        }
    }
    
    public function setStatusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_infographic = $this->getServiceLocator()->get('application_model_infographic');
            
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
                $user = $application_model_infographic->fetchRow(array('id' => $id));
                $user->setId($id);
                $user->setStatus($status);
                $user->setDateModified(new Expression('NOW()'));
                
                $updated = $application_model_infographic->update($user);
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
    
    public function changeImageAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $file = $this->params()->fromFiles('file');
            $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp");
            $name = $file['name'];
			if(strlen($name)) {
                $dir = 'data/image';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext, $valid_formats)) {
                    $newFilename = "infographic_".date("YmdHis"). '.' . $ext;
                    $tmp = $file['tmp_name'];
                    if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
                        $messages = array(
                            'success'  => true,
                            'name'     => $newFilename,
                            'src'      => '/data/image/'.$newFilename,
                            'msg'      => $translator->translate("Upload image successful"),
                        );
                    } else {
                        $messages = array('success' => false, 'msg' => $translator->translate("Something error. Please check"));
                    }
                } else {
                    $messages = array('success' => false, 'msg' => $translator->translate("Invalid file formats"));
                }
            } else {
                $messages = array('success' => false, 'msg' => $translator->translate("Please select image"));
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
  		return $response;
    }
    
    public function changeAttachmentAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $file = $this->params()->fromFiles('file');
            $valid_formats = array("pdf");
            $name = $file['name'];
			if(strlen($name)) {
                $dir = 'data/pdf';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext, $valid_formats)) {
                    $newFilename = "infographic_".date("YmdHis"). '.' . $ext;
                    $tmp = $file['tmp_name'];
                    if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
                        $messages = array(
                            'success'  => true,
                            'name'     => $newFilename,
                            'src'      => '/data/pdf/'.$newFilename,
                            'msg'      => $translator->translate("Upload file successful"),
                        );
                    } else {
                        $messages = array('success' => false, 'msg' => $translator->translate("Something error. Please check"));
                    }
                } else {
                    $messages = array('success' => false, 'msg' => $translator->translate("Invalid file formats"));
                }
            } else {
                $messages = array('success' => false, 'msg' => $translator->translate("Please select file"));
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
  		return $response;
    }
    
}