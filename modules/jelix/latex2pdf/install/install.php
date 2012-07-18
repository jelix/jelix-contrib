<?php
/**
* @package     jelix
* @subpackage  latex2pdf module
* @author      Laurent Jouanneau
* @copyright   2012 Laurent Jouanneau
* @link        http://www.jelix.org
* @licence     GNU Lesser General Public Licence see LICENCE file or http://www.gnu.org/licenses/lgpl.html
*/

class latex2pdfInstaller extends jInstallerModule {

    function install() {
        $config = $this->config->getMaster();
        $config->setValue('ltx2pdf', "latex2pdf~jResponseLatexToPdf", "responses");
    }
}