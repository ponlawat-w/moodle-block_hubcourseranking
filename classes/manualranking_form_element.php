<?php

defined('MOODLE_INTERNAL') || die();

include_once(__DIR__ . '/../../../lib/form/editor.php');
include_once(__DIR__ . '/../lib.php');

class block_hubcourseranking_manualranking_form_element extends MoodleQuickForm_editor {

  private static $js_called = false;

  private static function call_js() {
    global $PAGE;
    if (!self::$js_called) {
      $PAGE->requires->jquery();
      $PAGE->requires->js(new moodle_url('/blocks/hubcourseranking/manualranking.js'));
      self::$js_called = true;
    }
  }

  private $value;
  private $elementName;

  public function __construct($elementName = null, $elementLabel = null) {
    $this->elementName = $elementName;
    parent::__construct($elementName, $elementLabel);
    self::call_js();
  }

  public function setValue($value) {
    $this->value = $value;
  }

  public function getValue() {
    return $this->value;
  }

  public function toHtml() {
    $hubcourses = block_hubcourseranking_getcourses();
    $html = html_writer::start_tag('input', [
      'type' => 'hidden',
      'name' => $this->elementName,
      'value' => $this->getValue()
    ]);

    $rows = [];
    foreach ($hubcourses as $hubcourse) {
      $row = new html_table_row(['', $hubcourse->fullname]);
      $row->attributes['class'] = 'manualranking-row';
      $row->attributes['data-courseid'] = $hubcourse->courseid;
      $row->style = 'cursor: pointer;';
      $rows[] = $row;
    }

    $table = new html_table();
    $table->head = [get_string('order', 'block_hubcourseranking'), get_string('coursename', 'block_hubcourseranking')];
    $table->colclasses = ['manualranking-order text-center', 'manualranking-name'];
    $table->data = $rows;

    $html .= html_writer::table($table);

    $html .= html_writer::start_tag('p');
    $html .= html_writer::link('javascript:void(0);', get_string('deselectall', 'block_hubcourseranking'), [
      'class' => 'btn btn-danger manualranking-deselect'
    ]);
    $html .= html_writer::end_tag('p');

    return html_writer::div($html, '', ['data-formtype' => 'manualranking']);
  }
}
