<?php

defined('MOODLE_INTERNAL') || die();

class block_hubcourseranking extends block_base {
  public function init() {
    $this->title = (isset($this->config) && isset($this->config->title)) ?
      $this->config->title : get_string('pluginname', 'block_hubcourseranking');
    $this->version = 2020010800;
  }

  public function has_config() {
    return false;
  }

  public function instance_can_be_hidden() {
    return false;
  }

  public function instance_allow_multiple() {
    return true;
  }

  public function applicable_formats() {
    return [
      'all' => false,
      'my' => true,
      'site' => true
    ];
  }

  public function get_content() {
    global $PAGE;

    $PAGE->requires->jquery();
    $PAGE->requires->js(new moodle_url('/blocks/hubcourseranking/script.js'));
    $PAGE->requires->strings_for_js([
      'coursename',
      'uploadeddate',
      'downloads',
      'reviews',
      'ratings',
      'loadmore',
      'loading'
    ], 'block_hubcourseranking');

    if (isset($this->config)) {
      $this->title = $this->config->title;
      $this->content = new stdClass();
      $this->content->text = html_writer::div(
        get_string('loading', 'block_hubcourseranking'),
        'block-hubcourseranking-body',
        [
          'data-id' => $this->instance->id,
        ]
      );
    }

    return $this->content;
  }

  public function get_aria_role() {
    return 'navigation';
  }
}
