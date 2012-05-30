<?php
namespace Module\Users;
use Spot;

class Entity extends Spot\Entity
{
    // Table
    protected static $_datasource = "users";

    /**
     * Fields
     */
    public static function fields()
    {
        return array(
            'id' => array('type' => 'int', 'primary' => true, 'serial' => true),
            'name' => array('type' => 'string', 'required' => true),
            'email' => array('type' => 'email', 'required' => true, 'unique' => true),
            'password' => array('type' => 'string', 'required' => true),
            'salt' => array('type' => 'string', 'length' => 42, 'required' => true),
            'api_enabled' => array('type' => 'boolean', 'default' => false),
            'api_key' => array('type' => 'string', 'length' => 32),
            'is_admin' => array('type' => 'boolean', 'default' => false),
            'last_active' => array('type' => 'timestamp'),
            'date_created' => array('type' => 'datetime', 'default' => new \DateTime()),
            'date_modified' => array('type' => 'datetime', 'default' => new \DateTime())
        ) + parent::fields();
    }


    /**
     * Relations
     */
    public static function relations()
    {
        return array(
            // User session/login
            'session' => array(
                'type' => 'HasOne',
                'entity' => 'Module\Users\Session\Entity',
                'where' => array('user_id' => ':entity.id'),
                'order' => array('date_created' => 'DESC')
            )
        ) + parent::relations();
    }


    /**
     * Save with salt and encrypted password
     */
    public function beforeSave(Spot\Mapper $mapper)
    {
        $data = $mapper->data($this);

        // If password has been modified or set for the first time
        if(isset($this->_dataModified['password']) && ($this->_data['password'] != $this->_dataModified['password'])) {
            $this->__set('salt', $this->randomSalt());
            $this->__set('password', $this->encryptedPassword($this->_dataModified['password']));
        }

        // Generate API key
        if($this->api_enabled && !$this->api_key) {
            $this->__set('api_key', $this->generateApiKey());
        }

        //parent::beforeSave($mapper);
    }


    /**
     * Is user logged-in?
     *
     * @return boolean
     */
    public function isLoggedIn()
    {
        return $this->__get('id') ? true : false;
    }


    /**
     * Is user admin? (Has all rights)
     *
     * @return boolean
     */
    public function isAdmin()
    {
        return (boolean) $this->__get('is_admin');
    }


    /**
     * Return existing salt or generate new random salt if not set
     */
    public function randomSalt($length = 42)
    {
        $string = "";
        $possible = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789`~!@#$%^&*()[]{}<>-_+=|\/;:,.";
        $possibleLen = strlen($possible);

        for($i=0;$i < $length;$i++) {
            $char = $possible[mt_rand(0, $possibleLen-1)];
            $string .= $char;
        }

        return $string;
    }


    /**
     * Generate a new API key for the current user
     */
    public function generateApiKey()
    {
        // MD5 Ensures alphanumeric 32-character length
        return md5($this->randomSalt() . time() . $this->id . $this->email);
    }


    /**
     * Encrypt password
     *
     * @param string $pass Password needing encryption
     * @return string Encrypted password with salt
     */
    public function encryptedPassword($pass)
    {
        // Hash = <salt>:<password>
        return hash('sha256', $this->__get('salt') . ':' . $pass);
    }


    /**
     *  Get Gravatar image URL for given email address
     */
    public function gravatarUrl($size = null)
    {
        $hash = md5 ( strtolower ( trim( $this->email ) ) );
        return "http://www.gravatar.com/avatar/" . $hash . "?r=pg&d=mm" . (null === $size ? '' : '&s=' . $size);
    }


    /**
     * Convert object data to Array representation
     */
    public function toArray()
    {
        return array_diff_key(
            parent::toArray(),
            array_flip(array('password', 'salt', 'session', 'rooms', 'api_enabled', 'api_key'))
        ) + array(
            'gravatar_url' => $this->gravatarUrl()
        );
    }
}
