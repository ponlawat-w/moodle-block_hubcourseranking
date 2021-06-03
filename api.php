<?php

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$blockid = required_param('id', PARAM_INT);
$page = optional_param('page', 1, PARAM_INT);

$hubcourseranking = block_instance_by_id($blockid);
if (!$hubcourseranking || !($hubcourseranking instanceof block_hubcourseranking)) {
  throw new moodle_exception('Block ID is not of hubcourseranking block type');
}

$results = block_hubcourseranking_gethubcourselist($hubcourseranking->config, $page);
$results->config = $hubcourseranking->config;
$results->records = array_values($results->records);
$results->more = (count($results->records) < $results->count);

echo json_encode($results);
