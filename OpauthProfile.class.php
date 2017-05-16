<?php
/**
 * Class declaration for mediawiki extension OpauthProfile
 *
 * @file OpauthProfile.class.php
 * @ingroup OpauthProfile
 */

/**
 * Class OpauthProfile
 * @property string name
 * @property string email
 * @property string nickname
 * @property string first_name
 * @property string last_name
 * @property string location
 * @property string description
 * @property string image
 * @property string phone
 * @property string url
 * @property string provider
 * @property string uid
 * @property string interests
 */
class OpauthProfile {

    private $user_id = null;
    private static $table = 'opauth_user_profile';
    private $data = array();

    public static function exists( $user_id )
    {
        $dbr = wfGetDB(DB_SLAVE);
        $ret = $dbr->selectRow(
            self::$table,
            'user_id',
            array(
                'user_id' => $user_id
            )
        );
        if( $ret ) {
            return true;
        }
        return false;
    }

    /**
     * OpauthProfile constructor.
     * @param $user_id
     */
    function __construct( $user_id )
    {
        $this->user_id = $user_id;
        $this->load();
    }

    public function __set( $name, $value )
    {
        $this->data[ $name ] = $value;
    }

    public function __get( $name )
    {
        if( array_key_exists($name, $this->data) ) {
            return $this->data[ $name ];
        }
        return null;
    }

    /**
     * Load profile fields from database
     */
    function load()
    {

        $dbr = wfGetDB(DB_SLAVE);

        $ret = $dbr->select(
            self::$table,
            '*',
            array(
                'user_id' => $this->user_id
            )
        );

        if( $ret && $ret->numRows() ) {
            $this->data = $ret->fetchRow();
	        foreach ($this->data as $key => $value) {
		        if (is_int($key)) {
			        unset($this->data[$key]);
		        }
	        }
        }

    }

    function save()
    {

        $dbw = wfGetDB(DB_MASTER);

        $check = $dbw->selectRow(
            self::$table, '*', array( 'user_id' => $this->user_id )
        );

        if( $check ) {
            // Update existing record
            $dbw->update(
                self::$table,
                $this->data,
                array(
                    'user_id' => $this->user_id
                )
            );
        }else{
            $dbw->insert(
                self::$table,
                array_merge(
                    array(
                        'user_id' => $this->user_id
                    ),
                    $this->data
                )
            );
        }

        $dbw->commit();

    }

}