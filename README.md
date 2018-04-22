# Api Module 
<img src="https://travis-ci.org/baqianxin/luya-module-api.svg?branch=master" alt="build:passed">

File has been created with `module/create` command. 
 
## Installation

In order to add the modules to your project go into the modules section of your config:

```php
return [
    'modules' => [
        // ...
        'api' => [
            'class' => 'app\modules\api\frontend\Module',
            'useAppViewPath' => true, // When enabled the views will be looked up in the @app/views folder, otherwise the views shipped with the module will be used.
        ],
        'apiadmin' => 'app\modules\apiv1\admin\Module',
        // ...
    ],
];
```

Update components
```php
    'components' => [

        //...
        'user' => [
            'identityClass' => 'luya\apiauth\models\APIAuthUser',
            'enableAutoLogin' => true,
            'enableSession' => false,
            'loginUrl' => null,
        ],
        'urlManager' => [
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'api/user'],
            ],
        ],
        // ...   
    ]   
```
