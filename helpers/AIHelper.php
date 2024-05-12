<?php

namespace app\helpers;

use app\models\gpt\DetermineDTO;
use app\models\gpt\GroupingDTO;
use app\models\gpt\TaskDTO;
use app\models\User;
use GuzzleHttp\Client;
use OpenAI;
use Yii;

class AIHelper
{
    public const TASK_CREATE = 'system:create-task';
    public const GROUPING = 'system:grouping';
    public const DETERMINE = 'system:determine';
    public const PLANNING = 'system:planning';
    public const FIND_TASK = 'system:find-task';

    protected static $features = [
        self::GROUPING => [
            'title' => 'Определение группы для задачи',
            'prompt' => 'Только если сообщение начинается с system:grouping, значит нужно из предоставленного списка составить массив:
        task_id: ID задачи
        title: текст задачи
        group: группу, к которой можно определить задачу'
        ],
        self::PLANNING => [
            'title' => 'Планирование даты и времени выполнения задачи, которая находится в списке активных задач',
            'prompt' => 'Только если сообщение начинается с system:planning и если найдена задача по названию в списке активные задач: 
        title: текст задачи
        task_id: ID задачи из списка активных задач 
        planned_at: дата и время выполнения в формате php:Y-m-d H:i:00'
        ],
        self::TASK_CREATE => [
            'title' => 'Если точно определить команду не удается, и в списке активных задач такой нет, то это создание новой задачи',
            'prompt' => 'Только если сообщение начинается с system:create-task, значит нужно сформировать задачу по тексту пользователя. По сообщению пользователя, ты должен определить: 
        title: текст задачи
        description: примерное описание задачи
        approximate: примерный срок выполнения задачи (на днях, в течение недели, потом)
        group: группа, к которой относится задача
        planned_at: дата и время выполнения в формате php:Y-m-d H:i:00'
        ],
        self::FIND_TASK => [
            'title' => 'Найди задачу из списка активных по сообщению',
            'prompt' => 'Только если сообщение начинается с system:find-task, найди ID задачи из списка активных задач по тексту сообщения: 
        title: текст задачи
        task_id: ID задачи из списка активных задач
        planned_at: дата и время выполнения в формате php:Y-m-d H:i:00'
        ],
    ];

    protected static function getTasksPrompt(User $user)
    {
        $activeTasks = $user->getTasks()->select(['id', 'title'])->andWhere(['finished_at' => null])->all();
        return implode("\r\n", array_map(function ($task) {
            return 'ID=' . $task->id . ' ' . trim($task->title);
        }, $activeTasks));

    }

    protected static function prepare(User $user)
    {
        $date = [
            "role" => "system",
            "content" => Yii::$app->formatter->asDate(time(), 'php:Сейчас d F Y, l')
        ];

        $tasks = [
            "role" => "system",
            "content" => "Активные задачи: \r\n" . static::getTasksPrompt($user)
        ];

        $groups = [
            "role" => "system",
            "content" => 'Доступные группы для распределения задач: ' . implode(', ', $user->getGroups()->select(['name'])->column())
        ];
        $type = [
            "role" => "system",
            "content" => 'отвечаешь только в JSON формате'
        ];


        $descriptions = [];
        foreach (static::$features as $feature) {
            $descriptions[] = [
                "role" => "system",
                "content" => $feature['prompt']
            ];
        }
        $descriptions[] = [
            'role' => 'system',
            'content' => static::featureGetCommand()
        ];

        return array_merge([
            $type,
            $date,
            $tasks,
            $groups
        ], $descriptions);
    }

    public static function ask($user, $question)
    {
        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => static::DETERMINE . "\r\n" . $question
                ]
            ])
        ]);
        return json_decode($response->choices[0]->message->content);
    }

    public static function planning($user, $question)
    {
        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => static::PLANNING . "\r\n" . $question
                ]
            ])
        ]);
        return json_decode($response->choices[0]->message->content, true);
    }

    public static function getClient()
    {
        return OpenAI::factory()
            ->withApiKey(Yii::$app->params['proxy-api']['token'])
            ->withBaseUri('api.proxyapi.ru/openai/v1')
            ->withHttpClient(new Client())
            ->make();
    }

    public static function createTask($user, $question)
    {
        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => static::TASK_CREATE . "\r\n" . $question
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
                    'content' => static::GROUPING . "\r\n" . implode("\r\n", $tasks)
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

    private static function featureGetCommand()
    {
        $message = [];
        foreach (static::$features as $command => $description) {
            $message[] = $command . ' - ' . $description['title'];
        }
        return "Только сообщение начинается с system:determine. Определи, какой type сообщения из доступных, иначе верни type=null: \r\n" . implode("\r\n", $message);
    }

    public static function determine(User $user, string $question)
    {
        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => static::DETERMINE . "\r\n" . $question
                ]
            ])
        ]);
        return new DetermineDTO(json_decode($response->choices[0]->message->content, true));
    }

    public static function findTask(User $user, string $question)
    {
        $response = static::getClient()->chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => array_merge(static::prepare($user), [
                [
                    'role' => 'user',
                    'content' => static::FIND_TASK . $question
                ]
            ])
        ]);
        return json_decode($response->choices[0]->message->content, true);
    }

}