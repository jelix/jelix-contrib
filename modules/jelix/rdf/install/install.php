<?php
/**
* @package     jelix
* @subpackage  rdf module
* @author      Laurent Jouanneau
* @copyright   2012 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

class rdfInstaller extends jInstallerModule {

    function install() {
        $config = $this->config->getMaster();
        $config->setValue('rdf', "rdf~jResponseRdf", "responses");
        $config->setValue('rdf', "@rdf", "simple_urlengine_entrypoints");
        $config->setValue('rdf', "on", "basic_significant_urlengine_entrypoints");
    }
}