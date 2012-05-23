<?php
/**
* @package    jelix
* @subpackage db
* @author     Xavier Martin-Prével
* @copyright  2010 Xavier Martin-Prével
* @link     http://www.jelix.org
* @licence     http://www.gnu.org/licenses/lgpl.html GNU Lesser General Public Licence, see LICENCE file
*/

/**
 *
 * @package    jelix
 * @subpackage db_driver
 */
class sybaseDbTable extends jDbTable {
    protected function _loadColumns() {

        $this->columns = array ();
        $this->primaryKey = false;
        $conn = $this->schema->getConn();
        $tools = $conn->tools();

        // get primary keys informations
        $pkeys = array();
        $conn->exec("create table #cols (colName varchar(30) null)");
        for ($i = 1; $i <= 16; $i++) {
            $conn->exec("insert into #cols select isnull(index_col('".$this->name."',indid,".$i."),'')"
                ." from sysindexes where id = object_id('".$this->name."') and indid != 0 and indid != 255 and status2&2 = 2");
        }
        $rs = $conn->query ("select colName from #cols where isnull(colName,'')!='' drop table #cols ");
        while ($line = $rs->fetch()){
            $pkeys[] = $line->colName;
        }
        unset($line);

        // get columns informations
        $rs = $conn->query ("select c.name as colName, t.name as typeName, c.length,"
        	." (case when c.status&128=128 then 1 else 0 end) as autoinc,"
        	." (case when c.status&8=0 then 1 else 0 end) as notNull,"
        	." (case when upper(substring(co.text,1,7))='DEFAULT' then substring(co.text,9,char_length(co.text)-8) else '' end) as defaultText"
        	." from syscolumns c, systypes t, syscomments co"
        	." where id = object_id('".$this->name."') and t.usertype = c.usertype"
        	." and co.id=*c.cdefault and co.colid=1 and co.colid2=0"
        	." order by c.colid");

        while ($line = $rs->fetch ()) {

            $type = $line->typeName;
            $length = 0;
            if ($type == 'varchar' || $type == 'char')
                $length = $line->length;
            $notNull = ($line->notNull == 1);
            $autoIncrement  = ($line->autoinc == 1);
            $default = ltrim($line->defaultText);
            $hasDefault = ($default != '');

            $col = new jDbColumn($line->colName, $type, $length, $hasDefault, $default, $notNull);
            $col->autoIncrement = $autoIncrement;

            $typeinfo = $tools->getTypeInfo($type);
            $col->maxValue = $typeinfo[3];
            $col->minValue = $typeinfo[2];
            $col->maxLength = 0;
            if ($col->length !=0)
                $col->maxLength = $col->length;
            $col->minLength = $typeinfo[4];

            if(in_array($line->colName, $pkeys)){
                if (!$this->primaryKey)
                    $this->primaryKey = new jDbPrimaryKey($line->colName);
                else
                    $this->primaryKey->columns[] = $line->colName;
            }

            $this->columns[$line->colName] = $col;
        }
    }

    protected function _alterColumn(jDbColumn $old, jDbColumn $new) {
        $conn = $this->schema->getConn();

        if ($new->name != $old->name) {
            $sql = 'execute sp_rename "'.$this->name.'.'.$old->name.'",'.$new->name;
            $conn->exec($sql);
        }

	$sql = 'ALTER TABLE '.$this->name.' MODIFY '
            .$this->schema->_prepareSqlColumn($new);
        $conn->exec($sql);
    }

    protected function _addColumn(jDbColumn $new) {
        $conn = $this->schema->getConn();

        $sql = 'ALTER TABLE '.$this->name.' ADD '
            .$this->schema->_prepareSqlColumn($new);
        $conn->exec($sql);
    }

    protected function _loadIndexesAndKeys() {

        $conn = $this->schema->getConn();
        $this->uniqueKeys = $this->indexes = array();
        $this->primaryKey = false;

        // primary key
        $conn->exec("create table #cols (colNum tinyint null, colName varchar(30) null)");
        for ($i = 1; $i <= 16; $i++) {
            $conn->exec("insert into #cols select ".$i.", isnull(index_col('".$this->name."',indid,".$i."),'')"
                ." from sysindexes where id = object_id('".$this->name."') and indid != 0 and indid != 255 and status2&2 = 2");
        }
        $rs = $conn->query ("select colName from #cols where isnull(colName,'')!='' order by colNum drop table #cols ");
        $i = 0;
        while ($line = $rs->fetch()){
            if (!$this->primaryKey)
                $this->primaryKey = new jDbPrimaryKey($line->colName);
            else {
                $this->primaryKey->columns[$i] = $line->colName;
                $i++;
            }
        }

        // other indexes
        $conn->exec("create table #cols (indName varchar(30) null, indUnique tinyint null, colNum tinyint null, colName varchar(30) null)");
         for ($i = 1; $i <= 16; $i++) {
            $conn->exec("insert into #cols select name, (case when status&2=2 then 1 else 0),"
                .$i.", isnull(index_col('".$this->name."',indid,".$i."),'')"
                ." from sysindexes where id = object_id('".$this->name."') and indid != 0 and indid != 255 and status&16 != 16");
        }
        $rs = $conn->query ("select indName, indUnique, colName from #cols where isnull(colName,'')!=''"
                ." order by indName, colNum drop table #cols ");
        while ($line = $rs->fetch()){
            if ($line->indUnique == 1) {
                if(!isset($this->uniqueKeys[$line->indName])) {
                    $this->uniqueKeys[$line->indName] = new jDbUniqueKey($line->indName);
                    $i = 0;
                }
                $this->uniqueKeys[$line->indName]->columns[$i] = $line->colName;
            }
            else {
                if(!isset($this->indexes[$line->indName])) {
                    $this->indexes[$line->indName] = new jDbIndex($line->indName, '');
                    $i = 0;
                }
                $this->indexes[$line->indName]->columns[$i] = $line->colName;
            }
            $i++;
        }
    }

    protected function _createIndex(jDbIndex $index) {

        $conn = $this->schema->getConn();

        if ($index instanceof jDbPrimaryKey) {
            $sql = 'ALTER TABLE '.$this->name.' ADD CONSTRAINT '
                .$index->name.' PRIMARY KEY ('.implode(',', $index->columns).')';
        }

        else {
            $sql = 'CREATE '.$index instanceof jDbUniqueKey ? 'UNIQUE' : ''.' INDEX '
                .$index->name.' ON '.$this->name.'('.implode(',', $index->columns).')';
        }

        $conn->exec($sql);
    }

    protected function _dropIndex(jDbIndex $index) {

        $conn = $this->schema->getConn();

        if ($index instanceof jDbPrimaryKey) {
            $sql = 'ALTER TABLE '.$this->name.' DROP CONSTRAINT '.$index->name;
        }
        else {
            $sql = 'DROP INDEX '.$this->name.'.'.$index->name;
        }

        $conn->exec($sql);
    }

    protected function _loadReferences() {
        $conn = $this->schema->getConn();
        $sql = "create table #cols (keyName varchar(30) null, fTableName varchar(30) null,"
            ." colNum tinyint null, colName varchar(30) null, fColName varchar(30) null)";
        $conn->exec($sql);
        for ($i = 1; $i <= 16; $i++) {
            $sql = "insert into #cols select  o.name, object_name(r.reftabid), ".$i.", c1.name, c2.name"
                ." from sysobjects o, sysreferences r, syscolumns c1, syscolumns c2"
                ." where o.type = 'RI' and r.constrid = o.id and r.tableid = object_id('".$this->name."')"
                ." and c1.id = r.tableid and c1.colid = r.fokey".$i." and c2.id = r.reftabid and c2.colid = r.refkey".$i;
            $conn->exec($sql);
        }
        $sql = "select keyName, fTableName, colName, fColName from #cols "
            ."where isnull(colName,'')!='' order by keyName, colNum drop table #cols ";
        $rs = $conn->query ($sql);

        $ref = new jDbReference();
        $cols = array();
        $fcols = array();

        while ($line = $rs->fetch()) {
            if ($line->keyName != $ref->name && $ref->name != '') {
                $ref->columns = $cols;
                $ref->fcolumns = $fcols;
                $this->references[$ref->name] = $ref;

                $ref = new jDbReference();
                $cols = array();
                $fcols = array();
            }

            if ($ref->name == '') {
                $ref->name = $line->keyName;
                $ref->fTable = $line->fTableName;
                $ref->onDelete = "RESTRICT";
                $ref->onUpdate = "RESTRICT";
            }

            $cols[] = $line->colName;
            $fcols[] = $line->fColName;
        }
        if ($ref->name != '') {
            $ref->columns = $cols;
            $ref->fcolumns = $fcols;
            $this->references[$ref->name] = $ref;
        }       
    }

    protected function _createReference(jDbReference $ref) {
        $conn = $this->schema->getConn();

        $sql = 'ALTER TABLE '.$this->name.' ADD CONSTRAINT '.$ref->name. ' FOREIGN KEY (';

        $cols = array();
        foreach ($ref->columns as $c) {
            $cols[] = $c;
        }
        $sql .= impode(',', $cols).') REFERENCES '.$ref->fTable.'(';

	$fcols = array();
        foreach ($ref->fColumns as $c) {
            $fcols[] = $c;
        }
        $sql .= implode(',', $fcols).')';

        $conn->exec($sql);
    }

    protected function _dropReference(jDbReference $ref) {
        $conn = $this->schema->getConn();
        $sql = 'ALTER TABLE '.$this->name.' DROP CONSTRAINT '.$ref->name;
        $conn->exec($sql);
    }

}

/**
 *
 * @package    jelix
 * @subpackage db_driver
 */
class sybaseDbSchema extends jDbSchema {
    protected function _createTable($name, $columns, $primaryKey, $attributes = array()) {

        $cols = array();
        $withIdentity = false;

        if (is_string($primaryKey))
            $primaryKey = array($primaryKey);

        foreach ($columns as $col) {
            if ($col->autoIncrement)
                $withIdentity = true;
            $cols[] = $this->_prepareSqlColumn($col);
        }

        $sql = 'CREATE TABLE '.$name.' ('.implode(", ",$cols);
        if (count($primaryKey))
            $sql .= ', CONSTRAINT '.$name.'_primary PRIMARY KEY ('.implode(',', $primaryKey).')';
        $sql .= ')';
        if ($withIdentity)
            $sql .= ' WITH IDENTITY_GAP = 1';

        $this->conn->exec($sql);
        $table = new sybaseDbTable($name, $this);
        return $table;
    }

    protected function _getTables() {
        $results = array ();

        $rs = $this->conn->query ('SELECT name FROM sysobjects WHERE type = "U" ORDER BY name');

        while ($line = $rs->fetch ()){
            $results[$line->name] = new sybaseDbTable($line->name, $this);
        }

        return $results;
    }

    function _prepareSqlColumn($col) {
        $this->normalizeColumn($col);
        $colstr = $col->name.' '.$col->nativeType;

        if ($col->length) {
            $colstr .= '('.$col->length.')';
        }

        if ($col->hasDefault && !$col->autoIncrement) {
            if (!($col->notNull && $col->defaultValue === null)) {
                if ($col->defaultValue === null)
                    $colstr .= ' DEFAULT NULL';
                else
                    $colstr .= ' DEFAULT '.$col->defaultValue;
            }
        }

        if ($col->autoIncrement)
            $colstr .= ' IDENTITY';
        
        $colstr.= ($col->notNull?' NOT NULL':' NULL');

        return $colstr;
    }
}
