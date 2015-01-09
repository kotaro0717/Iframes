<?php
/**
 * iframe display change view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php $formName = 'IframeFrameSettingForm' . (int)$frameId; ?>

<?php $this->start('titleForModal'); ?>
<?php echo __d('iframes', 'plugin_name'); ?>
<?php $this->end(); ?>

<?php $this->start('tabIndex'); ?>
<?php echo '1'; ?>
<?php $this->end(); ?>

<?php echo $this->element('manage_tab_list'); ?>

<div ng-show="tab.isSet(1)">
	<?php echo $this->Form->create('IframeFrameSetting' . (int)$frameId, array(
			'type' => 'get',
			'name' => $formName,
			'novalidate' => true,
		)); ?>

		<div class="panel panel-default" ng-init="initialize(<?php echo $formName; ?>)">
			<div class="panel-body">
				<?php echo $this->element('display_change_form'); ?>
			</div>

			<div class="panel-footer text-center">
				<button type="button" class="btn btn-default" ng-click="cancel()" ng-disabled="sending">
					<span class="glyphicon glyphicon-remove"></span>
					<?php echo __d('net_commons', 'Cancel'); ?>
				</button>

				<button type="button" class="btn btn-primary"
					ng-disabled="(sending || form.$invalid)"
					ng-click="save()">

					<?php echo __d('net_commons', 'OK'); ?>
				</button>

			</div>

		</div>
</div>