<?php
/**
 * @package    jelix
 * @subpackage db_driver
 * @author     Xavier Martin-Prével
 * @copyright  2010 Xavier Martin-Prével
 * @link     http://www.jelix.org
 * @licence  http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
 */

/**
 * @experimental
 */
class sybaseDbConnection extends jDbConnection {
	/**
	 * array of sybase charset names
	 */
    protected $_charsets =array( 'ASCII 8'=>'ascii_8', 'Big 5'=>'big5', 'CP 437'=>'cp437',
    	'CP 850'=>'cp850', 'CP 852'=>'cp852', 'CP 855'=>'cp855', 'CP 857'=>'cp857', 'CP 860'=>'cp860',
    	'CP 864'=>'cp864', 'CP 866'=>'cp866', 'CP 869'=>'cp869', 'CP 874'=>'cp874', 'CP 932'=>'cp932',
    	'CP 950'=>'cp950', 'CP 1250'=>'cp1250', 'CP 1251'=>'cp1251', 'CP 1252'=>'cp1252', 'CP 1253'=>'cp1253',
    	'CP 1254'=>'cp1254', 'CP 1255'=>'cp1255', 'CP 1256'=>'cp1256', 'CP 1257'=>'cp1257', 'CP 1258'=>'cp1258',
    	'DEC Kanji'=>'deckanji', 'EUC-CNS'=>'euccns', 'EUC-GB'=>'eucgb', 'EUC-JIS'=>'eucjis', 'EUC-KSC'=>'eucksc',
    	'GREEK8'=>'greek8', 'ISO 8859-1'=>'iso_1', 'ISO 8859-2'=>'iso88592', 'ISO 8859-5'=>'iso88595',
    	'ISO 8859-6'=>'iso88596', 'ISO 8859-7'=>'iso88597', 'ISO 8859-8'=>'iso88598', 'ISO 8859-9'=>'iso88599',
    	'ISO 8859-15'=>'iso885915', 'Koi8'=>'koi8', 'Macintosh Cyrillic'=>'mac_cyr',
    	'Macintosh Central European'=>'mac_ee', 'Macintosh Greek'=>'macgrk2', 'Macintosh Roman'=>'mac',
    	'Macintosh Turkish'=>'macturk', 'ROMAN8'=>'roman8', 'Shift-JIS'=>'sjis', 'TIS 620'=>'tis620',
    	'TURKISH8'=>'turkish8', 'UTF-8'=>'utf8');
    
    /**
     * sybase message handler
     */
    public $_errno = 0, $_severity = 0, $_state = 0, $_line = 0, $_error = "";
    
    public function sybaseMessage ($errno, $severity, $state, $line, $error) {
        $this->_errno = $errno;
    	$this->_severity = $severity;
    	$this->_state = $state;
    	$this->_line = $line;
    	$this->_error = $error;
    }

    /**
     * Default constructor
     * @param $profile profile de connexion
     * @return unknown_type
     */
    function __construct($profile){
        if(!function_exists('sybase_connect')){
            throw new jException('jelix~db.error.nofunction','sybase');
        }
        parent::__construct($profile);
    }

    /**
     * begin a transaction
     */
    public function beginTransaction (){
        $this->_doExec ('SET chained OFF');
        $this->_doExec ('BEGIN TRANSACTION');
    }

    /**
     * Commit since the last begin
     */
    public function commit (){
        $this->_doExec ('COMMIT TRANSACTION');
        $this->_doExec ('SET chained ON');
    }

    /**
     * Rollback since the last BEGIN
     */
    public function rollback (){
        $this->_doExec ('ROLLBACK TRANSACTION');
        $this->_doExec ('SET chained ON');
    }

    /**
     *
     */
    public function prepare ($query){
        throw new jException('jelix~db.error.feature.unsupported', array('sybase','prepare'));
    }

    public function errorInfo(){
        return array($this->_errno, $this->_error);
    }

    public function errorCode(){
        return $this->_errno;
    }
     
    /**
     * (non-PHPdoc)
     * initialize the connection to the database
     * @see lib/jelix/db/jDbConnection#_connect()
     */
    protected function _connect (){
        $funcconnect = ($this->profile['persistent']? 'sybase_pconnect':'sybase_connect');
        $cnx = @$funcconnect ($this->profile['host'], $this->profile['user'], $this->profile['password']);
        if($cnx){

            sybase_set_message_handler (array('sybaseDbConnection','sybaseMessage'), $cnx);
        	
            if(isset($this->profile['force_encoding']) && $this->profile['force_encoding'] == true
            && isset($this->_charsets[$GLOBALS['gJConfig']->charset])){
            	$sql = "SET char_convert ".$this->_charsets[$GLOBALS['gJConfig']->charset];
                sybase_query ($sql, $cnx);
            }
            
            return $cnx;
        }else{
            throw new jException('jelix~db.error.connection',array($this->profile['host'],sybase_get_last_message()));
        }
    }

    /**
     * (non-PHPdoc)
     * 	close the connection to the database
     * @see lib/jelix/db/jDbConnection#_disconnect()
     */
    protected function _disconnect (){
        return sybase_close ($this->_connection);
    }

    /**
     * (non-PHPdoc)
     * 	execute an SQL instruction
     * @see lib/jelix/db/jDbConnection#_doQuery()
     */
    protected function _doQuery ($query){
        if(!sybase_select_db ($this->profile['database'], $this->_connection)){
            $message = sybase_get_last_message();
            if(strlen($message) > 0){
                throw new jException('jelix~db.error.database.unknown',array($this->profile['database'],$message));
            } else {
                throw new jException('jelix~db.error.connection.closed',$this->profile['name']);
            }
        }

        if ($qI = sybase_query ($query, $this->_connection)){
            return new sybaseDbResultSet ($qI);
        } else{
            throw new jException('jelix~db.error.query.bad',  sybase_get_last_message());
        }
    }
     
    /**
     * (non-PHPdoc)
     * @see lib/jelix/db/jDbConnection#_doExec()
     */
    protected function _doExec($query){
        if(!sybase_select_db ($this->profile['database'], $this->_connection))
            throw new jException('jelix~db.error.database.unknown',array($this->profile['database'],$message));

        if ($qI = sybase_query ($query, $this->_connection)){
            return sybase_affected_rows($this->_connection);
        }else{
            throw new jException('jelix~db.error.query.bad', sybase_get_last_message());
        }
    }
    /**
     * (non-PHPdoc)
     * @see lib/jelix/db/jDbConnection#_doLimitQuery()
     */
    protected function _doLimitQuery ($queryString, $offset, $number){
        if($number < 0)
            $number=0;
    	sybase_query ("SET rowcount ".$number, $this->_connection);
        $result = $this->_doQuery($queryString);
    	sybase_query ("SET rowcount 0", $this->_connection);
        return $result;
    }

    /**
     * (non-PHPdoc)
     * 	return the last inserted ID incremented in database
     * @see lib/jelix/db/jDbConnection#lastInsertId()
     */
    public function lastInsertId($fromSequence=''){
        $queryString = 'SELECT @@identity AS id';
        $resultSet = $this->_doQuery($queryString);
        $result = $resultSet->fetch();
        return $result->id;
    }

    /**
     * tell sybase to be implicit commit or not
     * @param boolean state the state of the autocommit value
     * @return void
     */
    protected function _autoCommitNotify ($state){
    	$this->_doExec ('SET chained '.$state ? 'ON' : 'OFF');
    }

    /**
     * escape special characters
     * @todo support of binary strings
     */
    protected function _quote($text, $binary){
        return str_replace( "'", "''", $text );
    }
}

?>