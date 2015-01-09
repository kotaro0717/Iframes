<?php
/**
 * iframes view template
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */
?>

<?php echo $this->Html->script('/net_commons/base/js/workflow.js', false); ?>
<?php echo $this->Html->script('/iframes/js/iframes.js', false); ?>

<div id="nc-iframes-<?php echo (int)$frameId; ?>"
	 ng-controller="Iframes"
	 ng-init="initialize(<?php echo (int)$frameId; ?>,
						<?php echo h(json_encode($iframe)); ?>,
						<?php echo h(json_encode($iframeFrameSetting)); ?>)">

	<div class="text-left">
		<p class="text-right" style="float:right;">
			<?php echo $this->element('NetCommons.publish_button',
					array('status' => 'iframe.Iframe.status')); ?>

			<?php echo $this->element('NetCommons.setting_button'); ?>
		</p>
		<?php echo $this->element('NetCommons.status_label',
				array('status' => 'iframe.Iframe.status')); ?>
	</div>

	<?php echo $this->element('display_iframe'); ?>

</div>
