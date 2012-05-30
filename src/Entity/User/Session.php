<?php
namespace Module\Users\Session;
use Spot;

class Entity extends Spot\Entity
{
    // Table
    protected static $_datasource = "user_session";

    /**
     * Fields
     */
    public static function fields()
    {
        return array(
            'id' => array('type' => 'int', 'primary' => true, 'serial' => true),
            'user_id' => array('type' => 'int', 'key' => true, 'required' => true),
            'session_id' => array('type' => 'string', 'required' => true, 'key' => true),
            'date_created' => array('type' => 'datetime')
        ) + parent::fields();
    }

    /**
     * Relations
     */
    public static function relations()
    {
        return array(
            // User session/login
            'user' => array(
                'type' => 'HasOne', // Actually a 'BelongsTo', but that is currently not implemented in Spot
                'entity' => 'Module\Users\Entity',
                'where' => array('id' => ':entity.user_id')
            )
        ) + parent::relations();
    }
}
