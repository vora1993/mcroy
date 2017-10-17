<?php

namespace Application\Model;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface;

use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Expression;

class Post extends AbstractDbMapper
{
    protected $tableName  = 'posts';
    
    public function fetchRow($condition=null)
    {
        $select = $this->getSelect();
        if($condition) $select->where($condition);
        $select->order('sort_order ASC');
        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function fetchCurrent($condition=null) {
        $select = $this->getSelect();
        
        if($condition) $select->where($condition);
        
        $select->order('id DESC');
        
        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function fetchCurrentDate($date=null) {
        $select = $this->getSelect();
        
        if($date) $select->where(new Expression('DATE(date_added) = ?', $date));
        $select->order('id DESC');
        
        $entity = $this->select($select)->current();
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function findNotMySelf($condition=null, $id=null) {
        $select = $this->getSelect();
        if($id) {
            $where = new Where;
            $where->notEqualTo('id', $id);
            $select->where($where);
        }
        
        if($condition) $select->where($condition); 
        
        $select->order('id DESC');
        
        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function fetchAll($condition=null, $order_column=null, $order_dir=null, $offset=null, $limit=null) {
        $select = $this->getSelect();
        if($condition) $select->where($condition);
        if($order_column) {
            $order = $order_column.' '.$order_dir;
            $select->order($order);
        } else {
            $select->order('post_date DESC');
        } 
        if($offset) $select->offset($offset);
        if($limit) $select->limit($limit); 
        
        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function fetchDate($condition=null, $month=null, $year=null, $order_column=null, $order_dir=null, $offset=null, $limit=null) {
        $select = $this->getSelect();
        if($condition) $select->where($condition);
        if($month) $select->where(new Expression('MONTH(post_date) = ?', $month));
        if($year) $select->where(new Expression('YEAR(post_date) = ?', $year));
        if($order_column) {
            $order = $order_column.' '.$order_dir;
            $select->order($order);
        } else {
            $select->order('post_date DESC');
        } 
        if($offset) $select->offset($offset);
        if($limit) $select->limit($limit); 
        
        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function search($condition=null, $keyword=null)
    { 
        $select = $this->getSelect();
        $where = new Where;
        if($keyword) {
            $where->nest
                  ->like('name', '%'.$keyword.'%')
                  ->unnest;
            $select->where($where);
        }
        if($condition) $select->where($condition);
        
        $select->group('id');
        $select->order('id ASC');
        
        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }
    
    public function getTableName()
    {
        return $this->tableName;
    }

    public function setTableName($tableName)
    {
        $this->tableName=$tableName;
    }

    public function insert($entity, $tableName = null, HydratorInterface $hydrator = null)
    {
        $result = parent::insert($entity, $tableName, $hydrator);
        $entity->setId($result->getGeneratedValue());
        return $result;
    }

    public function update($entity, $where = null, $tableName = null, HydratorInterface $hydrator = null)
    {
        if (!$where) {
            $where = array('id' => $entity->getId());
        }
        return parent::update($entity, $where, $tableName, $hydrator);
    }
    
    public function delete($where, $tableName = null)
    {
        return parent::delete($where);
    }
}
