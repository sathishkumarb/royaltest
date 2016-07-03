<?php
namespace Application\Controller;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;
use Zend\View\Model\ViewModel;
use Zend\Authentication\AuthenticationService;
use Zend\Authentication\Adapter\DbTable as AuthAdapter;
use Zend\Session\Container;  
class IndexController extends AbstractActionController
{	
	protected $productTable;
	protected $productAttributesTable;
	protected $userTable;
	protected $categoryTable;

	public function indexAction(){ 
		$auth = new AuthenticationService();
		$serviceManager = $this->getServiceLocator();
		if ($auth->hasIdentity()) {
			$identity = $auth->getIdentity();
			//return $this->redirect()->toRoute('memberprofile', array('member_profile' => $identity->user_profile_name));
		}else{
			$config = $this->getServiceLocator()->get('Config');
			$products = $this->getProductTable()->generalProductList(6,0);
			$product_general_list = array();
			foreach($products as $list){
				$product_values = array(
								'product_id'=>$list['product_id'],
								'product_name'=>$list['product_name'],
								'product_description'=>$list['product_description'],
								);

				$product_general_list[] = array('product_id'=> $list['product_id'],
											'productlist'=>$product_values,
											'productcategory'=>$this->getProductCategoryTable()->getGroupCategories(5,0,$list['product_id']),
										);	 
			}
			
			$result = new ViewModel(array(
			'products' => $product_general_list		
			return $result; 
		}
    }
	

	public function getProductTable(){
		$sm = $this->getServiceLocator();
		return  $this->productTable = (!$this->productTable)?$sm->get('Product\Model\ProductTable'):$this->productTable;  
    }
	public function getProductAttribTatesble(){
		$sm = $this->getServiceLocator();
		return  $this->productAttributesTable = (!$this->productAttributesTable)?$sm->get('Product\Model\ProductAttributesTable'):$this->productAttributesTable;  
    }

	public function getUserTable(){
		$sm = $this->getServiceLocator();
		return  $this->userTable = (!$this->userTable)?$sm->get('User\Model\UserTable'):$this->userTable;  
	}

	public function getCategoryTable(){
		$sm = $this->getServiceLocator();
		return  $this->categoryTable = (!$this->categoryTable)?$sm->get('Category\Model\CategoryTable'):$this->categoryTable;  
	}		

}

