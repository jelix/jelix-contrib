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
 * Layer encapsulation resultset sybase.
 * @experimental
 */
class sybaseDbResultSet extends jDbResultSet {

    protected function  _fetch (){
        if($this->_fetchMode == jDbConnection::FETCH_CLASS) {
            if ($this->_fetchModeCtoArgs)
        		throw new jException('jelix~db.error.feature.unsupported', array('sybase','_fetchModeCtoArgs'));
            else
                $ret =  sybase_fetch_object ($this->_idResult, $this->_fetchModeParam);
        }else{
            $ret =  sybase_fetch_object ($this->_idResult);
        }
        return $ret;
    }

    protected function _free (){
        return sybase_free_result ($this->_idResult);
    }

    protected function _rewind (){
        return @sybase_data_seek ( $this->_idResult, 0);
    }

    public function rowCount(){
        return sybase_num_rows($this->_idResult);
    }

    public function bindColumn($column, &$param , $type=null ) {
        throw new jException('jelix~db.error.feature.unsupported', array('sybase','bindColumn'));
    }

    public function bindParam($parameter, &$variable , $data_type =null, $length=null,  $driver_options=null) {
        throw new jException('jelix~db.error.feature.unsupported', array('sybase','bindParam'));
    }

    public function bindValue($parameter, $value, $data_type) {
        throw new jException('jelix~db.error.feature.unsupported', array('sybase','bindValue'));
    }

    public function columnCount() {
        return sybase_num_fields($this->_idResult);
    }

    public function execute($parameters=null) {
        throw new jException('jelix~db.error.feature.unsupported', array('sybase','execute'));
    }
    
    public function fetch_array(){
        return sybase_fetch_array($this->_idResult);
    }
    
}

