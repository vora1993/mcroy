<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;
use Zend\Session\Container as Session;

use Zend\Crypt\Password\Bcrypt;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as SessionContainer;

class UserController extends AbstractActionController
{
    protected $storage;
    protected $authservice;

    public function indexAction() {
        return $this->redirect()->toRoute("frontend_user", array("action" => "profile"));
    }

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

    public function authAction() {
        $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
        $settings = $application_model_setting->fetchAll();

        $session_user = new Session('user');
        $redirect = $session_user->offsetGet('redirect');

        if ($this->getAuthService()->hasIdentity()) return $this->redirect()->toRoute("home");
        return array("redirect_url" => $redirect, "settings" => array("settings" => $settings));
    }

    public function auto_login($identity, $credential) {
        $this->getAuthService()->getAdapter()
                               ->setIdentity($identity)
                               ->setCredential($credential);
        $result = $this->getAuthService()->authenticate();
        if ($result->isValid()) {
            $this->getAuthService()->setStorage($this->getSessionStorage());
            $this->getAuthService()->getStorage()->write($identity);
            return true;
        }
        return false;
    }

    public function force_auto_login($identity) {
        $this->getAuthService()->setStorage($this->getSessionStorage());
        $this->getAuthService()->getStorage()->write($identity);
        return true;
    }

    public function registerAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();

        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $error = 0;

            $application_model_user = $this->getServiceLocator()->get('application_model_user');
            $email = $application_model_user->fetchRow(array('email' => $post['email'], 'status' => array(0,1,2,3)));
            if($email) {
                $error = 1;
                $messages['error']['email'][] = "{$post['email']} ".$translator->translate("has been exist");
            }

            if(!$error) {
                $user_entity = new \Application\Entity\User;
                $user_entity->setFirstName($post['firstname']);
                $user_entity->setLastName($post['lastname']);
                $display_name = $post['firstname']." ".$post['lastname'];
                $user_entity->setDisplayName($display_name);
                //$username = strstr($post['email'],'@',true); //get text before @
                $user_entity->setUsername($post['email']);
                $bcrypt = new Bcrypt;
                $bcrypt->setCost(10);
                $user_entity->setPassword($bcrypt->create($post['password']));
                $user_entity->setRoleId(2);
                $user_entity->setEmail($post['email']);
                $user_entity->setPhone($post['phone']);
                $user_entity->setDateOfBirth($post['date_of_birth']);
                $user_entity->setGender($post['gender']);
                $user_entity->setCompanyName($post['company_name']);
                $user_entity->setRef($this->generateRandomString(8));
                $user_entity->setNewsletter($post['newsletter']);
                $user_entity->setDateAdded(new Expression('NOW()'));
                $user_entity->setStatus(1);
                $added = $application_model_user->insert($user_entity);
                if($added) {
                    $messages['success'] = true;
                    $messages['msg'] = $translator->translate("Successfully registered");

                    $user_id = $added->getGeneratedValue();
                    if($post['ref_to'] || $post['ref']) {
                        $ref = $post['ref'] ? $post['ref'] : $post['ref_to'];
                        $user_ref = new \Application\Entity\UserRef;
                        $user_ref->setUserId($user_id);
                        $user_ref->setRef($ref);
                        $user_ref->setDateAdded(new Expression('NOW()'));

                        $application_model_user_ref = $this->getServiceLocator()->get('application_model_user_ref');
                        $application_model_user_ref->insert($user_ref);
                    }

                    // Auto Login
                    if($post['auto_login'] === 'yes') {
                        $identity = $post['email'];
                        $credential = $post['password'];
                        $auto_login = $this->auto_login($identity, $credential);
                    }

                    if($request->getPost('redirect_url')) {
                        $messages['redirect'] = $request->getPost('redirect_url');
                    } else {
                        $redirect_url = $router->assemble(array("action" => "register-success"), array('name' => 'frontend_user'));
                        $messages['redirect'] = $redirect_url;
                    }
                } else {
                    $messages['success'] = false;
                    $messages['msg'] = $translator->translate("Something error. Please check");

                    $redirect_url = $router->assemble(array("action" => "register-fail"), array('name' => 'frontend_user'));
                    $messages['redirect'] = $redirect_url;
                }
            }
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }

    public function signupAction() {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session_user = new Session('user');
        $redirect = $session_user->offsetGet('redirect');
        if($redirect) {
            $redirect_url = $router->assemble(array("action" => "apply-form"), array('name' => 'personal_loan'));
        }
        if ($this->getAuthService()->hasIdentity()) return $this->redirect()->toRoute("home");
        return array("redirect_url" => $redirect_url);
    }

    public function signinAction() {
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $session_user = new Session('user');
        $redirect = $session_user->offsetGet('redirect');
        if($redirect) {
            $redirect_url = $router->assemble(array("action" => "apply-form"), array('name' => 'personal_loan'));
        }
        if ($this->getAuthService()->hasIdentity()) return $this->redirect()->toRoute("home");
        return array("redirect_url" => $redirect_url);
    }

    public function registerSuccessAction() {
        if ($this->getAuthService()->hasIdentity()) return $this->redirect()->toRoute("home");
    }

    public function registerFailAction() {
        if ($this->getAuthService()->hasIdentity()) return $this->redirect()->toRoute("home");
    }

    public function loginAction()
	{
	    $translator = $this->getServiceLocator()->get('translator');
        //$routeMatch = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouteMatch();
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();

            // current route
            $current_route = $request->getPost('redirect_url') ? $request->getPost('redirect_url') : '';

            // setup variables
            $success = false;

            //check authentication...
            $this->getAuthService()->getAdapter()
                                   ->setIdentity($request->getPost('identity'))
                                   ->setCredential($request->getPost('credential'));

            $result = $this->getAuthService()->authenticate();
            $msg = "";
            foreach ($result->getMessages() as $message) {
                //save message temporary into flashmessenger
                $msg .= $message;
            }

            if ($result->isValid()) {
                $success = true;
                $redirect = $current_route;

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

        return $this->redirect()->toRoute("home");
    }

    /**
     * Facebook login / sign in
     */
    function getFirstName($name) {
        return implode(' ', array_slice(explode(' ', $name), 0, -1));
    }

    function getLastName($name) {
        return array_slice(explode(' ', $name), -1)[0];
    }

    public function facebookAuthAction() {
        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_setting = $viewHelperManager->get('setting');
        $setting = $application_view_helper_setting();
        $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
        $config = $this->getServiceLocator()->get('Config');
        $full_url = $config['application']['full_url'] ? $config['application']['full_url'] : '/';

        $fb = new \Facebook\Facebook ([
            'app_id' => $setting->facebook_app_id,
            'app_secret' => $setting->facebook_app_secret,
            'default_graph_version' => 'v2.2',
        ]);
        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch(\Facebook\Exceptions\FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(\Facebook\Exceptions\FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (! isset($accessToken)) {
            $permissions = array('public_profile','email'); // Optional permissions

            $login_link = $router->assemble(array("action" => "facebook-auth"), array('name' => 'frontend_user'));
            $loginUrl = $helper->getLoginUrl($full_url.$login_link, $permissions);
            header("Location: ".$loginUrl);
            exit;
        }

        try {
            // Returns a `Facebook\FacebookResponse` object
            $fields = array('id', 'name', 'email');
            $response = $fb->get('/me?fields='.implode(',', $fields).'', $accessToken);
        } catch(Facebook\Exceptions\FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch(Facebook\Exceptions\FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $graph_user = $response->getGraphUser();
        $facebook_id = $graph_user['id'];
        $display_name = $graph_user['name'];
        $email = $graph_user['email'];

        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        $application_model_user_facebook = $this->getServiceLocator()->get('application_model_user_facebook');

        $user = $application_model_user->fetchRow(array("email" => $email));
        if($user) {
            $user_id = $user->getId();
            $user_facebook = $application_model_user_facebook->fetchRow(array("user_id" => $user_id, "facebook_id" => $facebook_id));
            if(!$user_facebook) {
                $user_facebook = new \Application\Entity\UserFacebook;
                $user_facebook->setUserId($user_id);
                $user_facebook->setFacebookId($facebook_id);
                $application_model_user_facebook->insert($user_facebook);
            }
            $identity = $user->getEmail();
            $logged = $this->force_auto_login($identity);
        } else {
            $user = new \Application\Entity\User;
            $firstname = $this->getFirstName($display_name);
            $user->setFirstName($firstname);
            $lastname = $this->getLastName($display_name);
            $user->setLastName($lastname);
            $user->setDisplayName($display_name);
            $user->setEmail($email);
            $user->setUsername($email);
            $user->setRoleId(2);
            $bcrypt = new Bcrypt;
            $bcrypt->setCost(10);
            $user->setPassword($bcrypt->create($facebook_id));
            $user->setDateAdded(new Expression('NOW()'));
            $user->setStatus(1);
            $added = $application_model_user->insert($user);
            if($added) {
                $user_id = $added->getGeneratedValue();
                $user_facebook = new \Application\Entity\UserFacebook;
                $user_facebook->setUserId($user_id);
                $user_facebook->setFacebookId($facebook_id);
                $application_model_user_facebook->insert($user_facebook);
            }
            $identity = $email;
            $credential = $facebook_id;
            $logged = $this->auto_login($identity, $credential);
        }
        if($logged) {
            $phone = $user->getPhone();
            if(empty($phone)) {
                $redirectUrl = $router->assemble(array("action" => "update-field"), array('name' => 'frontend_user'));
            } else {
                $session_user = new Session('user');
                $redirect = $session_user->offsetGet('redirect');
                if($redirect) $redirectUrl = $redirect;
                else $redirectUrl = $router->assemble(array("action" => "profile"), array('name' => 'frontend_user'));
            }
        } else {
            $redirectUrl = $router->assemble(array("action" => "error", "id" => 403), array('name' => 'page'));
        }

        header("Location: ".$redirectUrl);
        exit;
    }

    public function updateFieldAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();

        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $application_model_user = $this->getServiceLocator()->get('application_model_user');
            if($user->getPhone() == '' || $user->getRef() == '') {
                $user->setPhone($post['phone']);
                $user->setRef($post['ref']);
                $updated = $application_model_user->update($user);

                $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
                if($updated) {
                    $messages['success']  = true;
                    $messages['msg']      = $translator->translate("Successfully updated");

                    $session_user = new Session('user');
                    $redirect = $session_user->offsetGet('redirect');
                    if($redirect) $messages['redirect'] = $redirect;
                    else $messages['redirect'] = $router->assemble(array("action" => "index"), array('name' => 'frontend_user'));
                } else {
                    $messages['success']  = false;
                    $messages['msg']      = $translator->translate("Something error. Please check");
                    $messages['redirect'] = $router->assemble(array("action" => "update-field"), array('name' => 'frontend_user'));
                }
            } else {
                $messages['success']  = false;
                $messages['msg']      = $translator->translate("Something error. Please check");
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("user" => $user);
    }

    /**
     * Referral page
     */
    public function referralAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();

        $user_id = $user->getId();
        $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
        $referrals = $application_model_referral->fetchAll(array("user_id" => $user_id, "status" => array(0,1,2)));
        $histories = $application_model_referral->fetchAll(array("user_id" => $user_id, "status" => array(3,4)));

        return array("user" => $user, "referrals" => $referrals, "histories" => $histories);
    }

    /**
     * Profile page
     */
    public function profileAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        return array("user" => $user);
    }

    public function editProfileAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        //$username = $this->getAuthService()->getIdentity();
        $id = $user->getId();

        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();

            $error = 0;
            if(!$error) {
                $user->setId($id);
                $user->setFirstName($post['firstname']);
                $user->setLastName($post['lastname']);
                $display_name = $post['firstname'].' '.$post['lastname'];
                $user->setDisplayName($display_name);
                $user->setPhone($post['phone']);
                $user->setDateOfBirth(date("Y-m-d", strtotime($post['date_of_birth'])));
                $user->setGender($post['gender']);
                $user->setCompanyName($post['company_name']);
                $user->setNewsletter($post['newsletter']);
                $user->setDescription($this->clearHtml($post['description']));

                // Logo
                $dir_user = 'data/user/';
                if($post['logo'] !== $user->getAvatar()) {
                    $dir_logo = $dir_user.$id;
                    if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

                    $dir_tmp = $dir_user.'/tmp/'.$post['logo'];
                    $dir_new = $dir_logo.'/'.$post['logo'];
                    if(file_exists($dir_tmp)) {
                        copy($dir_tmp, $dir_new);

                        $ext = pathinfo($post['logo'], PATHINFO_EXTENSION);
                        $new_avatar_name = $id.'.'.$ext;
                        rename($dir_new, $dir_logo.'/'.$new_avatar_name);

                        // Resize Image
                        $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
                        $application_view_helper_resizeimage($dir_logo, $new_avatar_name);

                        // Delete Folder tmp
                        $application_view_helper_folder = $viewHelperManager->get('folder');
                        $application_view_helper_folder("delete", $dir_user.'/tmp');

                        $user->setAvatar($new_avatar_name);
                    }
                }

                $now = new Expression('NOW()');
                $user->setDateModified($now);
                $edited = $application_model_user->update($user);
                if($edited) {
                    $messages['success']  = true;
                    $messages['msg']      = $translator->translate("Successfully updated");
                    $messages['redirect'] = "/user/profile";
                } else {
                    $messages['success']  = false;
                    $messages['msg']      = $translator->translate("Something error. Please check");
                }
            }
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("user" => $user);
    }

    /**
     * Bank account page
     */
    public function bankAccountAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();

        $user_id = $user->getId();
        $application_model_user_bank_account = $this->getServiceLocator()->get('application_model_user_bank_account');
        $bank_account = $application_model_user_bank_account->fetchRow(array("user_id" => $user_id));
        if(!$bank_account) {
            $bank_account = new \Application\Entity\UserBankAccount;
            $bank_account->setUserId($user_id);
            $bank_account->setName($user->getDisplayName());
            $bank_account->setDateAdded(new Expression('NOW()'));
            $application_model_user_bank_account->insert($bank_account);
        }
        return array("user" => $user, "bank" => $bank_account);
    }

    public function editBankAccountAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $user_id = $user->getId();

        $application_model_user_bank_account = $this->getServiceLocator()->get('application_model_user_bank_account');
        $bank_account = $application_model_user_bank_account->fetchRow(array("user_id" => $user_id));

        $request = $this->getRequest();
        $response = $this->getResponse();
        $messages = array();
        if ($request->isPost()) {
            $post = $request->getPost();
            $bank_account->setBank($post['bank']);
            $bank_account->setName($post['name']);
            $bank_account->setAcctNo($post['acct_no']);
            $bank_account->setDateModified(new Expression('NOW()'));

            $updated = $application_model_user_bank_account->update($bank_account);
            if($updated) {
                $messages['success']  = true;
                $messages['msg']      = $translator->translate("Successfully updated");
            } else {
                $messages['success']  = false;
                $messages['msg']      = $translator->translate("Something error. Please check");
            }
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
        return array("user" => $user, "bank" => $bank_account);
    }

    public function applicableAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();

        $application_model_business_loan = $this->getServiceLocator()->get('application_model_business_loan');
        $business_loans = $application_model_business_loan->fetchAll(array("user_id" => $user->getId()));

        $application_model_property_loan = $this->getServiceLocator()->get('application_model_property_loan');
        $property_loans = $application_model_property_loan->fetchAll(array("user_id" => $user->getId()));

        return array("user" => $user, "business_loans" => $business_loans, "property_loans" => $property_loans);
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

    public function changePasswordAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        //$username = $this->getAuthService()->getIdentity();
        $id = $user->getId();

        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $success = false;
            $result = array();
            $post = $request->getPost();
            $new_password = $post['password'];
            $current_password = $post['passwordCurrent'];

            $bcrypt = new Bcrypt();
            if ($bcrypt->verify($current_password, $user->getPassword())) {
                $securePass = $bcrypt->create($new_password);
                $now = new Expression('NOW()');
                $user->setId($id);
                $user->setDateModified($now);
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

    /**
     * Forgot Password page
     */
    public function forgotPasswordAction() {
        $response = $this->getResponse();
        $request = $this->getRequest();
        if ($request->isPost()) {
            $translator = $this->getServiceLocator()->get('translator');
            $error = 0;
            $result = array();
            $post = $request->getPost();
            $email = $post['email'];

            $application_model_user = $this->getServiceLocator()->get('application_model_user');
            $user = $application_model_user->fetchRow(array("email" => $email));
            if($user) {
                $current_token = $user->getToken();
                if(empty($current_token)) {
                    $token = $this->generateRandomString(12);
                    $user->setId($user->getId());
                    $user->setToken($token);
                    $application_model_user->update($user);

                    $router = $this->getServiceLocator()->get('Application')->getMvcEvent()->getRouter();
                    $reset_link = $router->assemble(array("action" => "reset-password", "id" => $user->getToken()), array('name' => 'frontend_user'));

                    $config = $this->getServiceLocator()->get('Config');
                    $full_url = $config['application']['full_url'] ? $config['application']['full_url'] : '/';

                    $content = "<p>".$translator->translate("Thanks you for visit")."</p>";
                    $content.= "<p>".$translator->translate("Please click");
                    $content.= '<a href="'.$full_url.'/'.$reset_link.'">'.$translator->translate("here").'</a>';
                    $content.= $translator->translate("to reset password")."</p>";;
                    $content.= "<p>".$translator->translate("Best regards")."</p>";

                    // Send Email
                    $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
                    $application_view_helper_send_email = $viewHelperManager->get('send_email_to_user');
                    $application_view_helper_send_email($email, $translator->translate("Reset password"), $content, $translator->translate("Best regard"));
                }
                $result['success'] = true;
                $result['msg'] = $translator->translate("Please check email to reset password");
            } else {
                $result['success'] = false;
                $result['msg'] = $translator->translate("This email not found in system");
            }
            $response->setContent ( \Zend\Json\Json::encode ( $result ) );
            return $response;
        }
        return array("user" => $user);
    }

    public function resetPasswordAction() {
        $application_model_user = $this->getServiceLocator()->get('application_model_user');
        $token = $this->params()->fromRoute('id');
        $user = $application_model_user->fetchRow(array("token" => $token));
        if($user) {
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
                $now = new Expression('NOW()');
                $user->setId($id);
                $user->setDateModified($now);
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
        } else {
            return $this->redirect()->toRoute("home");
        }
    }

    public function withdrawAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $id = $post['id'];

            $application_model_referral = $this->getServiceLocator()->get('application_model_referral');
            $referral = $application_model_referral->fetchRow(array("id" => $id));
            if($referral) {
                $referral->setId($id);
                $referral->setStatus(2); // Withdraw Pending
                $updated = $application_model_referral->update($referral);

                if($updated) {
                    $result['success'] = true;
                    $result['msg'] = $translator->translate("Successfully updated");
                } else {
                    $result['success'] = false;
                    $result['msg'] = $translator->translate("Something error. Please check");
                }
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $result ) );
      		return $response;
        }
    }

    public function addSubscribeAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $application_model_user = $this->getServiceLocator()->get('application_model_user');

        $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
        $application_view_helper_auth = $viewHelperManager->get('auth');
        $user = $application_view_helper_auth();
        $user_id = $user ? $user->getId() : 0;

        $application_model_subscribe = $this->getServiceLocator()->get('application_model_subscribe');
        $request = $this->getRequest();
        $messages = array();
        $error = 0;
        if ($request->isPost()) {
            $post = $request->getPost();

            $_subscribe = $application_model_subscribe->fetchRow(array("email" => $post['subscribe_email']));
            if($_subscribe) $error = $error + 1;

            if(!$error) {
                $subscribe = new \Application\Entity\Subscribe;
                $subscribe->setUserId($user_id);
                $subscribe->setEmail($post['subscribe_email']);
                $subscribe->setDateAdded(new Expression('NOW()'));
                $subscribe->setStatus(1);

                $added = $application_model_subscribe->insert($subscribe);
                if($added) {
                    $messages['success']  = true;
                    $messages['msg']      = $translator->translate("Thanks for Subscribing to our Newsletter. You will receive the latest deals and offers email");
                } else {
                    $messages['success']  = false;
                    $messages['msg']      = $translator->translate("Something error. Please check");
                }
            } else {
                $messages['success']  = false;
                $messages['msg']      = $translator->translate("You have already subscribed to our newsletter. Please try again other email.");
            }
            $response = $this->getResponse();
            $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
            return $response;
        }
    }

    function clearHtml($html) {
        $html = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $html);
        $html = preg_replace("/<div>(.*?)<\/div>/", "$1", $html);
        return $html;
    }

    function generateRandomString($length = 10) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
