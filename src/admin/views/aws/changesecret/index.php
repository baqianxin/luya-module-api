<?php

/**
 * ChangeSecretActiveWindow Index View.
 *
 * @var $this ActiveWindowView
 * @var $model NgRestModel
 */

use oom\api\admin\Module;
use luya\admin\ngrest\base\ActiveWindowView;
use luya\admin\ngrest\base\NgRestModel;

?>
<script>
    zaa.bootstrap.register('ChangeSecretController', function($scope, $http, AdminToastService) {
        $scope.resetAppSecret = function () {
            AdminToastService.confirm("<?= Module::t('请确认是否要重新生成APP SECRET?此操作不可逆.') ?>", "<?= Module::t('确认重置 SECRET') ?>", function() {
                this.close();
                $scope.$parent.sendActiveWindowCallback('resetAppSecret').then(function(response) {
                    $scope.$parent.reloadActiveWindow();
                });
            });
        }
    });
</script>
<div class="content" ng-controller="ChangeSecretController">
    <div class="row" style="padding-bottom: 10px">
        <div class="col-md-6">
            <?= \luya\helpers\Html::button('更新Secret',
                ['class' => 'btn btn-primary', 'ng-click' => "resetAppSecret()"]) ?>
        </div>
        <div class="col-md-6">
            <?= $model->email ?>
        </div>
    </div>
    <?= \yii\widgets\DetailView::widget([
        'model' => $model,
    ]) ?>
</div>
