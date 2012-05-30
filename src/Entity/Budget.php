<?php
namespace Entity;
use Spot;

class Budget extends Spot\Entity
{
    protected static $_datasource = 'budgets';

    public static function fields()
    {
        return array(
            'id' => array('type' => 'int', 'primary' => true, 'serial' => true),
            'name' => array('type' => 'string', 'required' => true),
            'amount' => array('type' => 'float', 'required' => true, 'length' => '10,2'),

            'date_created' => array('type' => 'datetime', 'default' => new \DateTime()),
            'date_modified' => array('type' => 'datetime', 'default' => new \DateTime())
        );
    }

    public static function relations()
    {
        return array(
            'expenses' => array(
                'type' => 'HasMany',
                'entity' => 'Entity\Expense',
                'where' => array('budget_id' => ':entity.id')
            )
        );
    }

    public function toArray()
    {
        return array_merge(parent::dataExcept(array('expenses')), array(
            '_links' => array(
                'self' => array(
                    'href' => 'budgets/' . $this->id,
                    'method' => 'get'
                ),
                'expenses' => array(
                    'href' => 'budgets/' . $this->id . '/expenses',
                    'method' => 'get'
                )
            )
        ));
    }
}
