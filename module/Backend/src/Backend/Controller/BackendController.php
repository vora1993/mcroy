<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class BackendController extends AbstractActionController
{
	public function indexAction()
	{
        if (!$this->getServiceLocator()->get('AuthService')->hasIdentity()) {
            return $this->redirect()->toRoute("admin/user", array("action" => "login"));
        }
	}
}