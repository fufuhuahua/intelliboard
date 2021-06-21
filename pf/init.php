<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * This plugin provides access to Moodle data in form of analytics and reports in real time.
 *
 *
 * @package    local_intelliboard
 * @copyright  2017 IntelliBoard, Inc
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 * @website    https://intelliboard.net/
 */
require('../../../config.php');
require_once($CFG->dirroot .'/local/intelliboard/locallib.php');
require_once($CFG->dirroot .'/local/intelliboard/pf/lib.php');

$id = optional_param('id', 0, PARAM_INT);
$sitecontext = context_system::instance();

require_login();
require_capability('local/intelliboard:pf', $sitecontext);

if ($id) {
	$USER->pcid = $id;
	redirect(new moodle_url("/local/intelliboard/pf/index.php"));
}
$params = array(
	'do'=>'pf',
	'mode'=> 2
);
$intelliboard = intelliboard($params);
if (!isset($intelliboard) || !$intelliboard->token) {
		throw new moodle_exception('invalidaccess', 'error');
}

$PAGE->set_url(new moodle_url("/local/intelliboard/pf/init.php"));
$PAGE->set_pagetype('courses');
$PAGE->set_pagelayout('report');
$PAGE->set_context(context_system::instance());
$PAGE->set_title(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->set_heading(get_string('intelliboardroot', 'local_intelliboard'));
$PAGE->requires->jquery();
$PAGE->requires->css('/local/intelliboard/assets/css/style.css');

$cohorts = intelliboard_pf_cohorts();

echo $OUTPUT->header();
?>
<?php if(!$cohorts): ?>
	<div class="alert alert-error alert-block fade in " role="alert">Franchise Group NOT found!</div>
<?php else: ?>
<div class="intelliboard-page intelliboard-pf">
	<?php include("views/menu.php"); ?>
	<?php if($cohorts): ?>
	<div class="intelliboard-pf-content pf-table">
		<div class="intelliboard-search clearfix">
			<form action="" method="GET">
				<input type="hidden" name="sesskey" value="<?php p(sesskey()); ?>" />
				<div class="form-group" style="margin:30px auto 10px; width:300px;">
			    <label for="cohort">Select cohort</label>
					<select class="form-control" name="id" id="cohort">
						<option value="0"></option>
						<?php foreach($cohorts as $cohort): ?>
							<option value="<?php echo $cohort->id; ?>" <?php echo (isset($USER->pcid) and $cohort->id == $USER->pcid)?'selected="selected"':''; ?>><?php echo $cohort->name; ?></option>
						<?php endforeach; ?>
					</select>
			  </div>
				<div class="form-group" style="margin:0 auto; width:300px;">
				<button class="btn btn-default" style="margin:0; width:100%;"><?php echo get_string('save');?></button>
			  </div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<?php else: ?>
		<div class="alert alert-info alert-block fade in " role="alert">Please select cohort</div>
	<?php endif; ?>

	<?php include("../views/footer.php"); ?>
</div>
<?php endif; ?>

<?php echo $OUTPUT->footer();
