<?php

require_once 'app/Monitors/WebMonitorService.php';

$data = array();
$data['service'] = "OMG SERVICE";

$webService = new WebMonitorService($data);
$webService->execute();

