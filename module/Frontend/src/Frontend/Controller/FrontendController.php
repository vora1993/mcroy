<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Session\Container as Session;

class FrontendController extends AbstractActionController
{
	public function indexAction()
	{
        $application_model_testimonial = $this->getServiceLocator()->get('application_model_testimonial');
	    $testimonials = $application_model_testimonial->fetchAll(array("status" => 1));

        $application_model_slider = $this->getServiceLocator()->get('application_model_slider');
        $sliders = $application_model_slider->fetchAll(array('status' => array(1,2,3), 'type' => 0));

        $application_model_post = $this->getServiceLocator()->get('application_model_post');
        $posts = $application_model_post->fetchAll(array('status' => 1), "post_date", "DESC", 0, 3);

        $application_model_widget = $this->getServiceLocator()->get('application_model_widget');
        $widget_1 = $application_model_widget->fetchRow(array("type" => "widget_1"));
        $widget_2 = $application_model_widget->fetchAll(array("type" => "widget_2"));
        $widget_3 = $application_model_widget->fetchAll(array("type" => "widget_3"));
        $widget_4 = $application_model_widget->fetchAll(array("type" => "widget_4"));

        return array("widget_1" => $widget_1, "widget_2" => $widget_2, "widget_3" => $widget_3, "widget_4" => $widget_4, "testimonials" => $testimonials, "sliders" => $sliders, "posts" => $posts);
    }
}