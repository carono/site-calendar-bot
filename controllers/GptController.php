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
        if (Yii::$app->request->post()) {
            return $this->refresh();
        }

        return $this->render('index', ['model' => $model]);
    }

    public function actionConsensus()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        $model = new GptForm();
        $model->load(Yii::$app->request->post());
        $model->checkConsensus();

        return [
            'result' => $model->consensusResult,
            'modelName' => AIHelper::MODELS[$model->consensusModel] ?? $model->consensusModel,
        ];
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

        // Новая сессия — старые jobs от предыдущих вопросов проигнорируют свои результаты
        $sessionId = uniqid('', true);

        Yii::$app->cache->set('gpt-form-session', $sessionId);
        Yii::$app->cache->set('gpt-form-system', $system);
        Yii::$app->cache->set('gpt-form-prompt', $prompt);
        Yii::$app->cache->set('gpt-form-models', $validModels);
        Yii::$app->cache->set('gpt-form-pending', $validModels);
        Yii::$app->cache->delete('gpt-form-answers');

        // Пушим по одному job на каждую модель
        foreach ($validModels as $modelId) {
            $job            = new AskModelJob();
            $job->modelId   = $modelId;
            $job->sessionId = $sessionId;
            $job->system    = $system;
            $job->prompt    = $prompt;
            $job->imagesBase64 = $imagesBase64;
            Yii::$app->queue->push($job);
        }

        return ['queued' => $validModels];
    }

    /**
     * Прерывает обработку: очищает очередь RabbitMQ и сбрасывает pending-список.
     */
    public function actionStop()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;

        /** @var \yii\queue\amqp\Queue $queue */
        $queue = Yii::$app->queue;

        try {
            $connection = new \PhpAmqpLib\Connection\AMQPStreamConnection(
                $queue->host, $queue->port, $queue->user, $queue->password, $queue->vhost
            );
            $channel = $connection->channel();
            $channel->queue_purge($queue->queueName);
            $channel->close();
            $connection->close();
        } catch (\Throwable $e) {
            Yii::error('Stop queue error: ' . $e->getMessage());
        }

        // Инвалидируем сессию — летящие jobs увидят несовпадение и выбросят результаты
        Yii::$app->cache->set('gpt-form-session', uniqid('', true));
        Yii::$app->cache->delete('gpt-form-pending');

        return ['ok' => true];
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
