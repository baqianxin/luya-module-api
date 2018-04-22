<?php

namespace oom\api\admin\aws;

use oom\api\models\APIAuthUser;
use luya\admin\ngrest\base\ActiveWindow;

/**
 * Change Secret Active Window.
 *
 * File has been created with `aw/create` command.
 *
 * @property APIAuthUser $model The model evaluated by the `findOne` of the called ng rest model ActiveRecord.
 */
class ChangeSecretActiveWindow extends ActiveWindow
{
    /**
     * @var string The name of the module where the ActiveWindow is located in order to finde the view path.
     */
    public $module = '@apiadmin';

    /**
     * Default label if not set in the ngrest model.
     *
     * @return string The name of of the ActiveWindow. This is displayed in the CRUD list.
     */
    public function defaultLabel()
    {
        return 'Change Secret Active Window';
    }

    /**
     * Default icon if not set in the ngrest model.
     *
     * @var string The icon name from goolges material icon set (https://material.io/icons/)
     * @return string
     */
    public function defaultIcon()
    {
        return 'extension';
    }

    /**
     * The default action which is going to be requested when clicking the ActiveWindow.
     *
     * @return string The response string, render and displayed trough the angular ajax request.
     */
    public function index()
    {
        return $this->render('index', [
            'model' => $this->model,
        ]);
    }

    public function callbackResetAppSecret()
    {
        $this->model->generateAppSecret();
        return $this->model->save(false);
    }
}