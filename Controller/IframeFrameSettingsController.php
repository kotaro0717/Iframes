<?php
/**
 * Iframe Frame Settings Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('IframesAppController', 'Iframes.Controller');

/**
 * Iframe Frame Settings Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Iframes\Controller
 */
class IframeFrameSettingsController extends IframesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Iframes.Iframe',
		'Iframes.IframeFrameSetting',
	);

/**
 * use component
 *
 * @var array
 */
	public $components = array(
		'NetCommons.NetCommonsBlock', //Use Iframe model
		'NetCommons.NetCommonsFrame',
		'NetCommons.NetCommonsRoomRole' => array(
			//コンテンツの権限設定
			'allowedActions' => array(
				'contentEditable' => array('edit')
			),
		),
	);

/**
 * use helpers
 *
 * @var array
 */
	public $helpers = array(
		'NetCommons.Token'
	);

/**
 * view method
 *
 * @return void
 */
	public function view() {
		$this->layout = 'NetCommons.modal';
		$this->view = 'IframeFrameSettings/view';
	}
/**
 * edit method
 *
 * @return void
 */
	public function edit() {
		//登録処理
		if ($this->request->isPost()) {
			//登録
			$iframeFrameSetting = $this->IframeFrameSetting->saveIframeFrameSetting($this->data);
			if (! $iframeFrameSetting) {
				//バリデーションエラー
				$results = array('validationErrors' => $this->IframeFrameSetting->validationErrors);
				$this->renderJson($results, __d('net_commons', 'Bad Request'), 400);
				return;
			}
			$this->set('frameKey', $iframeFrameSetting['IframeFrameSetting']['frame_key']);
			$results = array('iframeFrameSetting' => $iframeFrameSetting);
			$this->renderJson($results, __d('net_commons', 'Successfully finished.'));
			return;
		}
		//最新データ取得
		$this->__setIframeFrameSetting();
		$results = array('iframeFrameSetting' => $this->viewVars['iframeFrameSetting']);

		$this->request->data = $this->viewVars['iframeFrameSetting'];
		$tokenFields = Hash::flatten($this->request->data);
		$hiddenFields = array(
			'iframeFrameSetting.frame_key',
		);
		$this->set('tokenFields', $tokenFields);
		$this->set('hiddenFields', $hiddenFields);
		$this->set('results', $results);
	}

/**
 * __setIframeFrameSetting method
 *
 * @return void
 */
	private function __setIframeFrameSetting() {
		//IframeFrameSettingデータの取得
		$iframeFrameSetting =
			$this->IframeFrameSetting->getIFrameFrameSetting(
				$this->viewVars['frameKey']
			);

		//IframeFrameSettingデータをviewにセット
		$this->set('iframeFrameSetting', $iframeFrameSetting);
	}

}