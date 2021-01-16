<?php

$capabilities = [
  'block/hubcourseranking:addinstance' => [
    'riskbitmask' => RISK_SPAM,
    'captype' => 'write',
    'contextlevel' => CONTEXT_BLOCK,
    'archtypes' => [],
  ],
  'block/hubcourseranking:myaddinstance' => [
    'riskbitmask' => RISK_SPAM,
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archtypes' => [],
  ],
  'block/hubcourseranking:managecontent' => [
    'riskbitmask' => RISK_SPAM,
    'captype' => 'write',
    'contextlevel' => CONTEXT_SYSTEM,
    'archtypes' => []
  ]
];
