<?php

namespace app\controllers;

use app\helpers\AIHelper;
use app\jobs\AskModelJob;
use app\models\gpt\GptForm;
use Yii;
use yii\web\Controller;
use yii\web\Response;
use yii\web\UploadedFile;

class GptController extends Controller
{

    public function actionIndex()
    {
        $model = new GptForm();

        return $this->render('index', ['model' => $model]);
    }

    public function actionConsensus()
    {
        $model = new GptForm();
        $model->load(Yii::$app->request->post());
        $model->checkConsensus();

        return $this->render('index', ['model' => $model]);
    }

    /**
     * Принимает форму, сохраняет данные и пушит по одному job на каждую выбранную модель в RabbitMQ.
     * Отвечает немедленно, ответы появляются асинхронно по мере обработки воркером.
     */
    public function actionPush()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $models = (array)Yii::$app->request->post('models', []);
        $system = Yii::$app->request->post('system', '');
        $prompt = Yii::$app->request->post('prompt', '');

        $validModels = array_values(array_filter($models, fn($m) => isset(AIHelper::MODELS[$m])));
        if (!$validModels) {
            return ['error' => 'Не выбрано ни одной допустимой модели'];
        }

        // Конвертируем загруженные картинки в base64 (для хранения в payload job)
        $imagesBase64 = [];
        foreach (UploadedFile::getInstancesByName('image') as $image) {
            $imagesBase64[] = base64_encode(file_get_contents($image->tempName));
        }

        // Сохраняем контекст в кеш и сбрасываем прошлые ответы
        Yii::$app->cache->set('gpt-form-system', $system);
        Yii::$app->cache->set('gpt-form-prompt', $prompt);
        Yii::$app->cache->set('gpt-form-models', $validModels);
        Yii::$app->cache->set('gpt-form-pending', $validModels);
        Yii::$app->cache->delete('gpt-form-answers');

        // Пушим по одному job на каждую модель
        foreach ($validModels as $modelId) {
            $job = new AskModelJob();
            $job->modelId = $modelId;
            $job->system = $system;
            $job->prompt = $prompt;
            $job->imagesBase64 = $imagesBase64;
            Yii::$app->queue->push($job);
        }

        return ['queued' => $validModels];
    }

    /**
     * Возвращает текущее состояние ответов из кеша (GET, используется для polling).
     */
    public function actionStatus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        return [
            'answers' => Yii::$app->cache->get('gpt-form-answers') ?: [],
            'pending' => Yii::$app->cache->get('gpt-form-pending') ?: [],
        ];
    }

}
