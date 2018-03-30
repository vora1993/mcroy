<?php
namespace Backend\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Zend\View\Model\JsonModel;

use Zend\Stdlib\ResponseInterface as Response;
use Zend\Db\Sql\Expression;

class SettingController extends AbstractActionController
{
	public function indexAction()
	{
    return $this->redirect()->toRoute('admin/setting', array('action' => 'view-company'));
  }

  public function viewCompanyAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    return array('settings' => $settings);
  }

  public function editCompanyAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
      $application_view_helper_folder = $viewHelperManager->get('folder');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        $company_array_key = array('company_name', 'owner_name', 'company_address', 'company_zip', 'company_country', 'company_state', 'company_currency', 'company_phone', 'company_fax', 'company_email', 'company_url', 'company_logo');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $company_array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

                // Logo
        $dir_setting = 'data/company';
        if($post['company_logo']) {
          $dir_logo = $dir_setting;
          if (!file_exists($dir_logo)) mkdir($dir_logo, 0777, true);

          $dir_tmp = $dir_setting.'/tmp/'.$post['company_logo'];
          $dir_new = $dir_logo.'/'.$post['company_logo'];
          if(file_exists($dir_tmp)) copy($dir_tmp, $dir_new);

          $application_view_helper_resizeimage($dir_logo, $post['company_logo']);
          $application_view_helper_folder("delete", $dir_setting.'/tmp');
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  public function editHomeAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
      $application_view_helper_folder = $viewHelperManager->get('folder');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        $company_array_key = array('homepage');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $company_array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  /**
   * Menu
   */
  public function menuAction() {
    $application_model_menu_group = $this->getServiceLocator()->get('application_model_menu_group');
    $group_id = $this->params()->fromRoute('id');
    if($group_id > 0) {
      $menu_group = $application_model_menu_group->fetchRow(array("id" => $group_id));
    } else {
      $menu_group = $application_model_menu_group->fetchRow(array("is_default" => 1));
    }

    $application_model_menu = $this->getServiceLocator()->get('application_model_menu');
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator  = $this->getServiceLocator()->get('translator');
      $source      = $this->params()->fromPost('source');
      $destination = $this->params()->fromPost('destination', 0);

      $menu = $application_model_menu->fetchRow(array('id' => $source));
      $menu->setId($source);
      $menu->setParent($destination);
      $application_model_menu->update($menu);

      $ordering       = \Zend\Json\Json::decode($this->params()->fromPost('order'));
      $rootOrdering   = \Zend\Json\Json::decode($this->params()->fromPost('rootOrder'));

      if($ordering) {
        foreach ($ordering as $order => $item_id) {
          $order = $order + 1;
          $itemToOrder = $application_model_menu->fetchRow(array('id' => $item_id));
          if($itemToOrder) {
            $itemToOrder->setId($item_id);
            $itemToOrder->setSortOrder($order);
            $application_model_menu->update($itemToOrder);
          }
        }
      } else {
        foreach($rootOrdering as $order => $item_id){
          $order = $order + 1;
          $itemToOrder = $application_model_menu->fetchRow(array('id' => $item_id));
          if($itemToOrder){
            $itemToOrder->setId($item_id);
            $itemToOrder->setSortOrder($order);
            $application_model_menu->update($itemToOrder);
          }
        }
      }
      return $response->setContent ( \Zend\Json\Json::encode ( array("success" => true, "msg" => $translator->translate("Successfully update")) ) );
    }
    $menus      = $application_model_menu->fetchAll(array("group_id" => $menu_group->getId()))->toArray();
    $menu_html  = $this->buildMenu($menus);

    $config = $this->getServiceLocator()->get('Config');
    $modules = $config['controllers']['invokables'];
    $routes = array();
    foreach ($modules as $key => $value) {
      $value = str_replace("\\", "/", $value);
      $controllerName = explode('/', $value)[0];
      $routerName = explode('/', $value)[2];
      $routerName = str_replace("Controller", "", $routerName);
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
          }
        }
      }
      $m = array_filter($m);
      $routes[$key] = $m;
    }

    return array("menu_group" => $menu_group, "menu_html" => $menu_html, "routes" => $routes);
  }

  public function loadActionAction() {
    $html = "";
    $request = $this->getRequest();
    if ($request->isPost()) {
      $post = $request->getPost();
      $route = $post['route'];

      $config = $this->getServiceLocator()->get('Config');
      $modules = $config['controllers']['invokables'];
      $m = array();
      foreach ($modules as $key => $value) {
        if($key === $route) {
          $value = str_replace("\\", "/", $value);
          $controllerName = explode('/', $value)[0];
          $routerName = explode('/', $value)[2];
          $routerName = str_replace("Controller", "", $routerName);
          $source = 'module/'.$controllerName.'/src/'.$value.'.php';
          $fh = fopen($source,'r');

          while ($line = fgets($fh)) {
            if(preg_match_all('/function(.*?)Action()/', $line, $matches)){
              $function = str_replace("(.*?)", "", $matches[1][0]);
              $function = preg_replace('/([A-Z])/', '-$1', $function);
              $action = trim(strtolower($function));
              if($action !== "") {
                $m[] = $action;
              }
            }
          }
          $m = array_filter($m);
        }
      }
      if(count($m > 0)) {
        foreach ($m as $k => $v) {
          $html .= '<option value="'.$v.'">'.$v.'</option>';
        }
      }
    }
    $response = $this->getResponse();
    $response->setContent ( \Zend\Json\Json::encode ( array("html" => $html) ) );
    return $response;
  }

  public function buildMenu($menus, $id_parent = 0) {
    $html = "";
    $translator = $this->getServiceLocator()->get('translator');
    $menu_tmp = array();
    $menus = $menus;
    foreach ($menus as $key => $item) {
      if ((int) $item['parent'] == (int) $id_parent) {
        $menu_tmp[] = $item;
        unset($menus[$key]);
      }
    }
    if ($menu_tmp)
    {
      $html .= '<ol class="dd-list" id="accordion">';
      foreach ($menu_tmp as $item)
      {
        $id = $item['id'];
        $label = $item['title'];
        $sort_order = $item['sort_order'];
        $html .= "<li class='dd-item nested-list-item panel' style='border: 0;' data-order='{$sort_order}' data-id='{$id}'>";
        $html .= "<div class='dd-handle nested-list-handle'><span class='glyphicon glyphicon-move'></span></div>";
        $html .= "<div class='nested-list-content'>{$label}<div class='pull-right'><a href='#collapse_{$id}' class='accordion-toggle accordion-toggle-styled collapsed' data-toggle='collapse' data-parent='#accordion'><i class='fa fa-edit'></i></a></div></div>";
        $html .= "<div id='collapse_{$id}' class='panel-collapse collapse'>";
        $html .= "<div class='panel-body'>";
        $html .= "<div class='row'><div class='col-md-12'><label class='control-label'>Navigation Label</label>";
        $html .= "<input type='text' class='form-control' name='label_title' value='{$label}'></div></div>";
        $html .= "<div class='row' style='margin-top: 15px;'><div class='col-md-6 text-left'><button type='button' class='btn red-thunderbird ladda-button' onclick='edit_menu(this, {$id})' data-style='expand-left'><i class='fa fa-check'></i> ".$translator->translate("Update")."</button></div><div class='col-md-6 text-right'><button type='button' class='btn purple ladda-button' onclick='remove_menu(this, {$id})' data-style='expand-left'><i class='fa fa-trash'></i> ".$translator->translate("Remove")."</button></div></div>";
        $html .= "</div></div>";
        $html .= $this->buildMenu($menus, $id);
        $html .= "</li>";
      }
      $html .= '</ol>';
    }

    return $html;
  }

  public function createMenuAction() {
    $messages = array();
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_menu_group = $this->getServiceLocator()->get('application_model_menu_group');

      $name = $this->params()->fromPost('name');
      $menu_group = $application_model_menu_group->fetchRow(array('name' => $name));
      if($menu_group) {
        $messages['success'] = false;
        $messages['msg'] = $translator->translate("The menu name $name conflicts with another menu name. Please try another");
      } else {
        $menu_group = new \Application\Entity\MenuGroup;
        $menu_group->setName($name);
        $menu_group->setIsDefault(0);
        $menu_group->setSortOrder(2);
        $menu_group->setDateModified(new Expression('NOW()'));
        $menu_group->setStatus(1);

        $added = $application_model_menu_group->insert($menu_group);
        if($added) {
          $messages['success'] = true;
          $messages['msg'] = $translator->translate("Successfully added");
        } else {
          $messages['success'] = false;
          $messages['msg'] = $translator->translate("Something error. Please check");
        }
      }
    }
    $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
    return $response;
  }

  public function addMenuAction() {
    $messages = array();
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_menu = $this->getServiceLocator()->get('application_model_menu');

      $group_id = $this->params()->fromPost('group');
      $title    = $this->params()->fromPost('title');
      $name     = $this->params()->fromPost('name');
      $action   = $this->params()->fromPost('action');
      $route    = $this->params()->fromPost('route');
      $value    = $this->params()->fromPost('value');

      $menu = new \Application\Entity\Menu;
      $menu->setTitle($title);
      $menu->setName($name);
      $menu->setGroupId($group_id);
      $menu->setRoute($route);
      $menu->setAction($action);
      $menu->setValue($value);
      $menu->setDateAdded(new Expression('NOW()'));
      $menu->setParent(0);

      $added = $application_model_menu->insert($menu);
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

  public function editMenuAction() {
    $messages = array();
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_menu = $this->getServiceLocator()->get('application_model_menu');

      $menu_id   = $this->params()->fromPost('menu_id');
      $menu_name = $this->params()->fromPost('menu_name');

      $menu = $application_model_menu->fetchRow(array('id' => $menu_id));
      if($menu) {
        $menu->setId($menu_id);
        $menu->setTitle($menu_name);
        $menu->setDateModified(new Expression('NOW()'));

        $updated = $application_model_menu->update($menu);
        if($updated) {
          $messages['success'] = true;
          $messages['msg'] = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg'] = $translator->translate("Something error. Please check");
        }
      } else {
        $messages['success'] = false;
      }
    }
    $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
    return $response;
  }

  public function removeMenuAction() {
    $messages = array();
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $application_model_menu = $this->getServiceLocator()->get('application_model_menu');

      $menu_id   = $this->params()->fromPost('menu_id');
      $items = $application_model_menu->fetchAll(array('parent' => $menu_id));
      foreach ($items as $item) {
        $menu = $application_model_menu->fetchRow(array('id' => $item->getId()));
        $menu->setId($item->getId());
        $menu->setParent(0);
        $menu->setDateModified(new Expression('NOW()'));
        $application_model_menu->update($menu);
      }

      $deleted = $application_model_menu->delete(array('id' => $menu_id));
      if($deleted) {
        $messages['success'] = true;
        $messages['msg'] = $translator->translate("Successfully removed");
      } else {
        $messages['success'] = false;
        $messages['msg'] = $translator->translate("Something error. Please check");
      }
    }
    $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
    return $response;
  }

  /**
   * Widget
   */
  public function widgetAction() {
    $application_model_widget = $this->getServiceLocator()->get('application_model_widget');
    $widget_1 = $application_model_widget->fetchRow(array("type" => "widget_1"));
    $widget_2 = $application_model_widget->fetchAll(array("type" => "widget_2"));
    $widget_3 = $application_model_widget->fetchAll(array("type" => "widget_3"));
    $widget_4 = $application_model_widget->fetchAll(array("type" => "widget_4"));

    $request = $this->getRequest();
    if ($request->isPost()) {
      $post = $request->getPost();

      $type = $post['type'];
      switch($type) {
        case 'widget_1':
        $widget_1->setId($widget_1->getId());
        $widget_1->setName($post['name']);
        $image = explode(",", $post['image']);
        $image = array_values(array_filter($image));
        $image_arr = array();
        $product_image = \Zend\Json\Json::decode ( $widget_1->getContent() );
        if(count($image) > 0) {
          foreach ($image as $k => $v) {
            $image_arr[] = $v;
          }
        }
        $widget_1->setContent(\Zend\Json\Json::encode ( $image_arr ));
        $updated = $application_model_widget->update($widget_1);
        break;

        case 'widget_2':
        $data = $post['widget_2'];
        foreach ($data as $id => $value) {
          $name = $value['name'];
          $content = $value['content'];
          $link = $value['link'];
          $label_link = $value['label_link'];
          $widget = $application_model_widget->fetchRow(array("id" => $id));
          $widget->setId($id);
          $widget->setName($name);
          $widget->setContent($content);
          $widget->setLink($link);
          $widget->setLabelLink($label_link);
          $updated = $application_model_widget->update($widget);
        }
        break;

        case 'widget_3':
        $data = $post['widget_3'];
        foreach ($data as $id => $value) {
          $name = $value['name'];
          $content = $value['content'];
          $link = $value['link'];
          $label_link = $value['label_link'];
          $widget = $application_model_widget->fetchRow(array("id" => $id));
          $widget->setId($id);
          $widget->setName($name);
          $widget->setContent($content);
          $widget->setLink($link);
          $widget->setLabelLink($label_link);
          $updated = $application_model_widget->update($widget);
        }
        break;

        case 'widget_4':
        $data = $post['widget_4'];
        foreach ($data as $id => $value) {
          $name = $value['name'];
          $content = $value['content'];
          $link = $value['link'];
          $label_link = $value['label_link'];
          $widget = $application_model_widget->fetchRow(array("id" => $id));
          $widget->setId($id);
          $widget->setName($name);
          $widget->setContent($content);
          $widget->setLink($link);
          $widget->setLabelLink($label_link);
          $updated = $application_model_widget->update($widget);
        }
        break;
      }
      return $this->redirect()->toRoute("admin/setting", array("action" => "widget"));
    }
    return array("widget_1" => $widget_1, "widget_2" => $widget_2, "widget_3" => $widget_3, "widget_4" => $widget_4);
  }

  public function widgetLoadImageAction() {
    $application_model_widget = $this->getServiceLocator()->get('application_model_widget');
    $widget_1 = $application_model_widget->fetchRow(array("type" => "widget_1"));

    $product_images = \Zend\Json\Json::decode($widget_1->getContent());

    $config = $this->getServiceLocator()->get('config');
    $path = $config['application']['base_path'] ? $config['application']['base_path'] : '/';

    $data = array();
    $response = $this->getResponse();
    if(count($product_images) > 0) {
      foreach ($product_images as $image) {
        if(file_exists('data/brand/'.$image)) {
          $file = $_SERVER["DOCUMENT_ROOT"].$path.'data/brand/'.$image;
        }
        $file_size = filesize($file);
        $data[] = array(
          'url'  => "/data/brand/".$image,
          'size' => $file_size,
          'name' => basename($image),
        );
      }
    }
    $response->setContent ( \Zend\Json\Json::encode ( $data ) );
    return $response;
  }

  public function widgetUploadImageAction() {
    $translator = $this->getServiceLocator()->get('translator');
    $messages = array();
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $file = $this->params()->fromFiles('file');
      $valid_formats = array("jpg", "jpeg", "png", "gif", "bmp");
      $name = $file['name'];
      if(strlen($name)) {
        $dir = 'data/brand';
        if (!file_exists($dir)) {
          mkdir($dir, 0777, true);
        }

        $ext = end(explode('.', $name));
        if(in_array($ext, $valid_formats)) {
          $newFilename = $name;
          $tmp = $file['tmp_name'];
          if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
            $messages = array(
              'success'  => true,
              'name'     => $newFilename,
              'src'      => '/data/brand/'.$newFilename,
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

  public function widgetDeleteImageAction() {
    $messages = array();
    $response = $this->getResponse();
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $name = $this->params()->fromPost('name');

      if($name) {
        $application_model_widget = $this->getServiceLocator()->get('application_model_widget');
        $product = $application_model_widget->fetchRow(array("type" => "widget_1"));
        $product_image = \Zend\Json\Json::decode ( $product->getContent() );
        if(($key = array_search($name, $product_image)) !== false) {
          unset($product_image[$key]);
        }
        $image = array();
        foreach ($product_image as $key => $value) {
          $image[] = $value;
        }

        $product->setId($product->getId());
        $product->setContent(\Zend\Json\Json::encode ( $image ));
        $application_model_widget->update($product);
      }
      $filename = $name;

      $dir_product = 'data/brand/'.$filename;
      if(file_exists($dir_product)) unlink($dir_product);

      $dir_thumb_product = 'data/brand/thumb/'.$filename;
      if(file_exists($dir_thumb_product)) unlink($dir_thumb_product);

      $messages['success'] = true;
    }
    $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
    return $response;
  }


  /**
   * Loan
   */
  public function editLoanAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        $array_key = array('max_loan_tenure', 'min_loan_amount', 'max_loan_amount', 'max_loan_compare', 'amt_business_loan', 'amt_property_loan', 'notify');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
          if($setting->getKey() === 'notify') {
            $repeater = $request->getPost('repeater');
            $notify = array();
            if(count($repeater) > 0) {
              foreach ($repeater as $r) {
                $notify[] = $r['notify'];
              }
            }
            $setting->setId($setting->getId());
            $setting->setValue(\Zend\Json\Json::encode($notify));
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  public function editEmailAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        $array_key = array('email_type', 'email_name', 'email_host', 'email_username', 'email_password', 'email_from', 'email_to', 'email_cc');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  /**
   * Change logo company
   */
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
        $dir = 'data/company/tmp';
        if (!file_exists($dir)) {
          mkdir($dir, 0777, true);
        }

        list($txt, $ext) = explode(".", $name);
        if(in_array($ext, $valid_formats)) {
          $newFilename = 'logo.' . $ext;
          $tmp = $file['tmp_name'];
          if(move_uploaded_file($tmp, $dir.'/'.$newFilename)) {
            $messages = array(
              'success'  => true,
              'name'     => $newFilename,
              'src'      => '/data/company/tmp/'.$newFilename,
              'msg'      => $translator->translate("Upload logo successful"),
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

  public function editSocialAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        $array_key = array('facebook_app_id', 'facebook_app_secret', 'facebook_link', 'twitter_link', 'linkedin_link', 'google_plus_link');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  public function editTooltipAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
      $application_view_helper_folder = $viewHelperManager->get('folder');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      $company_array_key = array('eligibility_sale_turn_over', 'eligibility_years_of_incorporation', 'min_requirement', 'monthly_instalments', 'total_interest_payable', 'processing_fee', 'penalty_fee', 'interest_property_loan', 'monthly_repayment', 'total_interest_payable', 'type_property_loan','min_turnover','min_years_incorporation','annual_fee','lock_in_period','max_tenure');

      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $company_array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings, 'array_key' => $company_array_key);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  public function editCommonInterestsAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
      $application_view_helper_folder = $viewHelperManager->get('folder');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      $company_array_key = array('sibor_1_month', 'sibor_3_months', 'sibor_6_months', 'sibor_1_year');

      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $company_array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings, 'array_key' => $company_array_key);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  public function editIncomeFactorAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $income_factor = $application_model_setting->fetchRow(array('key' => 'income_factor'));

    $request = $this->getRequest();
    if ($request->isPost() ) {
      $translator = $this->getServiceLocator()->get('translator');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if (!empty($income_factor)) {
        $id = $income_factor->getId();
      } else {
        $income_factor = new \Application\Entity\Setting;
        $income_factor->setKey('income_factor');
        $income_factor->setName('Income Factor');
      }

      $post = $request->getPost();
      $error = 0;
      if(!$error) {
        if (!empty($id)) {
          $income_factor->setId($id);
        }

        $_income_factor = $post['edit-income-factor'];
        if(count($_income_factor) > 0) {
          $income_factor_arr = array();
          foreach ($_income_factor as $key => $value) {
            $income_factor_arr[] = array(
              'industry'  => $value['industry'],
              'income_factor' => $value['income_factor']
            );
          }
          $income_factor->setValue(\Zend\Json\Json::encode($income_factor_arr));
        }
        if (!empty($id)){
          $edited = $application_model_setting->update($income_factor);
        } else {
          $edited = $application_model_setting->insert($income_factor);
        }

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

    return array('income_factor' => $income_factor);
  }

  public function editIncomeFactorAddonAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;

        $now = new Expression('NOW()');
        $array_key = array('base_average_noa_compare', 'base_net_profit_compare', 'base_annual_depreciation_compare', 'base_interest_expense_compare');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue(\Zend\Json\Json::encode($post[$setting->getKey()]));
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }

  function clearHtml($html) {
    $html = preg_replace("/<([a-z][a-z0-9]*)[^>]*?(\/?)>/i",'<$1$2>', $html);
    $html = preg_replace("/<div>(.*?)<\/div>/", "$1", $html);
    return $html;
  }

  public function setStatusAction() {
    $request = $this->getRequest();
    if ($request->isPost()) {
      $translator = $this->getServiceLocator()->get('translator');
      $settingMapper = $this->getServiceLocator()->get('setting_mapper');

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
        $setting = $settingMapper->fetchRow(array('id' => $id));
        $setting->setId($id);
        $setting->setStatus($status);
        $now = new Expression('NOW()');
        $setting->setDateModified($now);

        $updated = $settingMapper->update($setting);
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

  public function viewSupportInfoAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    return array('settings' => $settings);
  }

  public function editSupportInfoAction() {
    $application_model_setting = $this->getServiceLocator()->get('application_model_setting');
    $settings = $application_model_setting->fetchAll();
    if($settings) {
      $translator = $this->getServiceLocator()->get('translator');
      $viewHelperManager = $this->getServiceLocator()->get('ViewHelperManager');
      $application_view_helper_resizeimage = $viewHelperManager->get('resize_image');
      $application_view_helper_folder = $viewHelperManager->get('folder');

      $request = $this->getRequest();
      $response = $this->getResponse();
      $messages = array();
      if ($request->isPost()) {
        $post = $request->getPost();
        $error = 0;
        $now = new Expression('NOW()');
        $supporter_array_key = array('supporter_name', 'supporter_email', 'supporter_phone', 'supporter_image');
        foreach ($settings as $setting) {
          if(in_array($setting->getKey(), $supporter_array_key)) {
            $setting->setId($setting->getId());
            $setting->setValue($post[$setting->getKey()]);
            $setting->setDateModified($now);
            $edited = $application_model_setting->update($setting);
            if(!$edited) $error = $error + 1;
          }
        }

        // Logo
        $dir_setting = 'data/user';
        if($post['supporter_image']) {
          $dir_image = $dir_setting;
          if (!file_exists($dir_image)) mkdir($dir_image, 0777, true);

          $dir_tmp = $dir_setting.'/tmp/'.$post['supporter_image'];
          $dir_new = $dir_image.'/'.$post['supporter_image'];
          if(file_exists($dir_tmp)) copy($dir_tmp, $dir_new);

          $application_view_helper_resizeimage($dir_image, $post['supporter_image']);
          $application_view_helper_folder("delete", $dir_setting.'/tmp');
        }

        if(!$error) {
          $messages['success'] = true;
          $messages['msg']     = $translator->translate("Successfully updated");
        } else {
          $messages['success'] = false;
          $messages['msg']     = $translator->translate("Something error. Please check");
        }

        $response->setContent ( \Zend\Json\Json::encode ( $messages ) );
        return $response;
      }

      return array('settings' => $settings);
    } else {
      return $this->redirect()->toRoute("setting");
    }
  }
}