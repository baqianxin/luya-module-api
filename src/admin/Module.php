<?php

namespace oom\api\admin;

use luya\admin\components\AdminMenuBuilder;

/**
 * Api Admin Module.
 *
 * File has been created with `module/create` command.
 *
 * @author
 * @since 1.0.0
 */
class Module extends \luya\admin\base\Module
{

    public $apis = [
        'api-auth-user' => 'oom\api\admin\apis\ApiAuthUserController',
    ];

    public function getMenu()
    {
        return (new AdminMenuBuilder($this))
            ->node('API管理', 'extension')
            ->group('Group')
            ->itemApi('授权用户', 'apiadmin/api-auth-user/index', 'poll', 'api-auth-user');
    }

    /**
     * @inheritdoc
     */
    public static function onLoad()
    {
        self::registerTranslation('apiadmin*', static::staticBasePath() . '/messages', [
            'apiadmin' => 'apiadmin.php',
        ]);
    }

    /**
     * Translations for CMS Module.
     *
     * @param string $message
     * @param array $params
     * @return string
     */
    public static function t($message, array $params = [])
    {
        return parent::baseT('apiadmin', $message, $params);
    }


}