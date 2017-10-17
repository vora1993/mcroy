<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;
use Zend\Session\Container as SessionContainer;

class UserController extends AbstractActionController
{
    protected $storage;
    protected $authservice;
    
    public function getAuthService()
    {
        if (! $this->authservice) {
            $this->authservice = $this->getServiceLocator()->get('AuthService');
        }

        return $this->authservice;
    }
    
    public function getSessionStorage()
    {
        if (! $this->storage) {
            $this->storage = $this->getServiceLocator()->get('Backend\Model\AuthStorage');
        }

        return $this->storage;
    }
    
    public function indexAction() {
        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        $users = $application_model_user->fetchAll();
        return array("users" => $users);
    }
    
    public function addAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $error = 0;
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            
            $application_model_user = $this->getServiceLocator()->get('application_model_user');
            
            $email = $application_model_user->fetchRow(array('email' => $post['email']));
            if($email) {
                $error = 1;
                $messages['error']['email'][] = "{$post['email']} ".$translator->translate("has been exist");
            }
            
            if(!$error) {
                $application_entity_user = new \Application\Entity\User;
                $application_entity_user->setUsername($post['email']);
                $bcrypt = new Bcrypt;
                $bcrypt->setCost(10);
                $application_entity_user->setPassword($bcrypt->create($post['password']));
                $application_entity_user->setRoleId($post['role_id']);
                $application_entity_user->setEmail($post['email']);
                $firstname = $post['firstname'];
                $lastname = $post['lastname'];
                if($firstname || $lastname) {
                    $application_entity_user->setFirstName($firstname);
                    $application_entity_user->setLastName($lastname);
                    $displayname = $firstname.' '.$lastname;
                } else {
                    $_email_parts = explode("@", $post['email']);
                    $displayname = $_email_parts[0];
                }
                $application_entity_user->setDisplayName(trim($displayname));
                $application_entity_user->setPhone($post['phone']);
                $application_entity_user->setDescription($post['description']);
                $application_entity_user->setDateOfBirth(date("Y-m-d", strtotime($post['date_of_birth'])));
                $application_entity_user->setGender($post['gender']);
                $application_entity_user->setCompanyName($post['company_name']);
                $application_entity_user->setNewsletter($post['newsletter']);
                $application_entity_user->setDateAdded(new Expression('NOW()'));
                $application_entity_user->setAvatar($post['logo']);
                $application_entity_user->setStatus($post['status']);
                $added = $application_model_user->insert($application_entity_user);
                if($added) {
                    $messages['success'] = true;
                    $messages['msg'] = $translator->translate("Successfully added");
                    
                    // Logo
                    $dir_user = 'data/user/';
                    if($post['logo']) {
                        $dir_logo = $dir_user.$added->getGeneratedValue();
                        $this->deleteFolder($dir_logo);
                        
                        if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);
                            
                        $dir_tmp = $dir_user.'/tmp/'.$post['logo'];
                        $dir_new = $dir_logo.'/'.$post['logo'];
                        if(file_exists($dir_tmp)) copy($dir_tmp, $dir_new);
                            
                        $this->resizeLogo($dir_logo, $post['logo']);
                        $this->deleteFolder($dir_user.'/tmp');
                    }
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Something error. Please check");
                }
            }
            
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }
    
    public function editAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        $user = $application_model_user->fetchRow(array('id' => $id));
        if($user) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $response = $this->getResponse();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                
                $error = 0;
                if(!$error) {
                    $user->setId($id);
                    $user->setRoleId($post['role_id']);
                    $firstname = $post['firstname'];
                    $lastname = $post['lastname'];
                    if($firstname || $lastname) {
                        $user->setFirstName($firstname);
                        $user->setLastName($lastname);
                        $displayname = $firstname.' '.$lastname;
                        $user->setDisplayName($displayname);
                    } 
                    $user->setPhone($post['phone']);
                    $user->setDescription($post['description']);
                    $user->setDateOfBirth($post['date_of_birth']);
                    $user->setGender($post['gender']);
                    $user->setCompanyName($post['company_name']);
                    $user->setNewsletter($post['newsletter']);
                    
                    // Logo
                    $dir_user = 'data/user/';
                    if($post['logo'] !== $user->getAvatar()) {
                        $dir_logo = $dir_user.$id;
                        $this->deleteFolder($dir_logo);
                        if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);
                        
                        $dir_tmp = $dir_user.'/tmp/'.$post['logo'];
                        $dir_new = $dir_logo.'/'.$post['logo'];
                        if(file_exists($dir_tmp)) {
                            copy($dir_tmp, $dir_new);
                            
                            $ext = pathinfo($post['logo'], PATHINFO_EXTENSION);
                            $new_avatar_name = $id.'.'.$ext;
                            rename($dir_new, $dir_logo.'/'.$new_avatar_name);
                            
                            $this->resizeLogo($dir_logo, $new_avatar_name);
                            $this->deleteFolder($dir_user.'/tmp');
                            $user->setAvatar($new_avatar_name);
                        }
                    }
                    
                    $now = new Expression('NOW()');
                    $user->setDateModified($now);
                    $user->setStatus($post['status']);
                    $edited = $application_model_user->update($user);
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
            
            return array('user' => $user);
        } else {
            return $this->redirect()->toRoute("admin/user"); 
        }
    }
    
    public function setStatusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_user_model = $this->getServiceLocator()->get('application_model_user');
            
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
                $user = $application_user_model->fetchRow(array('id' => $id));
                $user->setId($id);
                $user->setStatus($status);
                $user->setDateModified(new Expression('NOW()'));
                
                $updated = $application_user_model->update($user);
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
    
    public function loginAction()
	{
        $this->layout('layout/login');
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            
            // setup variables
            $success = false;
            
            //check authentication...
            $this->getAuthService()->getAdapter()->setIdentity($request->getPost('identity'))->setCredential($request->getPost('credential'));
            
            $result = $this->getAuthService()->authenticate();
            $msg = "";
            foreach ($result->getMessages() as $message) {
                //save message temporary into flashmessenger
                $msg .= $message;
            }
            
            if ($result->isValid()) {
                $success = true;
                $redirect = 'admin';
                
                //check if it has rememberMe :
                if ($request->getPost('rememberme') == 1 ) {
                    $this->getSessionStorage()->setRememberMe(1);
                    
                    //set storage again
                    $this->getAuthService()->setStorage($this->getSessionStorage());
                }
                $this->getAuthService()->setStorage($this->getSessionStorage());
                
                // Storage User
                //$user_mapper = $this->getServiceLocator()->get('user_mapper');
                //$user = $user_mapper->fetchRow(array("username" => $request->getPost('username')));
                
                // set the user id in storage
                //$resultRow = $this->getAuthService()->getAdapter()->getResultRowObject();
                //$storage = $resultRow->user_id;
        
                $this->getAuthService()->getStorage()->write($request->getPost('identity'));
            }
            $messages['success']  = $success;
            $messages['redirect'] = $redirect;
            $messages['msg']      = $msg;
            
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
	}
    
    public function logoutAction()
    {
        if ($this->getAuthService()->hasIdentity()) {
            $this->getSessionStorage()->forgetMe();
            $this->getAuthService()->clearIdentity();
        }

        return $this->redirect()->toRoute("admin/user", array("action" => "login"));
    }
    
    public function myProfileAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $id = $user->getId();
        
        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
                
            $error = 0;
            if(!$error) {
                $user->setId($id);
                $firstname = $post['firstname'];
                $lastname = $post['lastname'];
                if($firstname || $lastname) {
                    $user->setFirstName($firstname);
                    $user->setLastName($lastname);
                    $displayname = $firstname.' '.$lastname;
                    $user->setDisplayName($displayname);
                } 
                $user->setPhone($post['phone']);
                $user->setDescription($post['description']);
                    
                // Logo
                $dir_user = 'data/user/';
                if($post['logo'] !== $user->getAvatar()) {
                    $dir_logo = $dir_user.$id;
                    $this->deleteFolder($dir_logo);
                    if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);
                        
                    $dir_tmp = $dir_user.'/tmp/'.$post['logo'];
                    $dir_new = $dir_logo.'/'.$post['logo'];
                    if(file_exists($dir_tmp)) {
                        copy($dir_tmp, $dir_new);
                            
                        $ext = pathinfo($post['logo'], PATHINFO_EXTENSION);
                        $new_avatar_name = $id.'.'.$ext;
                        rename($dir_new, $dir_logo.'/'.$new_avatar_name);
                            
                        $this->resizeLogo($dir_logo, $new_avatar_name);
                        $this->deleteFolder($dir_user.'/tmp');
                        $user->setAvatar($new_avatar_name);
                    }
                }
                    
                $now = new Expression('NOW()');
                $user->setDateModified($now);
                $edited = $application_model_user->update($user);
                if($edited) {
                    $messages['success'] = true;
                    $messages['msg']     = $translator->translate("Successfully updated");
                } else {
                    $messages['success'] = false;
                    $messages['msg']     = $translator->translate("Something error. Please check.");
                }
            }
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("user" => $user);
    }
    
    public function changePasswordAction() {
        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        
        $username = $this->getAuthService()->getIdentity();
        $user = $application_model_user->fetchRow(array("username" => $username));
        $id = $user->getId();
        
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $success = false;
            $result = array();
            $post = $request->getPost();
            $new_password = $post['password'];
            $current_password = $post['passwordCurrent'];
            
            $bcrypt = new Bcrypt();
            if ($bcrypt->verify($current_password, $user->getPassword())) {
                $securePass = $bcrypt->create($new_password);
                $user->setId($id);
                $user->setDateModified(new Expression('NOW()'));
                $user->setPassword($securePass);
                $updated = $application_model_user->update($user);
                if($updated) {
                    $success = true;
                    $msg = $translator->translate("Successfully updated");
                } else {
                    $msg = $translator->translate("Something error. Please check");
                }
            } else {
                $msg = $translator->translate("The current password is NOT correct");
            }
            
            $result['success'] = $success;
            $result['msg'] = $translator->translate($msg);
            $response->setContent ( \Zend\Json\Json::encode ( $result ) );
            return $response;
        }
        return array("user" => $user);
    }
    
    public function resetPasswordAction() {
        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        $id = $this->params()->fromRoute('id');
        $user = $application_model_user->fetchRow(array("id" => $id));
        
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $error = 0;
            $result = array();
            $post = $request->getPost();
            $new_password = $post['password'];
            $bcrypt = new Bcrypt();
            $securePass = $bcrypt->create($new_password);
            $user->setId($id);
            $user->setDateModified(new Expression('NOW()'));
            $user->setPassword($securePass);
            $updated = $application_model_user->update($user);
            if(!$updated) $error = $error + 1;
            
            if(!$error) {
                $result['success'] = true;
                $result['msg'] = $translator->translate("Successfully updated");
            } else {
                $result['success'] = false;
                $result['msg'] = $translator->translate("Something error. Please check");
            }
            $response->setContent ( \Zend\Json\Json::encode ( $result ) );
            return $response;
        }
        return array("user" => $user);
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
                $dir = 'data/user/tmp';
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
                            'src'      => '/data/user/tmp/'.$newFilename,
                            'msg'      => $translator->translate("Upload logo successful!"),
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
    
    public function resizeLogo($dir, $filename) {
        $image_src = $dir.DIRECTORY_SEPARATOR.$filename;
        $image = new SimpleImage();
        $image->load($image_src);                     
        $image_height = $image->get_height();
        $image_width = $image->get_width();
        
        // 512px
        if($image_height > $image_width) {
            if($image_height > 512) {
                $image->fit_to_height(512);
                $image->save($image_src);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 512) {
                $image->fit_to_width(512);
                $image->save($image_src);
            }
        } else {
            if($image_height > 512) {
                $image->resize(512, 512);
                $image->save($image_src);
            }
        }
        
        // 256px
        if($image_height > $image_width) {
            if($image_height > 256) {
                $image->fit_to_height(256);
                $image->save($dir.DIRECTORY_SEPARATOR.'l_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 256) {
                $image->fit_to_width(256);
                $image->save($dir.DIRECTORY_SEPARATOR.'l_'.$filename);
            }
        } else {
            if($image_height > 256) {
                $image->resize(256, 256);
                $image->save($dir.DIRECTORY_SEPARATOR.'l_'.$filename);
            }
        }
        
        // 128px
        if($image_height > $image_width) {
            if($image_height > 128) {
                $image->fit_to_height(128);
                $image->save($dir.DIRECTORY_SEPARATOR.'m_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 128) {
                $image->fit_to_width(128);
                $image->save($dir.DIRECTORY_SEPARATOR.'m_'.$filename);
            }
        } else {
            if($image_height > 128) {
                $image->resize(128, 128);
                $image->save($dir.DIRECTORY_SEPARATOR.'m_'.$filename);
            }
        }
        
        // 64px
        if($image_height > $image_width) {
            if($image_height > 64) {
                $image->fit_to_height(64);
                $image->save($dir.DIRECTORY_SEPARATOR.'xs_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 64) {
                $image->fit_to_width(64);
                $image->save($dir.DIRECTORY_SEPARATOR.'xs_'.$filename);
            }
        } else {
            if($image_height > 64) {
                $image->resize(64, 64);
                $image->save($dir.DIRECTORY_SEPARATOR.'xs_'.$filename);
            }
        }
        
        // 32px
        if($image_height > $image_width) {
            if($image_height > 32) {
                $image->fit_to_height(32);
                $image->save($dir.DIRECTORY_SEPARATOR.'s_'.$filename);
            }
        } elseif($image_height < $image_width) {
            if($image_width > 32) {
                $image->fit_to_width(32);
                $image->save($dir.DIRECTORY_SEPARATOR.'s_'.$filename);
            }
        } else {
            if($image_height > 32) {
                $image->resize(32, 32);
                $image->save($dir.DIRECTORY_SEPARATOR.'s_'.$filename);
            }
        }
    }
    
    public function roleAction() {
        $application_model_rolw = $this->getServiceLocator()->get('application_model_role');
        $user_roles = $application_model_rolw->fetchAll();
        return array("user_roles" => $user_roles);
    }
    
    public function addRoleAction() {
        $config = $this->getServiceLocator()->get('Config');
        $routes = $config['router']['routes'];
        $invokables = $config['controllers']['invokables'];
        $roles = array();
        $privileges = array();
        $resouces = array();
        foreach ($invokables as $key => $value) {
            $value = str_replace("\\", "/", $value);
            $controllerName = explode('/', $value)[0];
            $routerName = explode('/', $value)[2];
            $routerName = str_replace("Controller", "", $routerName);
            
            if($controllerName === 'Backend') {
                $resouces[] = $key;
            }
            // $route
            if(in_array($key, $resouces)) {
                $key = "admin/".$key;
                $key = str_replace("admin/admin", "admin", $key);
            } 
            
            $source = 'module/'.$controllerName.'/src/'.$value.'.php';
            $fh = fopen($source,'r');
            $m = array();
            while ($line = fgets($fh)) {
                if(preg_match_all('/function(.*?)Action()/', $line, $matches)){
                    $function = str_replace("(.*?)", "", $matches[1][0]);
                    $function = preg_replace('/([A-Z])/', '-$1', $function);
                    $action = trim(strtolower($function));
                    if($action !== "") {
                        $m[] = $action;
                        $privileges[] = strtolower($key).'--'.$action;
                    }
                }
            }
            $m = array_filter($m);
            $roles[$key] = $m;
        }
        
        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $post = $request->getPost();
            $error = 0;
            
            $application_model_role = $this->getServiceLocator()->get('application_model_role');
            $userrole = $application_model_role->fetchRow(array('name' => $post['name']));
            if($userrole) {
                $error = 1;
                $messages['error']['name'][] = "{$post['name']} ".$translator->translate("has been exist");
            } 
            
            if(!$error) {
                $userrole_entity = new \Admin\Entity\UserRole;
                $userrole_entity->setName($post['name']);
                $role_name = strtolower($post['name']);
                $key = str_replace(" ", "_", $role_name);
                $userrole_entity->setKey($key);
                $now = new Expression('NOW()');
                $userrole_entity->setDateAdded($now);
                $userrole_entity->setStatus($post['status']);
                
                // Privileges
                $userrole_entity->setAllow(\Zend\Json\Json::encode ( $post['allow'] ));
                $deny_arr = array_diff($privileges, $post['allow']);;
                $deny = array();
                if(count($deny_arr) > 0) {
                    foreach ($deny_arr as $k => $v) {
                        $deny[] = $v;
                    }
                }
                $userrole_entity->setDeny(\Zend\Json\Json::encode ( $deny ));
                    
                $deny_arr = explode(",", $post['deny']);
                $deny = array();
                foreach ($deny_arr as $k => $v) {
                    $deny[] = $v;
                }
                $userrole_entity->setDeny(\Zend\Json\Json::encode ( $deny ));
                    
                $added = $application_model_role->insert($userrole_entity);
                if($added) {
                    $messages['success'] = true;
                    $messages['msg'] = "Successfully added";
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = "Something error. Please check";
                }
            }
            
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("roles" => $roles);
    }
    
    public function editRoleAction() {
        $config = $this->getServiceLocator()->get('Config');
        $routes = $config['router']['routes'];
        $invokables = $config['controllers']['invokables'];
        $roles = array();
        $privileges = array();
        $resouces = array();
        foreach ($invokables as $key => $value) {
            $value = str_replace("\\", "/", $value);
            $controllerName = explode('/', $value)[0];
            $routerName = explode('/', $value)[2];
            $routerName = str_replace("Controller", "", $routerName);
            
            if($controllerName === 'Backend') {
                $resouces[] = $key;
            }
            // $route
            if(in_array($key, $resouces)) {
                $key = "admin/".$key;
                $key = str_replace("admin/admin", "admin", $key);
            } 
            
            $source = 'module/'.$controllerName.'/src/'.$value.'.php';
            $fh = fopen($source,'r');
            $m = array();
            while ($line = fgets($fh)) {
                if(preg_match_all('/function(.*?)Action()/', $line, $matches)){
                    $function = str_replace("(.*?)", "", $matches[1][0]);
                    $function = preg_replace('/([A-Z])/', '-$1', $function);
                    $action = trim(strtolower($function));
                    if($action !== "") {
                        $m[] = $action;
                        $privileges[] = strtolower($key).'--'.$action;
                    }
                }
            }
            $m = array_filter($m);
            $roles[$key] = $m;
        }
        
        $id = $this->params()->fromRoute('id');
        $application_model_role = $this->getServiceLocator()->get('application_model_role');
        $userrole = $application_model_role->fetchRow(array('status' => array(0,1,2,3), 'id' => $id));
        if($userrole) {
            $translator = $this->getServiceLocator()->get('translator');
            
            $request = $this->getRequest();
            $messages = array();
            if ($request->isPost()) {
                $post = $request->getPost();
                $error = 0;
                
                $userrole_name = $application_model_role->fetchName($post['name'], $id);
                if($userrole_name) {
                    $error = 1;
                    $messages['error']['name'][] = "{$post['name']} ".$translator->translate("has been exist");
                } 
                
                if(!$error) {
                    $userrole->setId($id);
                    $userrole->setName($post['name']);
                    $role_name = strtolower($post['name']);
                    $key = str_replace(" ", "_", $role_name);
                    $userrole->setKey($key);
                    $now = new Expression('NOW()');
                    $userrole->setDateModified($now);
                    
                    // Privileges
                    $userrole->setAllow(\Zend\Json\Json::encode ( $post['allow'] ));
                    $deny_arr = array_diff($privileges, $post['allow']);;
                    $deny = array();
                    if(count($deny_arr) > 0) {
                        foreach ($deny_arr as $k => $v) {
                            $deny[] = $v;
                        }
                    }
                    $userrole->setDeny(\Zend\Json\Json::encode ( $deny ));
                    
                    $edited = $application_model_role->update($userrole);
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
            return array('userrole' => $userrole, "roles" => $roles);
        } else {
            return $this->redirect()->toRoute("admin/user", array("action" => "role")); 
        }
    }
    
    public function statusRoleAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_role = $this->getServiceLocator()->get('application_model_role');
            
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
                $userrole = $application_model_role->fetchRow(array('id' => $id));
                $userrole->setId($id);
                if($userrole->getIsDefault() != 1) $userrole->setStatus($status);
                $userrole->setDateModified(new Expression('NOW()'));
                
                $updated = $application_model_role->update($userrole);
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
    
    public function setSubscribeStatusAction() {
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $application_model_subscribe = $this->getServiceLocator()->get('application_model_subscribe');
            
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
                $subscribe = $application_model_subscribe->fetchRow(array('id' => $id));
                $subscribe->setId($id);
                $subscribe->setStatus($status);
                $subscribe->setDateModified(new Expression('NOW()'));
                
                $updated = $application_model_subscribe->update($subscribe);
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
    
    public function subscribeAction() {
        $application_model_subscribe = $this->getServiceLocator()->get('application_model_subscribe');
        $subscribes = $application_model_subscribe->fetchAll(array("status" => array(0,1,2,3)));
        return array("subscribes" => $subscribes);
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
}