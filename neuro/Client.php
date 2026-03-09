<?php

namespace app\neuro;

use app\models\llm\AiProvider;
use app\neuro\abstract\Provider;
use app\neuro\interfaces\AsyncInterface;
use app\neuro\interfaces\FileProviderInterface;
use app\neuro\interfaces\ProviderInterface;
use ReflectionClass;
use Yii;
use yii\base\Model;

class Client extends Model
{
    /**
     * @var Provider
     */
    protected $_provider;


    public function setProvider(Provider $provider)
    {
        return $this->_provider = $provider;
    }

    public function ask($prompt, array $system = [])
    {
        return $this->_provider->ask($prompt, $system);
    }

    public function askAsText($prompt, $system)
    {
        $response = $this->_provider->ask($prompt, $system);
        return $this->_provider->responseToText($response);
    }

    public function askAsJson($prompt, $system)
    {
        $response = $this->askAsText($prompt, $system);
        if (str_contains($response, '```json')) {
            $response = trim($response, '`');
            $response = mb_substr($response, 4);
        }
        return json_decode($response);
    }

    protected static function getProviders()
    {
        $result = [];
        foreach (AiProvider::find()->notDeleted()->all() as $providerModel) {
            $result[] = $providerModel->getApi();
        }
        return $result;
    }

    public function setProviderByModel(string $model)
    {
        /**
         * @var ProviderInterface $provider
         */
        foreach (self::getProviders() as $provider) {
            if ($provider->hasModel($model)) {
                $provider->setModel($model);
                return $this->_provider = $provider;
            }
        }
        return null;
    }

    public static function getModels()
    {
        $result = [];
        foreach (self::getProviders() as $provider) {
            $key = $provider instanceof FileProviderInterface ? 'image' : 'text';
            foreach ($provider->getModels() as $model) {
                $result[] = ['model' => $model, 'type' => $key, 'provider' => $provider->getName()];
            }
        }

        return $result;
    }

    /**
     * @return ProviderInterface|FileProviderInterface|AsyncInterface
     */
    public function getProvider()
    {
        return $this->_provider;
    }
}