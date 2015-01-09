<?php
/**
 * iframes display change form element template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>
<div class="form-group has-feedback" ng-class="form.height.$invalid ? 'has-error' : 'has-success'">
	<label class="control-label">
		<?php echo __d('iframes', 'Frame height'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div class="nc-iframes-height-alert" ng-class="form.height.$invalid ? 'alert-danger' : 'alert-success'">
		<input type="number" name="height" class="form-control" placeholder="400" autofocus required
			    min="<?php echo IframeFrameSetting::HEIGHT_MIN_VALUE ?>"
				max="<?php echo IframeFrameSetting::HEIGHT_MAX_VALUE ?>"
				ng-change="serverValidationClear(form, 'height')"
				ng-model="edit.data.IframeFrameSetting.height">
	</div>
	<span class="form-control-feedback"
		ng-class="form.height.$invalid ? 'glyphicon glyphicon-remove' : 'glyphicon glyphicon-ok'">
	</span>

	<div class="help-block">
		<br ng-hide="form.height.$invalid" />
		<div ng-show="form.height.$invalid">
			<div ng-repeat="errorMessage in form.height.validationErrors">
				{{errorMessage}}
			</div>
			<div ng-if="! form.height.validationErrors">
				<?php echo sprintf(__d('net_commons', 'Please input %s.'), __d('iframes', 'Frame height must be a number bigger than 1 and less than 2000')); ?>
			</div>
		</div>
	</div>
</div>

<div class='form-group'>
	<label>
		<input type="checkbox"
				ng-model="edit.data.IframeFrameSetting.display_scrollbar">

		<?php echo __d('iframes', 'Display the scroll bar'); ?>
	</label>
</div>

<div class='form-group'>
	<label>
		<input type="checkbox"
				ng-model="edit.data.IframeFrameSetting.display_frame">
		<?php echo __d('iframes', 'Display the frame'); ?>
	</label>
</div>