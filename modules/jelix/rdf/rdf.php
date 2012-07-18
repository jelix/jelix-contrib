<?php
// example of an entry point for RDF

require ('../application.init.php');

require (jApp::getModulePath('rdf').'request/jRdfRequest.class.php');

checkAppOpened();

jApp::loadConfig('rdf/config.ini.php');

jApp::setCoord(new jCoordinator());
jApp::coord()->process(new jRdfRequest());


