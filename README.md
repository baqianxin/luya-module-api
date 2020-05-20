# Api Module 
[![Build Status](https://travis-ci.org/baqianxin/luya-module-api.svg?branch=master)](https://travis-ci.org/baqianxin/luya-module-api)

An oauth module for LUYA Api users.
 
## Installation

Install the module trough composer

```sh
composer require oom/luya-module-apiauth:dev-master
```

In order to add the modules to your project go into the modules section of your config:

```php
return [
    'modules' => [
        // ...
        'api' => [
            'class' => 'oom\api\frontend\Module',
            'useAppViewPath' => true, // When enabled the views will be looked up in the @app/views folder, otherwise the views shipped with the module will be used.
        ],
        'apiadmin' => 'oom\api\admin\Module',
        // ...
    ],
];
```

Enable the user component with the built in ApiAuthUser class and add the REST Url rule:

```php
    'components' => [

        //...
        'user' => [
            'identityClass' => 'oom\api\models\APIAuthUser',
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

## Example usage

1. Insert the test user: `http://your.domain/api/sign/signup-test`
2. Send a post request to `http://your.domain/api/sign/token` with `app_key` and `app_secret` data in order to get the access token.

![Step 2](https://github.com/baqianxin/luya-module-api/raw/master/step-2.png)

3. Test Api with the created access token `http://your.domain/api/user/rules?token=ACCESS_TOKEN_FROM_TOKEN_REQUEST`

![Step 3](https://github.com/baqianxin/luya-module-api/raw/master/step-3.png)
