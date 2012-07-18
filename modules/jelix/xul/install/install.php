<?php
/**
* @package     jelix
* @subpackage  xul module
* @author      Laurent Jouanneau
* @copyright   2012 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

class xulInstaller extends jInstallerModule {

    function install() {
        $config = $this->config->getMaster();
        $config->setValue('xul', "xul~jResponseXul", "responses");
        $config->setValue('xul', "xuloverlay~jResponseXulOverlay", "responses");
        $config->setValue('xul', "xuldialog~jResponseXulDialog", "responses");
        $config->setValue('xul', "xulpage~jResponseXulPage", "responses");
    }
}