<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class MediaController extends AbstractActionController
{
	public function indexAction()
	{
        $application_model_media = $this->getServiceLocator()->get('application_model_media');
        $medias = $application_model_media->fetchAll();
        return new ViewModel(array("medias" => $medias));
	}
    
    public function addAction() 
    {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $translator = $this->getServiceLocator()->get('translator');
        $user_id = $user->getId();
        
        $messages = array();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $application_model_media = $this->getServiceLocator()->get('application_model_media');
        
            $file = $this->params()->fromFiles('file');
            $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp");
            $name = $file['name'];
			if(strlen($name)) {
                $dir = 'data/media';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext, $valid_formats)) {
                    $tmp = $file['tmp_name'];
                    
                    $info = pathinfo($file['name']);
                    $file_name = basename($file['name'],'.'.$info['extension']);
                    $newFilename = $this->slug($file_name). '.' . $ext;
                    if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
                        list($width, $height, $type, $attr) = getimagesize($dir.'/'.$newFilename);
                        
                        
                        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
                        $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                        $application_view_helper_resizeimage($dir, $newFilename);
                        
                        $media_entity = new \Application\Entity\Media;
                        $media_entity->setTitle($file_name);
                        $media_entity->setAuthorId($user_id);
                        $media_entity->setSrc($newFilename);
                        $media_entity->setType($file['type']);
                        $media_entity->setSize($file['size']);
                        $now = new Expression('NOW()');
                        $media_entity->setDateAdded($now);
                        
                        $added = $application_model_media->insert($media_entity);
                        if($added) $success = true;
                        else $success = false;
                        $messages = array('success'  => $success, 'id' => $added->getGeneratedValue());
                    } else {
                        $messages = array('success' => false);
                    }
                } else {
                    $messages = array('success' => false);
                }
            } else {
                $messages = array('success' => false);
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
  		    return $response;
        }
    }
    
    public function deleteAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator  = $this->getServiceLocator()->get('translator');
            $application_model_media = $this->getServiceLocator()->get('application_model_media');
            
            $id        = $this->params()->fromPost('id');
            $media     = $application_model_media->fetchRow(array('id' => $id));
            $dir_media = 'data/media/'.$media->getSrc();
            if(file_exists($dir_media)) unlink($dir_media);
            
            $dir_m_media = 'data/media/m_'.$media->getSrc();
            if(file_exists($dir_m_media)) unlink($dir_m_media);
            
            $deleted = $application_model_media->delete(array('id' => $id));
            if($deleted) {
                $messages['success'] = true;
            } else {
                $messages['success'] = false;
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function trashAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator  = $this->getServiceLocator()->get('translator');
            $application_model_media = $this->getServiceLocator()->get('application_model_media');
            
            $error = 0;
            $ids = $this->params()->fromPost('ids');
            foreach ($ids as $id) {
                $media     = $application_model_media->fetchRow(array('id' => $id));
                $dir_media = 'data/media/'.$media->getSrc();
                if(file_exists($dir_media)) unlink($dir_media);
                
                $dir_m_media = 'data/media/m_'.$media->getSrc();
                if(file_exists($dir_m_media)) unlink($dir_m_media);
                
                $deleted = $application_model_media->delete(array('id' => $id));
                if(!$deleted) $error = $error + 1;
            }
            if($error) {
                $messages['success'] = false;
            } else {
                $messages['success'] = true;
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function editAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_media = $this->getServiceLocator()->get('application_model_media');
        $media = $application_model_media->fetchRow(array('id' => $id));
        if($media) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                $error = 0;
                if(!$error) {
                    $media->setId($id);
                    $media->setTitle($post['title']);
                    $media->setCaption($post['caption']);
                    $media->setAlt($post['alt']);
                    $media->setDescription($post['description']);
                    $now = new Expression('NOW()');
                    $media->setDateModified($now);
                    $updated = $application_model_media->update($media);
                    if($updated) {
                        $messages['success'] = true;
                        $messages['msg']     = $translator->translate("Your media has been successfully updated!");
                    } else {
                        $messages['success'] = false;
                        $messages['msg']     = $translator->translate("Something error. Please check.");
                    }
                }
                $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
                return $response;
            }
            
            return array('media' => $media);
        } else {
            return $this->redirect()->toRoute("zfcadmin/media"); 
        }
    }
    
    public function loadLibraryAction() {
        $request = $this->getRequest();
        $response = $this->getResponse();
        $data = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $method = $post['func'];
            if($method == '') die('No direct access. No access identifier found.');
            
            //$config = $this->getServiceLocator()->get('config');
            //$path = $config['application']['base_path'] ? $config['application']['base_path'] : '/';
            $basePath = $this->getServiceLocator()->get('viewhelpermanager')->get('basePath');
            
            if($method === 'load_thumbs') {
                if($post['ipp'] == '') $ipp=30;
                else $ipp = $post['ipp'];
                if($post['page']=='') $page=0; 
                else $page = $post['page']-1;
                
                $limit = $page * $ipp;
                
                $application_model_media = $this->getServiceLocator()->get('application_model_media');
                $complete = $application_model_media->fetchAll();
                $medias = $application_model_media->fetchFind($limit, $ipp);
                foreach ($medias as $key => $media) {
                    $image_src   = $basePath("data/media/".$media->getSrc());
                    $image_thumb = $basePath("data/media/m_".$media->getSrc());
                    $image_small = $basePath("data/media/s_".$media->getSrc());
                    $image_large = $basePath("data/media/l_".$media->getSrc());
                    $data[$key] = array(
                        'id'      => $media->getId(),
                        'type'    => $ext = pathinfo($media->getSrc(), PATHINFO_EXTENSION),
                        'title'   => $media->getTitle(),
                        'caption' => $media->getCaption() ? $media->getCaption() : '',
                        'alt'     => $media->getAlt() ? $media->getAlt() : '',
                        'url'     => $image_src,
                        'thumb'   => $image_thumb,
                        'small'   => $image_small,
                        'large'   => $image_large,
                        'newtime' => date("l, jS M Y, h:i:s a", strtotime($media->getDateAdded())),
                        'uid'     => $media->getAuthorId(),
                        'size'    => $this->format_size_units($media->getSize()),
                    );
                }
                
                $data['total']  = count($medias);
                $data['page']   = $page + 1;
                $data['ipp']    = $ipp;
                $data['gtotal'] = count($complete);
            }
            
            if($method === 'mlib_get_import_methods') {
                $application_model_media_import = $this->getServiceLocator()->get('application_model_media_import');
                $imports = $application_model_media_import->fetchAll();
                foreach ($imports as $key => $media) {
                    $data[$key] = array(
                        'id'       => $media->getId(),
                        'title'    => html_entity_decode($media->getTitle(), ENT_QUOTES, "UTF-8"),
                        'content'  => html_entity_decode($media->getContent(), ENT_QUOTES, "UTF-8"),
                        'contentx' => $media->getContent(),
                    );
                }
                $data['total']  = count($imports);
            }
            
            $response->setContent ( \Zend\Json\Json::encode ( $data ) );
        }
        return $response;
    }
    
    public function urlUploadAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $translator = $this->getServiceLocator()->get('translator');
        $user_id = $user->getId();
        
        $request = $this->getRequest();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $urls = explode("\n", $post['urls']);
            
            $application_model_media = $this->getServiceLocator()->get('application_model_media');
            $mlib_allowed_images = array("jpg", "jpeg", "png", "gif");
            $mlib_allowed_filetypes = array("txt", "pdf", "doc", "docx", "ppt", "zip", "rar");
            $error = 0;
            foreach ($urls as $url) {
                $url = trim($url);
                if (filter_var($url, FILTER_VALIDATE_URL)) {
                    $file = pathinfo($url);
                    $ext = strtolower($file['extension']);
                    if(in_array($ext, $mlib_allowed_images)) {
                        $data = $this->upload_from_url($url);
                        
                        $media_entity = new \Application\Entity\Media;
                        $media_entity->setTitle($data['title']);
                        $media_entity->setAuthorId($user_id);
                        $media_entity->setSrc($data['fname']);
                        //$media_entity->setType($file['type']);
                        //$media_entity->setSize($file['size']);
                        $now = new Expression('NOW()');
                        $media_entity->setDateAdded($now);
                        
                        $added = $application_model_media->insert($media_entity);
                        if(!$added) $error = $error + 1;
                    } elseif(in_array($ext, $mlib_allowed_filetypes)) {
                        $data = $this->upload_from_url($url);
                        
                    } else {
                        $msg = $ext.' is not a valid file. No extension was found.';
                    }
                }
            }
            $messages['error'] = $error;
            $messages['msg'] = $msg;
            
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }
    
    function upload_from_url($url){
        $fname = pathinfo($url);
        $ext = strtolower($fname['extension']);
        $title = $this->slug($fname['filename']);
        $get_file1 = file_get_contents($url);
        $new_file1 = fopen('data/media/'.$title.'.'.$ext, "w");
        fwrite($new_file1, $get_file1);
        fclose($new_file1);

        $data['fname'] = $title.'.'.$ext;
        $data['id'] = $title;
        $data['title'] = $fname['filename'];
        $data['ext'] = $ext;
        return $data;
    }
    
    function format_size_units($bytes)
    {
        if ($bytes >= 1073741824)
        {
            $bytes = number_format($bytes / 1073741824, 2) . ' GB';
        }
        elseif ($bytes >= 1048576)
        {
            $bytes = number_format($bytes / 1048576, 2) . ' MB';
        }
        elseif ($bytes >= 1024)
        {
            $bytes = number_format($bytes / 1024, 2) . ' kB';
        }
        elseif ($bytes > 1)
        {
            $bytes = $bytes . ' bytes';
        }
        elseif ($bytes == 1)
        {
            $bytes = $bytes . ' byte';
        }
        else
        {
            $bytes = '0 bytes';
        }

        return $bytes;
    }
    
    public function slug($string)
    {
        $slug = trim($string); // trim the string
        $slug = preg_replace('/[^a-zA-Z0-9 -]/', '', $slug); // only take alphanumerical characters, but keep the spaces and dashes too...
        $slug = str_replace(' ', '-', $slug); // replace spaces by dashes
        $slug = strtolower($slug);  // make it lowercase
        return $slug;
    }
}