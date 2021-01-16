<?php

defined('MOODLE_INTERNAL') || die();

const BLOCK_HUBCOURSERANKING_RANKBY_RECENT = 'recent';
const BLOCK_HUBCOURSERANKING_RANKBY_DOWNLOAD = 'download';
const BLOCK_HUBCOURSERANKING_RANKBY_REVIEWS = 'reviews';
const BLOCK_HUBCOURSERANKING_RANKBY_RATED = 'rated';

const BLOCK_HUBCOURSERANKING_DURATION_MONTH = 'month';
const BLOCK_HUBCOURSERANKING_DURATION_YEAR = 'year';

function block_hubcourseranking_getcourses() {
  global $DB;
  return $DB->get_records_sql(
    'SELECT hc.*, c.fullname FROM {block_hubcourses} hc JOIN {course} c '
    . 'ON hc.courseid = c.id ORDER BY c.fullname'
  );
}

function block_hubcourseranking_getshowoptions() {
  $a = [];
  for ($i = 3; $i <= 20; $i++) {
    $a[$i] = $i;
  }
  return $a;
}

function block_hubcourseranking_getmanualhubcourses($courseidsstr, $limit) {
  global $DB;
  $courseids = explode(';', $courseidsstr);
  $courseids = array_slice($courseids, 0, $limit);

  $cases = [];
  $idbindings = [];
  $params = [];
  for ($i = 0; $i < count($courseids); $i++) {
    $cases[] = 'WHEN ? THEN ' . $i;
    $idbindings[] = '?';
    $params[] = $courseids[$i];
  }
  $paramstr = implode(', ', $idbindings);
  $casestr = implode(' ', $cases);

  return $DB->get_records_sql(
    'SELECT hc.*, c.fullname, (CASE hc.courseid ' . $casestr . ' END) ranking '
    . 'FROM {block_hubcourses} hc JOIN {course} c ON hc.courseid = c.id '
    . 'WHERE c.id IN (' . $paramstr . ') ORDER BY RANKING'
  , array_merge($params, $courseids));
}

function block_hubcourseranking_gethubcourselist($config, $full = false) {
  global $DB;

  if (!$config || !$config->maxshow || !$config->minshow
    || ($config->ismanual && !$config->manual)
    || (!$config->ismanual && (!$config->by || !$config->duration))
  ) {
    return null;
  }

  $limit = $full ? $config->maxshow : $config->minshow;

  if ($config->ismanual) {
    return block_hubcourseranking_getmanualhubcourses($config->manual, $limit);
  }

  $time = time();
  switch ($config->duration) {
    case BLOCK_HUBCOURSERANKING_DURATION_MONTH: $time -= 2592000; break;
    case BLOCK_HUBCOURSERANKING_DURATION_YEAR: $time -= 31536000; break;
  }

  if ($config->by == BLOCK_HUBCOURSERANKING_RANKBY_RECENT) {
    $records = $DB->get_records_sql(
      'SELECT hc.*, c.fullname FROM {block_hubcourses} hc JOIN {course} c ON hc.courseid = c.id '
      . 'WHERE hc.timecreated > ? ORDER BY hc.timecreated DESC LIMIT ?'
    , [$time, $limit]);
    foreach ($records as $record) {
      $record->timecreatedstr = userdate($record->timecreated, get_string('strftimedate'));
    }
    return $records;
  }

  if ($config->by == BLOCK_HUBCOURSERANKING_RANKBY_DOWNLOAD) {
    return $DB->get_records_sql(
      'SELECT hc.*, c.fullname, ('
      . 'SELECT COUNT(*) FROM {block_hubcourse_downloads} hd WHERE hd.versionid IN'
      . ' (SELECT id FROM {block_hubcourse_versions} hv WHERE hv.hubcourseid = hc.id)'
      . ' AND hd.timedownloaded > ?'
      . ') downloads FROM {block_hubcourses} hc JOIN {course} c ON hc.courseid = c.id '
      . ' ORDER BY downloads DESC LIMIT ?'
    , [$time, $limit]);
  } else if ($config->by == BLOCK_HUBCOURSERANKING_RANKBY_REVIEWS) {
    return $DB->get_records_sql(
      'SELECT hc.*, c.fullname, ('
      . 'SELECT COUNT(*) FROM {block_hubcourse_reviews} hr WHERE hr.hubcourseid = hc.id'
      . ' AND hr.timecreated > ?'
      . ') reviews FROM {block_hubcourses} hc JOIN {course} c ON hc.courseid = c.id'
      . ' ORDER BY reviews DESC LIMIT ?'
    , [$time, $limit]);
  } else if ($config->by == BLOCK_HUBCOURSERANKING_RANKBY_RATED) {
    $records = $DB->get_records_sql(
      'SELECT hc.*, c.fullname, ('
      . 'SELECT COALESCE(AVG(rate), 0) FROM {block_hubcourse_reviews} hr WHERE hr.hubcourseid = hc.id'
      . ' AND hr.timecreated > ?'
      . ') rated FROM {block_hubcourses} hc JOIN {course} c ON hc.courseid = c.id'
      . ' ORDER BY rated DESC LIMIT ?'
    , [$time, $limit]);
    foreach ($records as $record) {
      $record->rated = number_format($record->rated, 2);
    }
    return $records;
  }

  return null;
}
