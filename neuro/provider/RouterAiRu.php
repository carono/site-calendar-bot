<?php

namespace app\neuro\provider;

use app\neuro\abstract\Provider;
use app\neuro\components\Client;
use app\neuro\exceptions\FailGenerationException;
use app\neuro\interfaces\FileProviderInterface;
use Yii;
use yii\helpers\Inflector;

class RouterAiRu extends Provider implements FileProviderInterface
{
    /**
     * @return Client
     */
    public function getClient()
    {
        $options = [
            'headers' => [
                'Authorization' => 'Bearer ' . ($this->token),
                'Content-Type' => 'application/json'
            ],
            'provider' => self::class,
            'log_data' => $this->log_data,
        ];
        return new Client($options);
    }

    public function request($messages = [])
    {
        $maxTokens = $this->getMaxTokens() ?: 4000;
        $params = [
            'body' => json_encode([
                'temperature' => ((float)$this->getTemperature() ?: 1),
                'model' => $this->getModel()->code,
                'messages' => array_merge($this->prepareMessage($messages)),
                'max_tokens' => $maxTokens
            ])
        ];

        return json_decode(static::getClient()->post($this->url, $params)->getBody()->getContents());
    }

    public function ask($prompt, $messages = [])
    {
        $messages[] = ['role' => 'user', 'content' => $prompt];
        return $this->request($messages);
    }

    public function getName()
    {
        // TODO: Implement getName() method.
    }

    public function responseToFilePath($response)
    {
        if (empty($response->choices[0]->message->images[0])) {
            throw new FailGenerationException(json_encode($response));
        }
        $imageUrl = $response->choices[0]->message->images[0]->image_url->url;
        $pattern = '/^data:image\/(?P<ext>[a-zA-Z0-9]+);base64,(?P<data>.+)$/';

        if (preg_match($pattern, $imageUrl, $m)) {
            $ext = $m['ext'];
            $base64 = $m['data'];
            $filePath = Yii::getAlias('@app/runtime/cache/' . uniqid(Inflector::slug(get_class($this)) . '-') . '.' . $ext);
            file_put_contents($filePath, base64_decode($base64));
            return $filePath;
        }
        throw new FailGenerationException(json_encode($response));
    }
}