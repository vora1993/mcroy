<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class TestimonialController extends AbstractActionController
{
    public function indexAction()
	{
        $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
        $testimonials = $application_model_testimonial->fetchAll(array('status' => array(0,1,2,3)));
        
        return array('testimonials' => $testimonials);
	}
    
    public function editAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $translator = $this->getServiceLocator()->get('translator');
        $user_id = $user->getId();
        
        $id = $this->params()->fromRoute('id');
        $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
        $post = $application_model_testimonial->fetchRow(array('id' => $id));
        if($post) {
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $data = $request->getPost();
                $error = 0;
                
                if(!$error) {
                    $post->setId($id);
                    $post->setName($data['name']);
                    $post->setCompany($data['company']);
                    $post->setPosition($data['position']);
                    $post->setContent($data['content']);
                    if($data['url']) $post->setUrl($data['url']);
                    $post->setDateModified(new Expression('NOW()'));
                    $updated = $application_model_testimonial->update($post);
                    if($updated) {
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
            
            return array('post' => $post);
        } 
    }
    
    /**
     * Add post
     */
    public function addAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $translator = $this->getServiceLocator()->get('translator');
        $user_id = $user->getId();
        
        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $error = 0;
            $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
            if(!$error) {
                $post_entity = new \Application\Entity\Testimonial;
                $post_entity->setName($post['name']);
                $post_entity->setCompany($post['company']);
                $post_entity->setPosition($post['position']);
                $post_entity->setContent($post['content']);
                if($post['url']) $post_entity->setUrl($post['url']);
                $post_entity->setDateAdded(new Expression('NOW()'));
                $post_entity->setStatus($post['status']);
                if($post['url']) {
                    $post_entity->setUrl($post['url']);    
                    // Resize image
                    $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                    $application_view_helper_resizeimage('data/image', $post['url']);
                }
                
                $added = $application_model_testimonial->insert($post_entity);
                if($added) {
                    $messages['success'] = true;
                    $messages['msg'] = $translator->translate("Successfully added");
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Something error. Please check");
                }
            }
            
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }
    
    /**
     * Change Image
     */
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
                    $newFilename = $name;
                    $tmp = $file['tmp_name'];
                    if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
                        $messages = array(
                            'success'  => true,
                            'name'     => $newFilename,
                            'src'      => '/data/image/'.$newFilename,
                            'msg'      => $translator->translate("Upload image successful"),
                        );
                    } else {
                        $messages = array('success' => false, 'msg' => $translator->translate("Something error. Please check."));
                    }
                } else {
                    $messages = array('success' => false, 'msg' => $translator->translate("Invalid file formats"));
                }
            } else {
                $messages = array('success' => false, 'msg' => $translator->translate("Please select photo."));
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
  		return $response;
    }
    
    /**
     * Load json
     */
    public function loadJsonAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $messages = array();
        $response = $this->getResponse();
        
        $result = array();
        $query = $this->params()->fromQuery('query');
            
        $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
        $posts = $application_model_testimonial->search(array('status' => 1), $query);
        
        $data = array();    
        if($posts) {
            foreach ($posts as $post) {
                $data[] = array(
                    'value' => $post->getName(),
                    'data'  => $post->getId()
                );
            }
        }
        $result['suggestions'] = $data;
        
        $response->setContent ( \Zend\Json\Json::encode ( $result ) );
  		return $response;
    }
    
    public function resizeImage($dir, $filename) {
        $image_src = $dir.DIRECTORY_SEPARATOR.$filename;
        $image = new SimpleImage();
        $image->load($image_src);                     
        $image_height = $image->getHeight();
        $image_width = $image->getWidth();
        
        if($image_height > $image_width) {
            if($image_height > 512) {
                $image->resizeToHeight(512);
                $image->save($dir.DIRECTORY_SEPARATOR.'thumb_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 512) {
                $image->resizeToWidth(512);
                $image->save($dir.DIRECTORY_SEPARATOR.'thumb_'.$filename);
            }
        } else {
            if($image_height > 512) {
                $image->resize(512, 512);
                $image->save($dir.DIRECTORY_SEPARATOR.'thumb_'.$filename);
            }
        }
    }
    
    function clearHtml($html) {
        $html = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $html);
        $html = preg_replace("/<div>(.*?)<\/div>/", "$1", $html);
        return $html;
    }
    
    function existFolder($folder)
    {
        // Get canonicalized absolute pathname
        $path = realpath($folder);
    
        // If it exist, check if it's a directory
        if($path !== false AND is_dir($path))
        {
            // Return canonicalized absolute pathname
            return $path;
        }
    
        // Path/folder does not exist
        return false;
    }
    
    function deleteFolder($directory)
    {
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file)) { 
                $this->deleteFolder($file);
            } else {
                unlink($file);
            }
        }
        if($this->existFolder($directory)) rmdir($directory);
    }
    
    public function setStatusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
            
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
                $post = $application_model_testimonial->fetchRow(array('id' => $id));
                $post->setId($id);
                $post->setStatus($status);
                $now = new Expression('NOW()');
                $post->setDateModified($now);
                
                $updated = $application_model_testimonial->update($post);
                if(!$updated) $error = $error + 1;
            }
            if(!$error) {
                $result['success'] = true;
                $result['msg'] = $translator->translate("Your managed table has been successfully updated!");
            } else {
                $result['success'] = false;
                $result['msg'] = $translator->translate("Something error. Please check.");
            }
        }
        return new JsonModel($result);
    }
    
    public function insertImageAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $file = $this->params()->fromFiles('file');
            $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp");
            $name = $file['name'];
			if(strlen($name)) {
                $dir = 'data/image/tmp';
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
                            'src'      => '/data/image/tmp/'.$newFilename,
                            'msg'      => $translator->translate("Upload image successful!"),
                        );
                    } else {
                        $messages = array('success' => false, 'msg' => $translator->translate("Something error. Please check."));
                    }
                } else {
                    $messages = array('success' => false, 'msg' => $translator->translate("Invalid file formats..!"));
                }
            } else {
                $messages = array('success' => false, 'msg' => $translator->translate("Please select photo."));
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
  		return $response;
    }
    
    protected function randomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
    
    protected function slug($str)
    {
        $str = trim(mb_strtolower($str));
        $str = preg_replace('/(à|á|?|?|ã|â|?|?|?|?|?|a|?|?|?|?|?)/', 'a', $str);
        $str = preg_replace('/(è|é|?|?|?|ê|?|?|?|?|?)/', 'e', $str);
        $str = preg_replace('/(ì|í|?|?|i)/', 'i', $str);
        $str = preg_replace('/(ò|ó|?|?|õ|ô|?|?|?|?|?|o|?|?|?|?|?)/', 'o', $str);
        $str = preg_replace('/(ù|ú|?|?|u|u|?|?|?|?|?)/', 'u', $str);
        $str = preg_replace('/(?|ý|?|?|?)/', 'y', $str);
        $str = preg_replace('/(d)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}