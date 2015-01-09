<?php
/**
 * IframeFrameSetting Model
 *
 * @property Frame $Frame
 *
 * @author Noriko Arai <arai@nii.ac.jp>
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @link http://www.netcommons.org NetCommons Project
 * @license http://www.netcommons.org/license.txt NetCommons License
 * @copyright Copyright 2014, NetCommons Project
 */

App::uses('IframesAppModel', 'Iframes.Model');

/**
 * IframeFrameSetting Model
 *
 * @author Kotaro Hokada <kotaro.hokada@gmail.com>
 * @package NetCommons\Iframes\Model
 */
class IframeFrameSetting extends IframesAppModel {

/**
 * use behaviors
 *
 * @var array
 */
	public $actsAs = array(
		'NetCommons.Trackable'
	);

/**
 * Minimum value of the height of the frame
 *
 * @var int
 */
	const HEIGHT_MIN_VALUE = '1';

/**
 * Maximum value of the height of the frame
 *
 * @var int
 */
	const HEIGHT_MAX_VALUE = '2000';

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
		'Frame' => array(
			'className' => 'Frames.Frame',
			'foreignKey' => 'frame_key',
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
			'frame_key' => array(
				'notEmpty' => array(
					'rule' => array('notEmpty'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				)
			),
			'height' => array(
				'numeric' => array(
					'rule' => array('range', self::HEIGHT_MIN_VALUE - 1, self::HEIGHT_MAX_VALUE + 1),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
			'display_scrollbar' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
			'display_frame' => array(
				'boolean' => array(
					'rule' => array('boolean'),
					'message' => __d('net_commons', 'Invalid request.'),
					'required' => true,
				),
			),
		));

		return parent::beforeValidate($options);
	}

/**
 * get iframe frame setting data
 *
 * @param string $frameKey frames.key
 * @return array
 */
	public function getIFrameFrameSetting($frameKey) {
		$conditions = array(
			'frame_key' => $frameKey,
		);

		$iframeFrameSetting = $this->find('first', array(
				'recursive' => -1,
				'conditions' => $conditions,
				'order' => 'IframeFrameSetting.id DESC'
			)
		);

		if (! $iframeFrameSetting) {
			$default = array(
				'frame_key' => $frameKey,
				'id' => '0'
			);
			$iframeFrameSetting = $this->create($default);
		}

		unset($iframeFrameSetting['IframeFrameSetting']['created'],
				$iframeFrameSetting['IframeFrameSetting']['created_user'],
				$iframeFrameSetting['IframeFrameSetting']['modified'],
				$iframeFrameSetting['IframeFrameSetting']['modified_user']);

		return $iframeFrameSetting;
	}

/**
 * save iframeFrameSetting
 *
 * @param array $data received post data
 * @return mixed On success Model::$data if its not empty or true, false on failure
 * @throws InternalErrorException
 */
	public function saveIframeFrameSetting($data) {
		//モデル定義
		$this->setDataSource('master');
		$models = array(
			'Frame' => 'Frames.Frame',
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

		try {
			//IframesFrameSettingデータの登録
			$iframeFrameSetting = $this->save(null, false);
			if (! $iframeFrameSetting) {
				throw new InternalErrorException(__d('net_commons', 'Internal Server Error'));
			}
			//トランザクションCommit
			$dataSource->commit();
			return $iframeFrameSetting;
		} catch (Exception $ex) {
			//トランザクションRollback
			$dataSource->rollback();
			//エラー出力
			CakeLog::write(LOG_ERR, $ex);
			throw $ex;
		}
	}

/**
 * validate iframeFrameSetting
 *
 * @param array $data received post data
 * @return bool|array True on success, validation errors array on error
 */
	private function __validateIframe($data) {
		//IframeFrameSettingデータの取得
		$iframeFrameSetting = $this->getIframeFrameSetting(
				(int)$data['IframeFrameSetting']['frame_key']
			);

		//IframeFrameSettingデータの登録
		if (! isset($data['IframeFrameSetting']['height'])) {
			//定義されていない場合、Noticeが発生するため、nullで初期化
			$data['IframeFrameSetting']['height'] = null;
		}
		if ($data['IframeFrameSetting']['height'] !== $iframeFrameSetting['IframeFrameSetting']['height'] ||
				$data['IframeFrameSetting']['display_scrollbar'] !== $iframeFrameSetting['IframeFrameSetting']['display_scrollbar'] ||
				$data['IframeFrameSetting']['display_frame'] !== $iframeFrameSetting['IframeFrameSetting']['display_frame']) {
			unset($data['IframeFrameSetting']['id']);
			$iframeFrameSetting = $this->create();
		}
		$iframeFrameSetting['IframeFrameSetting'] = $data['IframeFrameSetting'];
		$this->set($iframeFrameSetting);
		$this->validates();
		return $this->validationErrors ? $this->validationErrors : true;
	}
}
