<?php

namespace app\neuro\abstract;

use app\neuro\components\Message;
use app\neuro\exceptions\ModelNotFoundException;
use app\neuro\interfaces\ProviderInterface;
use Exception;
use yii\base\Model;
use yii\helpers\ArrayHelper;

abstract class Provider extends Model implements ProviderInterface
{
    public $token;
    protected $_model;
    protected $_async;
    public $url;
    public $folder_id;
    public $log_data = [];
    protected $_max_tokens;
    protected $_temperature;
    /**
     * @var \app\neuro\abstract\Model[]
     */
    private $models = [];

    abstract public function getClient();

    public function setModel(string $string)
    {
        $models = $this->getModels();

        foreach ($models as $model) {
            if ($model->code == $string) {
                return $this->_model = $model;
            }
        }

        throw new ModelNotFoundException();
    }

    /**
     * @return \app\neuro\abstract\Model
     */
    public function getModel()
    {
        return $this->_model;
    }

    abstract public function request($messages = []);

    abstract public function ask($prompt, $messages = []);

    abstract public function getName();

    public function askAsFile($prompt, $system = [])
    {
        return $this->responseToFilePath($this->ask($prompt, $system));
    }

    public function askAsText($prompt, $system = [])
    {
        return $this->responseToText($this->ask($prompt, $system));
    }

    protected function prepareMessage($messages = [])
    {
        /**
         * @var Message $message
         */
        $result = [];
        foreach ($messages as $message) {
            $result[] = ['role' => $message['role'], 'content' => $message['content']];
        }
        return $result;
    }

    public function hasModel($model)
    {
        return in_array($model, array_keys($this->getModels()));
    }

    public function setModels($array)
    {
        $this->models = $array;
    }

    public function getModels()
    {
        return $this->models;
    }

    public function responseToFilePath($response)
    {
        throw new Exception('Method not implemented');
    }

    public function responseToText($response)
    {
        return $response->choices[0]->message->content;
    }

    public function setTemperature($value)
    {
        return $this->_temperature = $value;
    }

    public function setMaxTokens($value)
    {
        return $this->_max_tokens = (int)$value;
    }

    public function getTemperature()
    {
        return $this->_temperature;
    }

    public function getMaxTokens()
    {
        return $this->_max_tokens;
    }
}