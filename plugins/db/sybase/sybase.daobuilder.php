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
 * driver for jDaoCompiler
 */
class sybaseDaoBuilder extends jDaoGenerator {

    protected $propertiesListForInsert = 'PrimaryFieldsExcludeAutoIncrement';

}
