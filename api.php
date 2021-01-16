<?php

define('AJAX_SCRIPT', true);
require_once(__DIR__ . '/../../config.php');
require_once(__DIR__ . '/lib.php');

$blockid = required_param('id', PARAM_INT);
$fullmode = optional_param('full', 0, PARAM_INT) ? true : false;

$hubcourseranking = block_instance_by_id($blockid);
if (!$hubcourseranking || !($hubcourseranking instanceof block_hubcourseranking)) {
  throw new moodle_exception('Block ID is not of hubcourseranking block type');
}

$hubcourses = array_values(
  block_hubcourseranking_gethubcourselist($hubcourseranking->config, $fullmode)
);

$result = new stdClass();
$result->config = new stdClass();
$result->config = $hubcourseranking->config;
$result->records = $hubcourses;

echo json_encode($result);
