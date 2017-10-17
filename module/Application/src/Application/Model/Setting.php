<?php
namespace Application\Model;

use ZfcBase\Mapper\AbstractDbMapper;
use Zend\Stdlib\Hydrator\HydratorInterface;

use Zend\Db\Sql\Predicate\Operator;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Where;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Predicate\Expression;

class Setting extends AbstractDbMapper
{
    protected $tableName  = 'setting';
    
    public function fetchAll($condition=null)
    {
        $select = $this->getSelect();
        if($condition) $select->where($condition);

        $entity = $this->select($select);
        $this->getEventManager()->trigger('find', $this, array('entity' => $entity));
        return $entity;
    }

    public function fetchRow($condition=null)
    {
        $select = $this->getSelect();
        if($condition) $select->where($condition);

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
}
