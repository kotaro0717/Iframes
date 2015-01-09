<?php
/**
 * iframes edit form element template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<div class="form-group has-feedback" ng-class="form.url.$invalid ? 'has-error' : 'has-success'">
	<label class="control-label">
		<?php echo __d('iframes', 'URL'); ?>
	</label>
	<?php echo $this->element('NetCommons.required'); ?>

	<div class="nc-iframes-url-alert" ng-class="form.url.$invalid ? 'alert-danger' : 'alert-success'">
		<input type="url" name="url" class="form-control" placeholder="http://" autofocus required
				ng-change="serverValidationClear(form, 'url')"
				ng-model="edit.data.Iframe.url">
	</div>
	<span class="form-control-feedback"
		   ng-class="form.url.$invalid ? 'glyphicon glyphicon-remove' : 'glyphicon glyphicon-ok'">
	</span>

	<div class="help-block">
		<br ng-hide="form.url.$invalid" />
		<div ng-show="form.url.$invalid">
			<div ng-repeat="errorMessage in form.url.validationErrors">
				{{errorMessage}}
			</div>
			<div ng-if="! form.url.validationErrors">
				<?php echo sprintf(__d('net_commons', 'Please input %s.'), __d('iframes', 'URL')); ?>
			</div>
		</div>
	</div>
</div>