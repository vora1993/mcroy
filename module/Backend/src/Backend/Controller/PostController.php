<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class PostController extends AbstractActionController
{
    public function indexAction()
	{
        $application_model_post = $this->getServiceLocator()->get('application_model_post');
        $posts = $application_model_post->fetchAll(array('status' => array(0,1,2,3)));
        
        return array('posts' => $posts);
	}
    
    public function categoryAction() {
        $application_model_category = $this->getServiceLocator()->get('application_model_category');
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $source      = $this->params()->fromPost('source');
            
            $category = $application_model_category->fetchRow(array('id' => $source));
            $category->setId($source);
            $category->setDateModified(new Expression('NOW()'));
            $application_model_category->update($category);
            
            $ordering       = \Zend\Json\Json::decode($this->params()->fromPost('order'));
	        $rootOrdering   = \Zend\Json\Json::decode($this->params()->fromPost('rootOrder'));
            
            if($ordering) {
                foreach ($ordering as $key => $item_id) {
                    $order = $key + 1;
                    $itemToOrder = $application_model_category->fetchRow(array('id' => $item_id));
                    if($itemToOrder) {
                        $itemToOrder->setId($item_id);
                        $itemToOrder->setSortOrder($order);
                        $itemToOrder->setDateModified(new Expression('NOW()'));
                        $application_model_category->update($itemToOrder);
                    }
                }
            } else {
                foreach($rootOrdering as $key => $item_id){
                    $order = $key + 1;
                    $itemToOrder = $application_model_category->fetchRow(array('id' => $item_id));
    	            if($itemToOrder){
                        $itemToOrder->setId($item_id);
                        $itemToOrder->setSortOrder($order);
                        $itemToOrder->setDateModified($now);
    	                $application_model_category->update($itemToOrder);
                    }
                }
    	    }
            return $response->setContent ( \Zend\Json\Json::encode ( array("success" => true) ) );
        }
        
        $categories = $application_model_category->fetchAll(array('status' => 1));
        $category_html  = $this->buildCategory($categories);
        return array("category_html" => $category_html);
    }
    
    public function buildCategory($categories) {
        $html = '<ol class="dd-list" id="accordion">';
        foreach ($categories as $item)  {
            $id = $item->getId();
            $label = $item->getName();
            $sort_order = $item->getSortOrder();
            $html .= "<li class='dd-item nested-list-item panel' style='border: 0;' data-order='{$sort_order}' data-id='{$id}'>";
            $html .= "<div class='dd-handle nested-list-handle' style='height: 100% !important;'><span class='glyphicon glyphicon-move'></span></div>";
            $html .= "<div class='nested-list-content'>";
            $html .= "{$label}";
            $html .= "<div class='pull-right'><a href='#collapse_{$id}' class='accordion-toggle accordion-toggle-styled collapsed' data-toggle='collapse' data-parent='#accordion'>Edit</a> | <a href='javascript:;' onclick='delete_category({$id})' rel='{$id}'>Delete</a></div></div>";
            $html .= "<div id='collapse_{$id}' class='panel-collapse collapse'>";
            $html .= "<div class='panel-body' style='padding-left: 50px;'>";
            $html .= "<label class='control-label'>Title</label>";
            $html .= "<input type='text' class='form-control' name='label_title' value='{$label}'>";
            $html .= "<div style='margin-top: 12px; text-align: right;'><button type='button' class='btn blue ladda-button' onclick='edit_category(this, {$id})' data-style='expand-left'>Update</button></div>";
            $html .= "</div></div>";
            $html .= "</li>";
        }
        $html .= '</ol>';
        
        return $html;
    }
    
    public function addCategoryAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $application_model_category = $this->getServiceLocator()->get('application_model_category');
            $category_entity = new \Application\Entity\Category;
            $category_entity->setName($post['name']);
            $category_entity->setDateAdded(new Expression('NOW()'));
            $category_entity->setStatus(1);
            $added = $application_model_category->insert($category_entity);
            if($added) {
                $messages['success'] = true;
                $messages['msg'] = $translator->translate("Successfully added");
            } else {
                $messages['success'] = false;
                $messages['msg'] = $translator->translate("Something error. Please check");
            }
            
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }
    
    public function editCategoryAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_category = $this->getServiceLocator()->get('application_model_category');
            
            $category_id      = $this->params()->fromPost('category_id');
            $category_name    = $this->params()->fromPost('category_name');
            
            $category = $application_model_category->fetchRow(array('id' => $category_id));
            if($category) {
                $category->setId($category_id);
                $category->setName($category_name);
                $now = new Expression('NOW()');
                $category->setDateModified($now);
                    
                $updated = $application_model_category->update($category);
                if($updated) {
                    $messages['success'] = true;
                } else {
                    $messages['success'] = false;
                }
            } else {
                $messages['success'] = false;
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function deleteCategoryAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_category = $this->getServiceLocator()->get('application_model_category');
            
            $category_id   = $this->params()->fromPost('category_id');
            $category = $application_model_category->fetchRow(array('id' => $category_id));
            $category->setId(($category->getId()));
            $category->setStatus(4);
            
            $deleted = $application_model_category->update($category);
            if($deleted) {
                $messages['success'] = true;
            } else {
                $messages['success'] = false;
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
    }
    
    public function editAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $translator = $this->getServiceLocator()->get('translator');
        $user_id = $user->getId();
        
        $id = $this->params()->fromRoute('id');
        $application_model_post = $this->getServiceLocator()->get('application_model_post');
        $post = $application_model_post->fetchRow(array('id' => $id));
        if($post) {
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $data = $request->getPost();
                $error = 0;
                $current_seo = $post->getSeo();
                if($current_seo !== $data['seo']) {
                    $error = 1;
                    $messages['error']['seo'][] = "{$data['seo']} " . $translator->translate("has been exist!");
                    
                    $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                    $application_view_helper_resizeimage('data/image', $data['featured_image']);
                }
                
                if(!$error) {
                    $post->setId($id);
                    $post->setPostTitle($data['post_title']);
                    $post->setCategoryId($data['category_id']);
                    $post->setSeo($data['seo']);
                    $post->setPostExcerpt($data['post_excerpt']);
                    $post->setPostContent($data['post_content']);
                    $post->setFeaturedImage($data['featured_image']);
                    if($data['post_date']) {
                        $post_date = explode("/", $data['post_date']);
                        $date_post = $post_date[2].'-'.$post_date[1].'-'.$post_date[0].' '.date("H:i:s");
                        $post->setPostDate($date_post);
                    }
                    $post->setHits(0);
                    $post->setFeatured($data['featured']);
                    $post->setDateModified(new Expression('NOW()'));
                    $attachment = explode(",", $data['attachment']);
                    $attachment_arr = array_values(array_filter($attachment));
                    $post->setAttachment(\Zend\Json\Json::encode ( $attachment_arr ));
                    
                    $updated = $application_model_post->update($post);
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
        } else {
            return $this->redirect()->toRoute("post"); 
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
            $application_model_post = $this->getServiceLocator()->get('application_model_post');
            $current_seo = $application_model_post->fetchRow(array("seo" => $post['seo']));
            if($current_seo) {
                $error = 1;
                $messages['error']['seo'][] = "{$post['seo']} " . $translator->translate("has been exist");
            }
            if(!$error) {
                $post_entity = new \Application\Entity\Post;
                $post_entity->setPostAuthor($user_id);
                if($post['post_date']) {
                    $post_date = explode("/", $post['post_date']);
                    $date_post = $post_date[2].'-'.$post_date[1].'-'.$post_date[0].' '.date("H:i:s");
                    $post_entity->setPostDate($date_post);
                }
                $post_entity->setPostTitle($post['post_title']);
                $post_entity->setPostExcerpt($post['post_excerpt']);
                $post_entity->setPostContent($post['post_content']);
                $post_entity->setCategoryId($post['category_id']);
                $post_entity->setHits(0);
                $post_entity->setFeatured($post['featured']);
                $post_entity->setSeo($post['seo']);
                $post_entity->setDateAdded(new Expression('NOW()'));
                $post_entity->setStatus($post['status']);
                $post_entity->setFeaturedImage($post['featured_image']);
                $attachment = explode(",", $post['attachment']);
                $attachment_arr = array_values(array_filter($attachment));
                $post_entity->setAttachment(\Zend\Json\Json::encode ( $attachment_arr ));
                
                // Resize image
                $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                $application_view_helper_resizeimage('data/image', $post['featured_image']);
                
                $added = $application_model_post->insert($post_entity);
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
     * Upload Attachment
     */
    public function uploadAttachmentAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $file = $this->params()->fromFiles('file');
            $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp", "pdf", "doc", "txt", "docx", "xls", "xlsx");
            $name = $file['name'];
			if(strlen($name)) {
                $dir = 'data/post/documents';
                if (!file_exists($dir)) {
                    mkdir($dir, 0777, true);
                }
                
                list($txt, $ext) = explode(".", $name);
                if(in_array($ext, $valid_formats)) {
                    $newFilename = $this->slug($txt).'-'.$this->randomString(6). '.' . $ext;
                    $tmp = $file['tmp_name'];
                    if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
                        $messages = array(
                            'success'  => true,
                            'name'     => $newFilename,
                            'src'      => '/data/post/documents/'.$newFilename,
                            'msg'      => $translator->translate("Upload file successful!"),
                        );
                    } else {
                        $messages = array('success' => false, 'msg' => $translator->translate("Something error. Please check."));
                    }
                } else {
                    $messages = array('success' => false, 'msg' => $translator->translate("Invalid file formats..!"));
                }
            } else {
                $messages = array('success' => false, 'msg' => $translator->translate("Please select file."));
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
  		return $response;
    }
    
    /**
     * Delete Attachment
     */
    public function deleteAttachmentAction() {
        $messages = array();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $id = $this->params()->fromPost('id');
            $filename = $this->params()->fromPost('name');
            
            if($filename) {
                $dir_attachment = 'data/post/documents/'.$filename;
                if(file_exists($dir_attachment)) unlink($dir_attachment); 
                $messages['success'] = true;
            } else {
                $messages['success'] = false;
            }
        }
        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
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
            
        $application_model_post = $this->getServiceLocator()->get('application_model_post');
        $posts = $application_model_post->search(array('status' => 1), $query);
        
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
            $application_model_post = $this->getServiceLocator()->get('application_model_post');
            
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
                $post = $application_model_post->fetchRow(array('id' => $id));
                $post->setId($id);
                $post->setStatus($status);
                $now = new Expression('NOW()');
                $post->setDateModified($now);
                
                $updated = $application_model_post->update($post);
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
        $str = preg_replace('/(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)/', 'a', $str);
        $str = preg_replace('/(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)/', 'e', $str);
        $str = preg_replace('/(ì|í|ị|ỉ|ĩ)/', 'i', $str);
        $str = preg_replace('/(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)/', 'o', $str);
        $str = preg_replace('/(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)/', 'u', $str);
        $str = preg_replace('/(ỳ|ý|ỵ|ỷ|ỹ)/', 'y', $str);
        $str = preg_replace('/(đ)/', 'd', $str);
        $str = preg_replace('/[^a-z0-9-\s]/', '', $str);
        $str = preg_replace('/([\s]+)/', '-', $str);
        return $str;
    }
}