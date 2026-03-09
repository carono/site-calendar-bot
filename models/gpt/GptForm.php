<?php

namespace app\models\gpt;

use app\helpers\AIHelper;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class GptForm extends Model
{
    public $image;
    public $prompt;
    public $system;
    public $answer;

    public function rules()
    {
        return [
            [['image', 'prompt', 'system'], 'safe'],
            [['image'], 'file']
        ];
    }

    public function init()
    {
        $this->system = Yii::$app->cache->get('gpt-form-system');
        $this->prompt = Yii::$app->cache->get('gpt-form-prompt');
    }

    public function ask()
    {
        $messages = [];
        if ($this->system) {
            $messages[] = [
                'role' => 'system',
                'content' => $this->system
            ];
            Yii::$app->cache->set('gpt-form-system', $this->system);
        }

        if ($images = UploadedFile::getInstances($this, 'image')) {
            foreach ($images as $image) {
                $file = file_get_contents($image->tempName);
                $base64 = base64_encode($file);
                $messages[] = [
                    'role' => 'user',
                    'content' => [
                        ["type" => "image_url", "image_url" => ["url" => "data:image/png;base64,$base64"]]
                    ]
                ];
            }
        }

        if ($this->prompt) {
            $messages[] = [
                'role' => 'user',
                'content' => $this->prompt
            ];
            Yii::$app->cache->set('gpt-form-prompt', $this->prompt);
        }

        $request = [
//            'model' => 'gpt-4o-mini',
            'model' => 'gpt-4o',
            'messages' => $messages
        ];

        $response = AIHelper::getClient()->chat()->create($request);
        $this->answer = $response->choices[0]->message->content;
    }
}