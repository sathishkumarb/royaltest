<?php
namespace Order\Model;
use Zend\Db\Sql\Select, Zend\Db\Sql\Where;
use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Crypt\BlockCipher;	#for encryption
use Zend\Db\Sql\Expression;
class OrderTable extends AbstractTableGateway
{
    protected $table = 'order'; 
    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Order());
        $this->initialize();
    }
	public function getOrders($limit,$offset,$id){
		$select = new Select;	
		$select->from('order')
				   ->columns(array("order_id","order_ref_id","order_user_id","order_date","order_status"))
				   ->where(array("order_status",1));
		$select->limit($limit);
		$select->offset($offset);
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->toArray();
	}

	public function getCountOfAllOrders($search=''){
        $select = new Select;
        $select->from('order')        
               ->columns(array(new Expression('COUNT(order.order_id) as order_count')));
        if($search!=''){
            $select->where->like('order.order_ref_id',$search.'%');      
        }
        $statement = $this->adapter->createStatement();
        $select->prepareStatement($this->adapter, $statement);
        $resultSet = new ResultSet();
        $resultSet->initialize($statement->execute());
        return  $resultSet->current()->order_count;
    }

	public function fetchAll(){
       $resultSet = $this->select();
       return $resultSet;
    }
	public function saveOrder(Order $order){
        $data = array(
            'order_title' => $order->order_ref_id,
            'order_date'  => date("Y-m-d H:i:s"),   
            'order_user_id'  => $order->order_user_id,
			'order_status'  => $order->order_status		
        );
        $order_id = (int)$order->order_id;
        if ($order_id == 0) {
            $this->insert($data);
			return $this->adapter->getDriver()->getConnection()->getLastGeneratedValue();
        } else {
            if ($this->getOrder($order_id)) {
                $this->update($data, array('order_id' => $order_id));
            } else {
                throw new \Exception('order id does not exist');
            }
        }
    }
	public function getOrder($order_id){
        $order_id  = (int) $order_id;
        $rowset = $this->select(array('order_id' => $order_id));
        return $rowset->current();        
    }
	public function deletetOrder($order_id){
        $this->delete(array('order_id' => $order_id));
    }
	public function getActiveOrders(){
		$select = new Select;
		$select->from('order')
				->where(array("order_status",1));
		$statement = $this->adapter->createStatement();
		//echo $select->getSqlString();exit;
		$select->prepareStatement($this->adapter, $statement);		 
		$resultSet = new ResultSet();
		$resultSet->initialize($statement->execute());	
		return $resultSet->toArray();
	}

}