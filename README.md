# Api Module
 
File has been created with `module/create` command. 
 
## Installation

In order to add the modules to your project go into the modules section of your config:

```php
return [
    'modules' => [
        // ...
        'apifrontend' => [
            'class' => 'app\modules\api\frontend\Module',
            'useAppViewPath' => true, // When enabled the views will be looked up in the @app/views folder, otherwise the views shipped with the module will be used.
        ],
        'apiv1' => 'app\modules\apiv1\admin\Module',
        // ...
    ],
];
```

Update components
```php
    'components' => [

        //...
        'user' => [
            'identityClass' => 'app\components\models\ApiAuthUser',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'urlManager' => [
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'addressbookadmin/user'],
            ],
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_RAW,
            'charset' => 'UTF-8',
            // ...
        ],
        // ...   
    ]   
```