<?php
/**
 * Iframes Controller
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('IframesAppController', 'Iframes.Controller');

/**
 * Iframes Controller
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Iframes\Controller
 */
class IframesController extends IframesAppController {

/**
 * use model
 *
 * @var array
 */
	public $uses = array(
		'Frames.Frame',
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
				'contentEditable' => array('setting', 'edit')
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
 * index method
 *
 * @return void
 */
	public function index() {
		$this->view = 'Iframes/view';
		$this->view();
	}

/**
 * view method
 *
 * @return void
 */
	public function view() {
		//Iframeデータを取得
		$this->__setIframe();
		//IframeFrameSettingデータを取得
		$this->__setIframeFrameSetting();

		if ($this->viewVars['contentEditable']) {
			$this->view = 'Iframes/viewForEditor';
		}
		if (! $this->viewVars['iframe'] || ! $this->viewVars['iframeFrameSetting']) {
			$this->autoRender = false;
		}
	}

/**
 * setting method
 *
 * @return void
 */
	public function setting() {
		$this->layout = 'NetCommons.modal';
		$this->__setIframe();
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
			$iframe = $this->Iframe->saveIframe($this->data);
			if (! $iframe) {
				//バリデーションエラー
				$results = array('validationErrors' => $this->Iframe->validationErrors);
				$this->renderJson($results, __d('net_commons', 'Bad Request'), 400);
				return;
			}
			$this->set('blockId', $iframe['Iframe']['block_id']);
			$results = array('iframe' => $iframe);
			$this->renderJson($results, __d('net_commons', 'Successfully finished.'));
			return;
		}
		//最新データ取得
		$this->__setIframe();
		$results = array('iframe' => $this->viewVars['iframe']);

		//コメントデータ取得
		$contentKey = $this->viewVars['iframe']['Iframe']['key'];
		if ($contentKey) {
			$view = $this->requestAction(
					'/comments/comments/index/iframes/' . $contentKey . '.json', array('return'));
			$comments = json_decode($view, true);
			//JSON形式で戻す
			$results = Hash::merge($comments['results'], $results);
		}

		$this->request->data = $this->viewVars['iframe'];
		$tokenFields = Hash::flatten($this->request->data);
		$hiddenFields = array(
			'iframe.block_id',
			'iframe.key'
		);
		$this->set('tokenFields', $tokenFields);
		$this->set('hiddenFields', $hiddenFields);
		$this->set('results', $results);
	}

/**
 * __setIframe method
 *
 * @return void
 */
	private function __setIframe() {
		//Iframeデータの取得
		$iframe = $this->Iframe->getIframe(
				$this->viewVars['frameId'],
				$this->viewVars['blockId'],
				$this->viewVars['contentEditable']
			);

		//Iframeデータをviewにセット
		$this->set('iframe', $iframe);
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