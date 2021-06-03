<?php

defined('MOODLE_INTERNAL') || die();

include_once(__DIR__ . '/lib.php');

class block_hubcourseranking_edit_form extends block_edit_form {

  private static $defaultvalues = [
    'config_title' => '',
    'config_ismanual' => 0,
    'config_by' => BLOCK_HUBCOURSERANKING_RANKBY_RECENT,
    'config_duration' => BLOCK_HUBCOURSERANKING_DURATION_MONTH,
    'config_manual' => '',
    'config_displaycount' => 5
  ];
  
  protected function specific_definition($mform) {
    global $CFG, $PAGE;

    $context = context_system::instance();

    if (has_capability('block/hubcourseranking:managecontent', $context)) {
      $PAGE->requires->jquery();
      $PAGE->requires->js(new moodle_url('/blocks/hubcourseranking/edit_form.js'));

      $mform->addElement('header', 'blockheader', get_string('block', 'block_hubcourseranking'));
      $mform->setExpanded('blockheader', true);
      $mform->addElement('text', 'config_title', get_string('title', 'block_hubcourseranking'));
      $mform->settype('config_title', PARAM_TEXT);
      
      $mform->addElement('header', 'rankingheader', get_string('rankingtypeheader', 'block_hubcourseranking'));
      $mform->setExpanded('rankingheader', true);
      $mform->addGroup([
        $mform->createElement('radio', 'config_ismanual', '', get_string('auto', 'block_hubcourseranking'), 0, []),
        $mform->createElement('radio', 'config_ismanual', '', get_string('manual', 'block_hubcourseranking'), 1, [])
      ], 'ranking_type', get_string('rankingtype', 'block_hubcourseranking'), [''], false);
      $mform->settype('config_ismanual', PARAM_INT);

      $mform->addElement('select', 'config_by', get_string('rankby', 'block_hubcourseranking'), [
        BLOCK_HUBCOURSERANKING_RANKBY_RECENT => get_string('rankby_recent', 'block_hubcourseranking'),
        BLOCK_HUBCOURSERANKING_RANKBY_DOWNLOAD => get_string('rankby_download', 'block_hubcourseranking'),
        BLOCK_HUBCOURSERANKING_RANKBY_REVIEWS => get_string('rankby_reviews', 'block_hubcourseranking'),
        BLOCK_HUBCOURSERANKING_RANKBY_RATED => get_string('rankby_rated', 'block_hubcourseranking')
      ]);
      $mform->settype('config_by', PARAM_TEXT);
      $mform->addElement('select', 'config_duration', get_string('rankduration', 'block_hubcourseranking'), [
        BLOCK_HUBCOURSERANKING_DURATION_MONTH => get_string('rankduration_month', 'block_hubcourseranking'),
        BLOCK_HUBCOURSERANKING_DURATION_YEAR => get_string('rankduration_year', 'block_hubcourseranking')
      ]);
      $mform->settype('config_duration', PARAM_TEXT);

      MoodleQuickForm::registerElementType(
        'manualranking',
        "{$CFG->dirroot}/blocks/hubcourseranking/classes/manualranking_form_element.php",
        'block_hubcourseranking_manualranking_form_element'
      );
      $mform->addElement('manualranking', 'config_manual', get_string('manualranking', 'block_hubcourseranking'));
      $mform->settype('config_manual', PARAM_RAW);

      $mform->addElement('header', 'displayheader', get_string('displayheader', 'block_hubcourseranking'));
      $mform->setExpanded('displayheader', true);
      $options = block_hubcourseranking_getshowoptions();
      $mform->addElement('select', 'config_displaycount', get_string('displaycount', 'block_hubcourseranking'), $options);
      $mform->settype('config_displaycount', PARAM_INT);

      foreach (self::$defaultvalues as $key => $defaultvalue) {
        $mform->setDefault($key,
          isset($this->block->config->{$key}) ? $this->block->config->{$key} : $defaultvalue);
      }
    }
  }
}
