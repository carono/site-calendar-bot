<?php

namespace app\helpers;

use app\models\gpt\GroupingDTO;
use app\models\gpt\TaskDTO;
use app\models\Task;
use app\models\User;
use GuzzleHttp\Client;
use OpenAI;
use Yii;
use yii\helpers\ArrayHelper;

class AIHelper
{
    protected static function prepare(User $user)
    {
        $date = [
            "role" => "system",
            "content" => Yii::$app->formatter->asDate(time(), 'php:Сейчас d F Y, l')
        ];
        $tasks = [
            "role" => "system",
            "content" => 'Текущие задачи: ' . implode(', ', $user->getTasks()->select(['title'])->andWhere(['finished_at' => null])->column())
        ];
        $groups = [
            "role" => "system",
            "content" => 'Доступные группы для распределения задач: ' . implode(', ', $user->getGroups()->select(['name'])->column())
        ];
        $type = [
            "role" => "system",
            "content" => 'отвечаешь только в JSON формате'
        ];

        $features = [];
        $features[] = "Ты система для обработки запросов, ты умеешь:";
        $features[] = "Сформировать задачу по тексту пользователя. По сообщению пользователя, ты должен определить: 
        title: текст задачи
        description: примерное описание задачи
        approximate: примерный срок выполнения задачи (на днях, в течение недели, потом)
        group: группа, к которой относится задача
        planned_at: дата и время выполнения в формате php:Y-m-d H:i:00";
        $features[] = "Распределение задач по группам, если есть ключевое слово system:grouping, значит нужно из предоставленного списка составить:
        task_id: ID задачи
        title: текст задачи
        group: группу, к которой можно определить задачу";

        $features = array_map(function ($item) {
            return "* " . $item;
        }, $features);

        $description = [
            "role" => "system",
            "content" => implode("\r\n", $features)
        ];

        return [
            $type,
            $date,
            $tasks,
            $groups,
            $description
        ];
    }

    public static function getClient()
    {
        return OpenAI::factory()
            ->withApiKey(Yii::$app->params['proxy-api']['token'])
            ->withBaseUri('api.proxyapi.ru/openai/v1')
            ->withHttpClient(new Client())
            ->make();
    }

    public static function ask($user, $question)
    {
        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => $question
                ]
            ])
        ]);

        return new TaskDTO(json_decode($response->choices[0]->message->content, true));
    }

    /**
     * @param User $user
     * @param array $tasks
     * @return GroupingDTO[]
     */
    public static function grouping(User $user, array $tasks)
    {
        $tasks = array_map(function ($task) {
            return 'ID=' . $task->id . '. ' . $task->title;
        }, $tasks);

        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => "system:grouping\r\n" . implode("\r\n", $tasks)
                ]
            ])
        ]);
        $json = current(json_decode($response->choices[0]->message->content, true));
        $result = [];
        foreach ($json as $item) {
            $result[] = new GroupingDTO($item);
        }
        return $result;
    }
}