<?php
/**
 * manage tab header element template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php $this->startIfEmpty('tabList'); ?>
<li ng-class="{active:tab.isSet(0)}">
	<a href="#" ng-click="showSetting('edit')">
		<?php echo __d('iframes', 'Edit'); ?>
	</a>
</li>

<?php if ($contentPublishable) : ?>
	<li ng-class="{active:tab.isSet(1)}">
		<a href="#" ng-click="showSetting('displayChange')">
			<?php echo __d('iframes', 'Display change'); ?>
		</a>
	</li>
<?php endif; ?>

<?php $this->end();