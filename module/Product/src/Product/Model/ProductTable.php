<?php
namespace Product\Model;
 
use Zend\Db\Sql\Select ;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\TableGateway\Feature\RowGatewayFeature;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Expression;
class ProductTable extends AbstractTableGateway
{ 
    protected $table = 'product';  
    public function __construct(Adapter $adapter){
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Product());
        $this->initialize();
    }

	public function saveProductDetails(Product $objProduct, $intProductId) {
         $data = array(
            'product_name'           => addslashes($objProduct->productName),
            'product_color'      => addslashes($objProduct->productColor),
			'product_brand'      => addslashes($objProduct->productBrand),
			'product_status'         => 1,
			'product_description'     => addslashes($objProduct->strDesp),
		);
        if($intProductId != ''){
			$this->update($data, array('product_id' => $intProductId));
			return $intProductId;
        }else {
			$this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        }
    }

	public function checkProductExist($seotitle){
		$select = new Select;
		$select->from('y2m_product')
			   ->columns(array('product_id'))
			   );
		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);	
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		$row = $resultSet->current();
		if(!empty($row)&&$row->product_id!=''){
			return true;
		}else{
			return false;
		}
	}
	
	public function updateProduct($data,$product_id){
		 $this->update($data, array('product_id' => $product_id));
		return true;
	}

	public function getCountOfAllProduct(){
		$select = new select();
		$select->from('y2m_product')
				 ->columns(array(new Expression('COUNT(y2m_product.product_id) as total_product')));

		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);

		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->current()->total_product;
	}
	public function getLatestProduct($limit = null, $offset = null){
		$select = new select();
		$select->from('y2m_product')
				 ->columns(array('product_id'=>'product_id', 'product_title'=>'product_title'))
				 ->order('product_added_timestamp desc')
				 ->offset($offset)                    
				 ->limit($limit);

		$statement = $this->adapter->createStatement();
		$select->prepareStatement($this->adapter, $statement);

		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());
		return  $resultSet->toArray();
	}
}