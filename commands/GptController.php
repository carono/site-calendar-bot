<?php

namespace app\commands;

use app\helpers\AIHelper;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;

class GptController extends Controller
{
    /**
     * Тестирование модели через OpenAI Compatible API.
     *
     * @param string $question Вопрос к модели
     * @param string|null $model  Идентификатор модели (по умолчанию DEFAULT_MODEL)
     *
     * Примеры:
     *   php yii gpt/test "Привет!"
     *   php yii gpt/test "Привет!" openai/gpt-5.4-pro
     *   php yii gpt/test "Привет!" anthropic/claude-sonnet-4.6
     *   php yii gpt/test "Привет!" x-ai/grok-4
     */
    public function actionTest(string $question = 'Привет! Представься.', ?string $model = null): int
    {
        $model = $model ?? AIHelper::DEFAULT_MODEL;

        if (!array_key_exists($model, AIHelper::MODELS)) {
            $this->stderr("Неизвестная модель: $model\n", Console::FG_RED);
            $this->stdout("Доступные модели:\n", Console::BOLD);
            foreach (AIHelper::MODELS as $id => $name) {
                $this->stdout("  $id  ($name)\n");
            }
            return ExitCode::DATAERR;
        }

        $this->stdout("Модель : ", Console::BOLD);
        $this->stdout(AIHelper::MODELS[$model] . " [$model]\n", Console::FG_CYAN);
        $this->stdout("Вопрос : $question\n\n", Console::BOLD);

        try {
            $response = AIHelper::start($model)->ask($question);
            $answer = $response->choices[0]->message->content ?? '(пустой ответ)';
            $this->stdout("Ответ:\n", Console::BOLD);
            $this->stdout($answer . "\n");
        } catch (\Throwable $e) {
            $this->stderr("Ошибка: " . $e->getMessage() . "\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        return ExitCode::OK;
    }

    /**
     * Список доступных моделей.
     */
    public function actionModels(): int
    {
        $this->stdout("Доступные модели:\n", Console::BOLD);
        foreach (AIHelper::MODELS as $id => $name) {
            $this->stdout("  ");
            $this->stdout(str_pad($id, 35), Console::FG_CYAN);
            $this->stdout($name . "\n");
        }
        return ExitCode::OK;
    }
}