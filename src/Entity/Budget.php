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
            'transactions' => array(
                'type' => 'HasMany',
                'entity' => 'Entity\Transaction',
                'where' => array('budget_id' => ':entity.id')
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

    public function balance()
    {
        $spent = 0;
        foreach($this->transactions as $txn) {
            $spent += $txn->amount;
        }
        return (float) $this->amount - $spent;
    }

    public function toArray()
    {
        return array_merge(parent::dataExcept(array('date_created', 'date_modified', 'transactions')), array(
            'balance' => $this->balance(),
            '_links' => array(
                'self' => array(
                    'rel' => 'budget',
                    'href' => app()->url('budgets/' . $this->id),
                    'method' => 'get'
                ),
                'delete' => array(
                    'title' => t('Delete'),
                    'href' => app()->url('budgets/' . $this->id),
                    'method' => 'delete'
                ),
                'transactions' => array(
                    'title' => t('Transactions'),
                    'href' => app()->url('budgets/' . $this->id . '/transactions'),
                    'method' => 'get'
                ),
                'add_transaction' => array(
                  'title' => t('Add Transaction'),
                  'href' => app()->url('budgets/' . $this->id . '/transactions'),
                  'method' => 'post',
                  'parameters' => Transaction::parameters()
                )
            )
        ));
    }
}
