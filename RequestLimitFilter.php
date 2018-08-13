<?php

namespace venomousboy\yii2-request-limit;

use Yii;
use yii\base\ActionFilter;

/**
 * Class RequestLimitFilter
 * @package app\controllers\filters
 */
class RequestLimitFilter extends ActionFilter
{
    const TIME_LIMIT = 600; //Checking duration in seconds - 10 minutes
    const TIME_WAITING = 1800; //Ban duration in seconds - 30 minutes
    const CHECK_COUNT = 10; //Check count per one user

    /**
     * @param \yii\base\Action $action
     * @return bool
     * @throws \Exception
     */
    public function beforeAction($action): bool
    {
        if (in_array($action->actionMethod, $action->controller->requestLimitActions)) {
            $hash = $this->generateHashBy($action->actionMethod);
            if (!$this->handle($hash)) {
                throw new \Exception(
                    Yii::t('app', 'The limit of access to server was ended')
                );
            }
        }

        return parent::beforeAction($action);
    }

    public function generateHashBy($key)
    {
        return base64_encode(Yii::$app->request->getUserIP() . Yii::$app->request->getUserAgent() . $key);
    }

    /**
     * @param string $hash
     * @return bool
     */
    public function handle(string $hash): bool
    {
        $value = Yii::$app->unauthorizedStorage->get($hash);

        if (!$value) {
            $this->create($hash);
            return true;
        }

        if ($this->limitIsOver($value)) {
            $this->setDurationWaiting($hash, $value);
            return false;
        }

        $this->increment($hash, $value);
        return true;
    }

    private function create(string $hash): void
    {
        Yii::$app->unauthorizedStorage->add($hash, 1, self::TIME_LIMIT);
    }

    private function increment(string $hash, int $count): void
    {
        Yii::$app->unauthorizedStorage->set($hash, $count + 1);
    }

    private function limitIsOver(int $value): bool
    {
        return $value >= self::CHECK_COUNT;
    }

    private function setDurationWaiting(string $hash, int $count): void
    {
        Yii::$app->unauthorizedStorage->set($hash, $count, self::TIME_WAITING);
    }
}
