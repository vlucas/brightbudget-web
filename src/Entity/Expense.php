<?php
namespace Entity;
use Spot;

class Expense extends Spot\Entity
{
    protected static $_datasource = 'expenses';

    public static function fields()
    {
        return array(
            'id' => array('type' => 'int', 'primary' => true, 'serial' => true),
            'budget_id' => array('type' => 'int', 'index' => true),
            'name' => array('type' => 'string', 'required' => true),
            'amount' => array('type' => 'float', 'required' => true, 'length' => '10,2'),

            'date_created' => array('type' => 'datetime', 'default' => new \DateTime()),
        );
    }

    public function toArray()
    {
        return array_merge(parent::dataExcept(array('expenses')), array(
            '_links' => array(
                'self' => array(
                    'href' => app()->request()->url() . '/budgets/' . $this->id,
                    'method' => 'get'
                ),
                'expenses' => array(
                    'href' => '/budgets/' . $this->id . '/expenses',
                    'method' => 'get'
                )
            )
        ));
    }
}
