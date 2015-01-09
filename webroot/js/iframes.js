/**
 * @fileoverview Iframes Javascript
 * @author kotaro.hokada@gmail.com (Kotaro Hokada)
 */


/**
 * Iframes Javascript
 *
 * @param {string} Controller name
 * @param {function($scope)} Controller
 */
NetCommonsApp.controller('Iframes',
    function($scope, NetCommonsBase, NetCommonsWorkflow, NetCommonsFlash) {

      /**
       * workflow
       *
       * @type {object}
       */
      $scope.workflow = NetCommonsWorkflow.new($scope);

      /**
       * Iframe
       *
       * @type {Object.<string>}
       */
      $scope.iframe = {};

      /**
       * Iframe Frame Setting
       *
       * @type {Object.<string>}
       */
      $scope.iframeFrameSetting = {};

      /**
       * post object
       *
       * @type {Object.<string>}
       */
      $scope.edit = {
        _method: 'POST',
        data: {}
      };

      /**
       * iframes plugin id
       *
       * @const
       */
      $scope.IFRAME_ID = '#nc-iframes-';

      /**
       * iframes plugin id
       *
       * @type {sring}
       */
      $scope.iframeId = '';

      /**
       * iframe tag
       *
       * @const
       */
      $scope.IFRAME_TAG = ' iframe';

      /**
       * iframe tag
       *
       * @type {sring}
       */
      $scope.iframeTag = '';

      /**
       * iframe tab parent class
       *
       * @const
       */
      $scope.IFRAME_TAG_PARENT_CLASS = ' .nc-iframes-display-iframe';

      /**
       * iframe tab parent class
       *
       * @type {sring}
       */
      $scope.iframeTagParentClass = '';

      $scope.tabMode = 0;

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(frameId, iframe, iframeFrameSetting) {
        $scope.frameId = frameId;
        $scope.iframe = iframe;
        $scope.iframeFrameSetting = iframeFrameSetting;
        $scope.iframeId = $scope.IFRAME_ID + $scope.frameId;
        $scope.iframeTag = $scope.iframeId + $scope.IFRAME_TAG;
        $scope.iframeTagParentClass =
                            $scope.iframeId + $scope.IFRAME_TAG_PARENT_CLASS;
      };

      /**
       * Show manage dialog
       *
       * @return {void}
       */
      $scope.showSetting = function(controller) {
        switch (controller) {
          case 'edit':
          default :
            $scope.plugin = NetCommonsBase.initUrl('iframes', 'iframes');
            $scope.tabMode = 0;
            NetCommonsBase.showSetting(
                $scope.plugin.getUrl('edit', $scope.frameId + '.json'),
                $scope.setEditData,
                {templateUrl: $scope.plugin.getUrl('setting', $scope.frameId),
                  scope: $scope,
                  controller: 'Iframes.edit'}
            );
            break;
          case 'displayChange':
            $scope.plugin = NetCommonsBase.initUrl('iframes', 'iframeFrameSettings');
            $scope.tabMode = 1;
            NetCommonsBase.showSetting(
                $scope.plugin.getUrl('edit', $scope.frameId + '.json'),
                $scope.setEditData,
                {templateUrl: $scope.plugin.getUrl('view', $scope.frameId),
                  scope: $scope,
                  controller: 'Iframes.displayChange'}
            );
            break;
        }
      };

      /**
       * dialog initialize
       *
       * @return {void}
       */
      $scope.setEditData = function(data) {
        if (! $scope.tabMode) {
          //workflow初期化
          $scope.workflow.clear();

          //最新データセット
          if (data) {
            $scope.iframe = data.iframe;
            $scope.workflow.init('iframes',
                                 $scope.iframe.Iframe.key,
                                 data['comments']);
          }

          //編集データセット
          $scope.edit.data = angular.copy($scope.iframe);

          $scope.workflow.currentStatus = $scope.iframe.Iframe.status;
          $scope.workflow.editStatus = $scope.edit.data.Iframe.status;
          $scope.workflow.input.comment = $scope.edit.data.Comment.comment;

        } else {
          //最新データセット
          $scope.iframeFrameSetting = data.iframeFrameSetting;
          //jsで受け取った際に文字列配列として扱われるため、数値化
          //TODO:view側でng-modelに設定する際に型変換できないか？
          $scope.iframeFrameSetting.IframeFrameSetting.height =
            +(data.iframeFrameSetting.IframeFrameSetting.height);

          //jsで受け取った際に文字列配列として扱われるため、二値化
          //TODO:view側でng-modelに設定する際に型変換できないか？
          $scope.iframeFrameSetting.IframeFrameSetting.display_scrollbar =
            Boolean(+(data.iframeFrameSetting.IframeFrameSetting.display_scrollbar));
          $scope.iframeFrameSetting.IframeFrameSetting.display_frame =
            Boolean(+(data.iframeFrameSetting.IframeFrameSetting.display_frame));

          //編集データセット
          $scope.edit.data = angular.copy($scope.iframeFrameSetting);
        }
      };

      /**
       * published method
       *
       * @return {void}
       */
      $scope.publish = function() {
        $scope.edit.data = angular.copy($scope.iframe);

        $scope.edit.data.Iframe.status = NetCommonsBase.STATUS_PUBLISHED;

        $scope.plugin = NetCommonsBase.initUrl('iframes', 'iframes');

        NetCommonsBase.save(
            null,
            $scope.plugin.getUrl('edit', $scope.frameId + '.json'),
            $scope.edit,
            function(data) {
              angular.copy(data.results.iframe, $scope.iframe);
              NetCommonsFlash.success(data.name);
            });
      };
    });


/**
 * Iframes.edit Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $modalStack)} Controller
 */
NetCommonsApp.controller('Iframes.edit',
    function($scope, $modalStack, NetCommonsBase, NetCommonsUser,
             NetCommonsFlash) {

      /**
       * show user information method
       *
       * @param {number} users.id
       * @return {string}
       */
      $scope.user = NetCommonsUser.new();

      /**
       * serverValidationClear method
       *
       * @param {number} users.id
       * @return {string}
       */
      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      /**
       * form
       *
       * @type {form}
       */
      $scope.form = {};

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(form) {
        $scope.form = form;
      };

      /**
       * dialog save
       *
       * @param {number} status
       * - 1: Publish
       * - 2: Approve
       * - 3: Draft
       * - 4: Disapprove
       * @return {void}
       */
      $scope.save = function(status) {
        $scope.edit.data.Iframe.status = status;
        $scope.workflow.editStatus = status;
        $scope.edit.data.Comment.comment = $scope.workflow.input.comment;

        NetCommonsBase.save(
            $scope.form,
            $scope.plugin.getUrl('edit', $scope.frameId + '.json'),
            $scope.edit,
            function(data) {
              $scope.setLatestData(data.results.iframe.Iframe);
              angular.copy(data.results.iframe, $scope.iframe);
              NetCommonsFlash.success(data.name);
              $modalStack.dismissAll('saved');
            });
      };

      /**
       * set iframe latest data
       *
       * @param {Object.<string>} Iframe
       * @return {void}
       */
      $scope.setLatestData = function(Iframe) {
        if ($($scope.iframeTag).length === 0) {
          //create iframe tag if it isn't created
          $($scope.iframeTagParentClass).html('');
          $($scope.iframeTagParentClass).html($('<iframe width="100%">'));

          //set height
          $($scope.iframeTag).prop('height',
              +($scope.iframeFrameSetting.IframeFrameSetting.height));

          //set scrolling
          $($scope.iframeTag).prop('scrolling',
              (+($scope.iframeFrameSetting
                 .IframeFrameSetting.display_scrollbar) === 1 ? 'yes' : 'no'));

          //set frameborder
          $($scope.iframeTag).prop('frameborder',
              (+($scope.iframeFrameSetting
                 .IframeFrameSetting.display_frame) === 1 ? '1' : '0'));
        }

        //set src
        $($scope.iframeTag).prop('src', Iframe.url);
      };
    });


/**
 * Iframes.displayChange Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, $modalStack)} Controller
 */
NetCommonsApp.controller('Iframes.displayChange',
    function($scope, $modalStack, NetCommonsBase, NetCommonsUser,
             NetCommonsFlash) {

      /**
       * show user information method
       *
       * @param {number} users.id
       * @return {string}
       */
      $scope.user = NetCommonsUser.new();

      /**
       * serverValidationClear method
       *
       * @param {number} users.id
       * @return {string}
       */
      $scope.serverValidationClear = NetCommonsBase.serverValidationClear;

      /**
       * form
       *
       * @type {form}
       */
      $scope.form = {};

      /**
       * Initialize
       *
       * @return {void}
       */
      $scope.initialize = function(form) {
        $scope.form = form;
      };

      /**
       * dialog save
       *
       * @return {void}
       */
      $scope.save = function() {

        NetCommonsBase.save(
            $scope.form,
            $scope.plugin.getUrl('edit', $scope.frameId + '.json'),
            $scope.edit,
            function(data) {
              if ($($scope.iframeTag).length === 1) {
                $scope.setLatestData(data.results.iframeFrameSetting.IframeFrameSetting);
              }
              angular.copy(data.results.iframeFrameSetting, $scope.IframeFrameSetting);
              NetCommonsFlash.success(data.name);
              $modalStack.dismissAll('saved');
            });
      };

      /**
       * set iframeFrameSetting latest data
       *
       * @param {Object.<string>} IframeFrameSetting
       * @return {void}
       */
      $scope.setLatestData = function(IframeFrameSetting) {
        //set height
        $($scope.iframeTag).prop('height', +(IframeFrameSetting.height));

        //set scrolling
        $($scope.iframeTag).prop('scrolling',
            (+(IframeFrameSetting.display_scrollbar) === 1 ? 'yes' : 'no'));

        //set frameborder
        $($scope.iframeTag).prop('frameborder',
            (+(IframeFrameSetting.display_frame) === 1 ? '1' : '0'));
      };
    });