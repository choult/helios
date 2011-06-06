<?php
require_once dirname( __FILE__ ) . '/../../lib/vendor/SolrPhpClient/Apache/Solr/Service.php';

$server = 'orchid';
$port   = 8983;
$path   = '/solr';

$service = new Apache_Solr_Service( $server, $port, $path );

$service->deleteByQuery( '*:*' );


$document = new Apache_Solr_Document();

$document->id = 10001;
$document->_type = 'foo';
$document->field1_s = 'one';
$document->field2_i = 2;

$service->addDocument( $document );


$document->id = 10002;
$document->_type = 'foo';
$document->field1_s = 'two';
$document->field2_i = 3;

$service->addDocument( $document );


$service->commit( );

