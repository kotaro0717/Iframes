<?php
/**
 * Iframe Model
 *
 * @property Block $Block
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('IframesAppModel', 'Iframes.Model');

/**
 * Iframe Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Iframes\Model
 */
class Iframe extends IframesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable',
		'NetCommons.Publishable'
	);

/**
 * Validation rules
 *
 * @var array
 */
	const COMMENT_PLUGIN_KEY = 'iframes';

/**
 * Validation rules
 *
 * @var array
 */
	public $validate = array();

	//The Associations below have been created with all possible keys, those that are not needed can be removed

/**
 * belongsTo associations
 *
 * @var array
 */
	public $belongsTo = array(
		'Block' => array(
			'className' => 'Blocks.Block',
			'foreignKey' => 'block_id',
			'conditions' => '',
			'fields' => '',
			'order' => ''
		),
		'CreatedUser' => array(
			'className' => 'Users.UserAttributesUser',
			'foreignKey' => false,
			'conditions' => array(
				'Iframe.created_user = CreatedUser.user_id',
				'CreatedUser.key' => 'nickname'
			),
			'fields' => array('CreatedUser.key', 'CreatedUser.value'),
			'order' => ''
		)
	);

/**
 * Called during validation operations, before validation. Please note that custom
 * validation rules can be defined in $validate.
 *
 * @param array $options Options passed from Model::save().
 * @return bool True if validate operation should continue, false to abort
 * @link http://book.cakephp.org/2.0/en/models/callback-methods.html#beforevalidate
 * @see Model::save()
 */
	public function beforeValidate($options = array()) {
		$this->validate = Hash::merge($this->validate, array(
			'block_id' => array(
				'numeric' => array(
					'rule' => array('numeric'),
					'message' => __d('net_commons', 'Invalid request.'),
					'allowEmpty' => false,
					'required' => true,
				)
			),
			'key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),

			//status to set in PublishableBehavior.

			'url' => array(
				'website' => array(
					'rule' => array('url', true),
					'message' => sprintf(__d('net_commons', 'Please input %s.'), __d('iframes', 'URL')),
					'required' => true,
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * get iframe data
 *
 * @param int $frameId frames.id
 * @param int $blockId blocks.id
 * @param bool $contentEditable true can edit the content, false not can edit the content.
 * @return array
 */
	public function getIframe($frameId, $blockId, $contentEditable) {
		$conditions = array(
			'block_id' => $blockId,
		);
		if (! $contentEditable) {
			$conditions['status'] = NetCommonsBlockComponent::STATUS_PUBLISHED;
		}

		$iframe = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => 'Iframe.id DESC',
			)
		);

		if ($contentEditable && ! $iframe) {
			$default = array(
				'url' => '',
				'key' => '',
				'block_id' => '0',
				'id' => '0'
			);
			$iframe = $this->create($default);
		}

		unset($iframe['Iframe']['created'],
				$iframe['Iframe']['created_user'],
				$iframe['Iframe']['modified'],
				$iframe['Iframe']['modified_user']);

		if ($iframe) {
			//Commentセット
			$iframe['Comment']['comment'] = '';
			//Frameセット
			$iframe['Frame']['id'] = $frameId;
		}

		return $iframe;
	}

/**
 * save iframe
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveIframe($data) {
		//モデル定義
		$this->setDataSource('master');
		$models = array(
			'Block' => 'Blocks.Block',
			'Comment' => 'Comments.Comment',
			'IframeFrameSetting' => 'Iframes.IframeFrameSetting',
		);
		foreach ($models as $model => $class) {
			$this->$model = ClassRegistry::init($class);
			$this->$model->setDataSource('master');
		}

		//トランザクションBegin
		$dataSource = $this->getDataSource();
		$dataSource->begin();

		//validationを実行
		$ret = $this->__validateIframe($data);
		if (is_array($ret)) {
			$this->validationErrors = $ret;
			return false;
		}
		$ret = $this->Comment->validateByStatus($data, array('caller' => $this->name));
		if (is_array($ret)) {
			$this->validationErrors = $ret;
			return false;
		}

		try {
			//ブロックの登録
			$block = $this->Block->saveByFrameId($data['Frame']['id'], false);

			//Iframeデータの登録
			$this->data['Iframe']['block_id'] = (int)$block['Block']['id'];

			$iframe = $this->save(null, false);
			if (! $iframe) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//コメントの登録
			if ($this->Comment->data) {
				if (! $this->Comment->save(null, false)) {
					throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
				}
			}
			//トランザクションCommit
			$dataSource->commit();
			return $iframe;
		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			//エラー出力
			CakeLog::write(LOG_ERR, $ex);
			throw $ex;
		}
	}

/**
 * validate iframe
 *
 * @param array $data received post data
 * @return bool|array True on success, validation errors array on error
 */
	private function __validateIframe($data) {
		//Iframeデータの取得
		$iframe = $this->getIframe(
				(int)$data['Frame']['id'],
				(int)$data['Iframe']['block_id'],
				true
			);

		if ($iframe['Iframe']['key'] === '') {
			$data[$this->name]['key'] = Security::hash($this->name . mt_rand() . microtime(), 'md5');
		}

		if ($data['Iframe']['url'] !== $iframe['Iframe']['url'] ||
				$data['Iframe']['status'] !== $iframe['Iframe']['status']) {

			unset($data['Iframe']['id']);
			$iframe = $this->create();
		}

		$iframe['Iframe'] = $data['Iframe'];
		$this->set($iframe);
		$this->validates();
		return $this->validationErrors ? $this->validationErrors : true;
	}
}