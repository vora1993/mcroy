<?php
namespace Frontend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\Db\Sql\Expression;
use Zend\Session\Container as Session;

// import Download file
use Zend\Http\Headers;
use Zend\Http\Response\Stream;

class BlogController extends AbstractActionController
{
	public function indexAction()
	{
        $application_model_post = $this->getServiceLocator()->get('application_model_post');
        $total_posts = $application_model_post->fetchAll(array("status" => 1));
        $rec_count = count($total_posts);  
        
        $url = $this->getServiceLocator()->get('viewhelpermanager')->get('url');
        $paged = $this->params()->fromRoute('paged') ? $this->params()->fromRoute('paged') : 1;
        $offset = 0;
        $rec_limit = 10;
        $max = ceil($rec_count / $rec_limit);
        
        if($paged > 1) $offset = $rec_limit * ($paged - 1);
        
        if($paged >= 1) {
            $links[] = $paged;
        } 
        if ( $paged >= 3 ) {
            $links[] = $paged - 1;
            $links[] = $paged - 2;
        }
        if (($paged + 2 ) <= $max ) {
    		$links[] = $paged + 2;
    		$links[] = $paged + 1;
    	}
        
        $pagination = '<ul class="pagination pull-right">';
        $pagination .= '<li><a href="'.$url('blog', array('action' => 'index', 'paged' => 1)).'"><i class="fa fa-long-arrow-left"></i></a></li>';
        
        if(!in_array(1, $links)) {
            $class = 1 == $paged ? true : false;
            if($class) {
                $pagination .= '<li><span>1</span></li>';
            } else {
                $link_page = $url('blog', array('action' => 'index', 'paged' => 1));
                $pagination .= '<li><a href="'.$link_page.'">1</a></li>';
            }
        
            if ( ! in_array( 2, $links ) ) $pagination .= '<li>...</li>';
        }
        
        sort( $links );
        foreach ( (array) $links as $link ) {
    		$class = $paged == $link ? true : false;
            if($class) {
                $pagination .= '<li><span>'.$link.'</span></li>';
            } else {
                $link_page = $url('blog', array('action' => 'index', 'paged' => $link));
  		        $pagination .= '<li'.$class.'><a href="'.$link_page.'">'.$link.'</a></li>';
            }
    	}
        
        if ( ! in_array( $max, $links ) ) {
    		if ( ! in_array( $max - 1, $links ) )
    			$pagination .= '<li>...</li>' . "\n";
    
    		$class = $paged == $max ? true : false;
            if($class) {
                $pagination .= '<li><span>'.$max.'</span></li>';
            } else {
                $link_page = $url('blog', array('action' => 'index', 'paged' => $max));
                $pagination .= '<li'.$class.'><a href="'.$link_page.'">'.$max.'</a></li>';
            }
    	}
        
        $pagination .= '<li><a href="'.$url('blog', array('action' => 'index', 'paged' => $max)).'"><i class="fa fa-long-arrow-right"></i></a></li>';
        $pagination .= '</ul>';
        
        // Get featured posts
        $featured_posts = $application_model_post->fetchAll(array("featured" => 1), "date_added", "DESC", 0, 3);
        
        // Get popular posts
        $populars = $application_model_post->fetchAll(array("status" => 1), "hits", "DESC", 0, 5);
        
        // Infographic
        $application_model_infographic = $this->getServiceLocator()->get('application_model_infographic');
        $infographics = $application_model_infographic->fetchAll(array("status" => 1));
        
        $posts = $application_model_post->fetchAll(array("status" => 1), "post_date", "DESC", $offset, $rec_limit);
        return array("posts" => $posts, "count" => $rec_count, "pagination" => $pagination, "featured_posts" => $featured_posts, "populars" => $populars, "infographics" => $infographics);
	}
    
    public function viewAction() {
        $translator = $this->getServiceLocator()->get('translator');
        $id = $this->params()->fromRoute('id');
        $application_model_post = $this->getServiceLocator()->get('application_model_post');
        $post = $application_model_post->fetchRow(array("id" => $id));
        
        $current_hits = $post->getHits() ? $post->getHits() : 0;
        // Update hits
        $post->setId($id);
        $post->setHits($current_hits + 1);
        $application_model_post->update($post);
        
        return array("post" => $post);
    }
    
    public function downloadAttachmentAction() {
        $id = $this->params()->fromRoute('id');
        $application_model_infographic = $this->getServiceLocator()->get('application_model_infographic');
        $infographic = $application_model_infographic->fetchRow(array('id' => $id));
        
        if($infographic->getPdf() !== null) {
            $file = 'data/pdf/'.$infographic->getPdf();
            $response = new Stream();
            $response->setStream(fopen($file, 'r'));
            $response->setStatusCode(200);
            $response->setStreamName(basename($file));
            $headers = new Headers();
            $headers->addHeaders(array(
                'Content-Disposition' => 'attachment; filename="' . basename($file) .'"',
                'Content-Type' => 'application/octet-stream',
                'Content-Length' => filesize($file)
            ));
            $response->setHeaders($headers);
            return $response;
        }
    }
}