# yii2-request-limit
Check user request on action in controller

## Installation

Recommended installation via [composer](http://getcomposer.org/download/):

```
composer require venomousboy/yii2-request-limit
```

## Usage

Use in Controller behaviors:

```php

class CommentController extends \yii\web\Controller
{
    public function behaviors()
    {
        return ArrayHelper::merge(
            parent::behaviors(),
            [
                'request_limit' => [
                    'class' => RequestLimitFilter::class
                ],
            ]
        );
    }

...
}
```

in Controller add $requestLimitActions array with controller action names:

```php

class CommentController extends \yii\web\Controller
{
    public $requestLimitActions = [
        'actionHasPhone',
        'actionHasEmail'
    ];
...
}
```

Initial settings

```php
    const TIME_LIMIT = 600; //Checking duration in seconds - 10 minutes
    const TIME_WAITING = 1800; //Ban duration in seconds - 30 minutes
    const CHECK_COUNT = 10; //Check count per one user
```
