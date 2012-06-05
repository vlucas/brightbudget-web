<?php
namespace Entity;
use Spot;

class Transaction extends Spot\Entity
{
    protected static $_datasource = 'transactions';

    public static function fields()
    {
        return array(
            'id' => array('type' => 'int', 'primary' => true, 'serial' => true),
            'budget_id' => array('type' => 'int', 'index' => true, 'required' => true),
            'name' => array('type' => 'string', 'required' => true),
            'amount' => array('type' => 'float', 'required' => true, 'length' => '10,2'),

            'date_created' => array('type' => 'datetime', 'default' => new \DateTime()),
        );
    }

    public static function relations()
    {
        return array(
            'budget' => array(
                'type' => 'HasOne',
                'entity' => 'Entity\Budget',
                'where' => array('id' => ':entity.budget_id')
            )
        );
    }

    /**
     * Return only field info that we want exposed in API 'parameters'
     */
    public static function parameters()
    {
        $fields = array('name', 'amount');
        return array_intersect_key(self::fields(), array_flip($fields));
    }

    public function toArray()
    {
        return array_merge(parent::dataExcept(array('budget')), array(
            '_links' => array(
                'self' => array(
                    'rel' => 'transaction',
                    'href' => app()->url('/budgets/' . $this->budget->id . '/transactions/' . $this->id),
                    'method' => 'get'
                )
            )
        ));
    }
}
