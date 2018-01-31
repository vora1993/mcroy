<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Db\Sql\Expression;
use Mynamespace\SimpleImage;

class DesignController extends AbstractActionController
{
    public function indexAction() {
        $application_model_design = $this->getServiceLocator()->get('application_model_design');
        $loan = $application_model_design->fetchRow(array('id' => 1));
        $request = $this->getRequest();
        if ($request->isPost()) {
            $post = $request->getPost();
            $messages = array();
            $translator = $this->getServiceLocator()->get('translator');
            $loan = new \Application\Entity\Design;

            //set json feautures
            $feautures=[];
            for($i=1;$i<=6;$i++)
            {
            	$feauture_title='feauture_title_'.$i;
            	$feauture_content='feauture_content_'.$i;
            	$feautures[$i]=[
            		$feauture_title=>$post[$feauture_title],
            		$feauture_content=>$post[$feauture_content]
            	];
            }
            $feautures=json_encode($feautures);
            $loan->setId(1);
            $loan->setFeautures($feautures);
            $loan->setTestimonialsTitle($post['testimonials_title']);
            $loan->setTestimonialsSign($post['testimonials_sign']);
            $added = $application_model_design->update($loan);
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
        return array('loan' => $loan);
    }
}