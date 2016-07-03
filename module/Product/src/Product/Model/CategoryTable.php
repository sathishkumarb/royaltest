<?php
namespace Product\Model;
use Zend\Db\Sql\Select, Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;	#for encryption
use Zend\Db\Sql\Expression;
class CategoryTable extends AbstractTableGateway
{
    protected $table = 'category'; 
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Category());
        $this->initialize();
    }
	public function getCategories($limit,$offset,$id){
		$select = new Select;	
		$select->from('category')
				   ->columns(array("category_id","category_title"))
				   ->where(array("category_status",1));
		$select->limit($limit);
		$select->offset($offset);
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->toArray();
	}

	public function getCountOfAllCategories($search=''){
        $select = new Select;
        $select->from('category')        
               ->columns(array(new Expression('COUNT(category.category_id) as category_count')));
        if($search!=''){
            $select->where->like('category.category_title',$search.'%');      
        }
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return  $resultSet->current()->category_count;
    }

	public function fetchAll(){
       $resultSet = $this->select();
       return $resultSet;
    }
	public function saveCategory(Category $category){
        $data = array(
            'category_title' => $category->category_title,
            'category_icon'  => $category->category_icon,   
            'category_desc'  => $category->category_desc,
			'category_status'  => $category->category_status		
        );
        $category_id = (int)$category->category_id;
        if ($category_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getProductCategory($category_id)) {
                $this->update($data, array('category_id' => $category_id));
            } else {
                throw new \Exception('product category id does not exist');
            }
        }
    }
	public function getCategory($category_id){
        $category_id  = (int) $category_id;
        $rowset = $this->select(array('category_id' => $category_id));
        return $rowset->current();        
    }
	public function deleteProductCategory($category_id){
        $this->delete(array('category_id' => $category_id));
    }
	public function getActiveCategories(){
		$select = new Select;
		$select->from('category')
				->where(array("category_status",1));
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->toArray();
	}

}