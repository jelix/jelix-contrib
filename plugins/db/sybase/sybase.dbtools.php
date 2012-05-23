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
class sybaseDbTools extends jDbTools {

    protected $typesInfo = array(
      // type                  native type        unified type  minvalue     maxvalue   minlength  maxlength
      'bool'		=>array('bit',              'boolean',  0,		1,          null,	null),
      'boolean'         =>array('bit',              'boolean',  0,           	1,          null,     	null),
      'bit'             =>array('bit',              'integer',  0,           	1,          null,     	null),
      'tinyint'         =>array('smallint',         'integer',  -128,        	127,        null,     	null),
      'smallint'        =>array('smallint',         'integer',  -32768,      	32767,      null,     	null),
      'mediumint'       =>array('integer',          'integer',  -8388608,    	8388607,    null,     	null),
      'integer'         =>array('integer',          'integer',  -2147483648, 	2147483647, null,     	null),
      'int'             =>array('int',              'integer',  -2147483648, 	2147483647, null,     	null),
      'intn'            =>array('intn',             'integer',  -2147483648, 	2147483647, null,     	null),
      'bigint'          =>array('numeric',          'numeric',  '-9223372036854775808', '9223372036854775807', null, null),
      'serial'          =>array('numeric',          'numeric',  '-9223372036854775808', '9223372036854775807', null, null),
      'bigserial'       =>array('numeric',          'numeric',  '-9223372036854775808', '9223372036854775807', null, null),
      'autoincrement'   =>array('numeric',          'integer',  -2147483648, 	2147483647, null,     	null), // for old dao files
      'bigautoincrement'=>array('numeric',          'numeric',  '-9223372036854775808', '9223372036854775807', null, null),// for old dao files

      'float'           =>array('float',            'float',    null,       	null,       null,     	null), //4bytes
      'floatn'          =>array('floatn',           'float',    null,       	null,       null,     	null), //4bytes
      'money'           =>array('money',            'float',    null,       	null,       null,     	null), //4bytes
      'smallmoney'      =>array('smallmoney',       'float',    null,       	null,       null,     	null), //4bytes
      'moneyn'          =>array('moneyn',           'float',    null,       	null,       null,     	null), //4bytes
      'double precision'=>array('double precision', 'decimal',  null,       	null,       null,     	null), //8bytes
      'double'          =>array('double precision', 'decimal',  null,       	null,       null,     	null), //8bytes
      'real'            =>array('real',             'decimal',  null,       	null,       null,     	null), //8bytes
      'number'          =>array('numeric',          'decimal',  null,       	null,       null,     	null), //8bytes
      'binary_float'    =>array('float',            'float',    null,       	null,       null,     	null), //4bytes
      'binary_double'   =>array('double precision', 'decimal',  null,       	null,       null,     	null), //8bytes
      
      'numeric'         =>array('numeric',          'numeric',  null,       	null,       null,     	null),
      'numericn'        =>array('numericn',         'numeric',  null,       	null,       null,     	null),
      'decimal'         =>array('decimal',          'decimal',  null,       	null,       null,     	null),
      'decimaln'        =>array('decimaln',         'decimal',  null,       	null,       null,     	null),
      'dec'             =>array('decimal',          'decimal',	null,       	null,       null,     	null),

      'date'            =>array('date',       		'date',		null,       	null,       10,    		10),
      'daten'           =>array('daten',       		'date',		null,       	null,       10,    		10),
      'time'            =>array('time',       		'time',		null,       	null,       8,     		8),
      'timen'           =>array('timen',       		'time',		null,       	null,       8,     		8),
      'datetime'        =>array('datetime',   		'datetime',	null,       	null,       19,    		19),
      'smalldatetime'   =>array('smalldatetime',   	'datetime',	null,       	null,       19,    		19),
      'datetimn'        =>array('datetimn',   		'datetime',	null,       	null,       19,    		19),
      'timestamp'       =>array('timestamp',  		'datetime',	null,       	null,       19,    		19), // oracle/pgsql timestamp
      'utimestamp'      =>array('timestamp',  		'integer',	0,          	2147483647, null,  		null), // mysql timestamp
      'year'            =>array('integer',    		'year',		null,       	null,       2,     		4),
      'interval'        =>array('datetime',   		'datetime',	null,       	null,       19,    		19),

      'char'            =>array('char',       		'char',		null,       	null,       0,     		255),
      'nchar'           =>array('nchar',      		'char',		null,       	null,       0,     		255),
      'varchar'         =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'nvarchar'        =>array('nvarchar',    		'varchar',	null,       	null,       0,     		65535),
      'varchar2'        =>array('varchar',    		'varchar',	null,       	null,       0,     		4000),
      'nvarchar2'       =>array('nvarchar',   		'varchar',	null,       	null,       0,     		4000),
      'character'       =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'character varying'=>array('varchar',   		'varchar',	null,       	null,       0,    		65535),
      'name'            =>array('varchar',    		'varchar',	null,       	null,       0,     		64),
      'longvarchar'     =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'string'          =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),// for old dao files

      'tinytext'        =>array('varbinary',  		'text',		null,       	null,       0,     		255),
      'text'            =>array('text',  	  		'text',		null,       	null,       0,     		65535),
      'mediumtext'      =>array('text', 	  		'text',		null,       	null,       0,     		16777215),
      'longtext'        =>array('text',   	  		'text',		null,       	null,       0,     		0),
      'long'            =>array('text',   	  		'text',		null,       	null,       0,     		0),
      'clob'            =>array('text',   	  		'text',		null,       	null,       0,     		0),
      'nclob'           =>array('text',   	  		'text',		null,       	null,       0,     		0),


      'tinyblob'        =>array('varbinary',  		'varbinary',null,       	null,       0,     		255),
      'image'           =>array('image',      		'varbinary',null,      	 	null,       0,     		65535),
      'blob'            =>array('image',      		'varbinary',null,      	 	null,       0,     		65535),
      'mediumblob'      =>array('image', 	  		'varbinary',null,       	null,       0,     		16777215),
      'longblob'        =>array('image',   	  		'varbinary',null,       	null,       0,     		0),
      'bfile'           =>array('image',   	  		'varbinary',null,       	null,       0,     		0),
      
      'bytea'           =>array('image',   	  		'varbinary',null,       	null,       0,     		0),
      'binary'          =>array('binary',     		'binary',	null,       	null,       0,     		255),
      'varbinary'       =>array('varbinary',  		'varbinary',null,       	null,       0,     		255),
      'raw'             =>array('varbinary',  		'varbinary',null,       	null,       0,     		2000),
      'long raw'        =>array('image',  	  		'varbinary',null,       	null,       0,     		0),

      'enum'            =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'set'             =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'xmltype'         =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),

      'point'           =>array('varchar',    		'varchar',	null,       	null,       0,     		16),
      'line'            =>array('varchar',    		'varchar',	null,       	null,       0,     		32),
      'lsed'            =>array('varchar',    		'varchar',	null,       	null,       0,     		32),
      'box'             =>array('varchar',    		'varchar',	null,       	null,       0,     		32),
      'path'            =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'polygon'         =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'circle'          =>array('varchar',    		'varchar',	null,       	null,       0,     		24),
      'cidr'            =>array('varchar',    		'varchar',	null,       	null,       0,     		24),
      'inet'            =>array('varchar',    		'varchar',	null,       	null,       0,     		24),
      'macaddr'         =>array('integer',    		'integer',	0,          	0xFFFFFFFFFFFF, null,	null),
      'bit varying'     =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'arrays'          =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
      'complex types'   =>array('varchar',    		'varchar',	null,       	null,       0,     		65535),
    );

    /**
     * 	List of tables
     * @return   array    $tab[] = $nomDeTable
     */
    function getTableList (){
        $results = array ();
        $sql = "SELECT name as tableName FROM " .$this->_conn->profile['database']. "..sysobjects WHERE type = 'U'";
        $rs = $this->_conn->query ($sql);
        while ($line = $rs->fetch ()){
            $results[] = $line->tableName;
        }
        return $results;
    }

    /**
     * return a field list of a table.
     * @return   array    $tab[NomDuChamp] = obj avec prop (type, length, lengthVar, notnull)
     */
    function getFieldList ($tableName){
        $results = array ();

        // get primary keys informations
        $pkeys = array();
        $this->_conn->exec("create table #cols (colName varchar(30) null)");
        for ($i = 1; $i <= 16; $i++) {
            $this->_conn->exec("insert into #cols select isnull(index_col('".$tableName."',indid,".$i."),'')"
                ." from sysindexes where id = object_id('".$tableName."') and indid != 0 and indid != 255 and status2&2 = 2");
        }
        $rs = $this->_conn->query("select colName from #cols where isnull(colName,'')!='' drop table #cols");
        while ($line = $rs->fetch()){
            $pkeys[] = $line->colName;
        }
        
        // get table informations
        unset($line);
        $sql = "select c.name as colName, t.name as typeName, c.length,"
        	." (case when c.status&128=128 then 1 else 0 end) as autoinc,"
        	." (case when c.status&8=0 then 1 else 0 end) as notNull,"
        	." (case when upper(substring(co.text,1,7))='DEFAULT' then substring(co.text,9,char_length(co.text)-8) else '' end) as defaultText"
        	." from syscolumns c, systypes t, syscomments co"
        	." where c.id = object_id('".$tableName."') and t.usertype = c.usertype"
        	." and co.id=*c.cdefault and co.colid=1 and co.colid2=0"
        	." order by c.colid";
        $rs = $this->_conn->query ($sql);
        while ($line = $rs->fetch ()){
            $field = new jDbFieldProperties();
            
            $field->name = $line->colName;
            $field->type = $line->typeName;
            $typeinfo = $this->getTypeInfo($field->type);
            $field->unifiedType = $typeinfo[1];
            $field->maxValue = $typeinfo[3];
            $field->minValue = $typeinfo[2];
//            $fiels->length = 0;
            $field->maxLength = 0;
            if ($field->type == 'char' || $field->type == 'varchar') {
                $field->length = $line->length;
                $field->maxLength = $line->length;
            }
            $field->minLength = $typeinfo[4];
            $field->notNull = false;
            if ($line->autoinc == 1){
                $field->autoIncrement = true;
                $field->notNull = true;
            }
            if ($line->notNull == 1){
                $field->notNull = true;
            }            
            $field->hasDefault = false;
            $field->default = '';
            if (strlen(ltrim($line->defaultText)) > 0){
                $field->hasDefault = true;
                $field->default = $line->defaultText;
            }
            if(in_array($field->name, $pkeys)){
                $field->primary = true;
            }
            
            $results[$line->colName] = $field;
        }
        return $results;
    }

    public function execSQLScript ($file) {
        $lines = file($file);
        $cmdSQL = '';
        $nbCmd = 0;
        $search = array(chr(9), chr(10), chr(13), chr(32));

        foreach ((array)$lines as $key=>$line) {
        	if ((substr($line,0,3) != "go".chr(13)) && (substr($line,0,3) != "go".chr(10))) {
                $cmdSQL.=$line;
            }
			elseif (strlen(str_replace($search, '', $cmdSQL)) > 0) {
				$this->_conn->query ($cmdSQL);
				$nbCmd++;
				$cmdSQL = '';
			}
			else {
				$cmdSQL = '';
			}
        }
        return $nbCmd;
   }
}
?>