<?php


namespace app\components;


use app\telegram\ChatJoinRequest;
use carono\telegram\abs\Command;
use carono\telegram\helpers\StringHelper;
use TelegramBot\Api\Types\ReplyKeyboardMarkup;
use Yii;
use yii\db\Expression;

class Bot extends \carono\telegram\Bot
{
    public $chat_id;

    public static function getAuthKeyboard()
    {
        $keyboardParams = [];

        $keyboardParams[] = [
            ['text' => 'Настройки'],
            ['text' => 'Техподдержка'],
        ];

        return new ReplyKeyboardMarkup($keyboardParams);
    }

    public function init()
    {
        $this->token = \Yii::$app->params['telegram']['token'];
        $this->name = \Yii::$app->params['telegram']['name'];
        $this->buttonsFolder = Yii::getAlias('@app/telegram/buttons');
        $this->commandsFolder = Yii::getAlias('@app/telegram/commands');
        static::setCacheFolder(Yii::getAlias('@runtime/cache/telegram'));
        parent::init();
    }

    public function getFromId()
    {
        if (isset($this->message)) {
            return $this->message->from->id;
        }
        if (isset($this->callback_query)) {
            return $this->callback_query->from->id;
        }
        return $this->chat_id;
    }

    public function process()
    {
        $this->init();

        if (!empty($this->chat_join_request)) {
            $model = new ChatJoinRequest();
            $model->handle($this);
            return '';
        }

        if (!empty($this->callback_query)) {
            $data = $this->callback_query->data;
            $arr = explode('?', $data);
            if (isset($arr[1])) {
                $params = StringHelper::parseQuery($arr[1]);
            } else {
                $params = [];
            }
            $command = explode('/', $arr[0]);
            $button = $command[0];
            $file = $this->buttonsFolder . DIRECTORY_SEPARATOR . StringHelper::camelize($button) . '.php';
            $class = StringHelper::getClassFromFile($file);

            $method = StringHelper::camelize($command[1]);
            if (class_exists($class)) {
                $class::run($method, array_merge([$this], $params));
            }
        }

        $dir = $this->commandsFolder;
        foreach (glob("$dir/*.php") as $file) {
            $class = StringHelper::getClassFromFile($file);
            $reflect = new \ReflectionClass($class);
            if (!$reflect->isAbstract() && $reflect->isSubclassOf(Command::class)) {
                $command = new $class;
                call_user_func([$command, 'register'], $this);
            }
        }

        if (empty($this->callback_query) && !empty($this->message)) {
            $key = json_encode(['ask', $this->message->from->id]);
            if ($closureData = static::getCacheValue($key)) {
                $closureData = \Opis\Closure\unserialize($closureData);
                if ($closureData['retries'] < 0) {
                    static::getCache()->delete($key);
                } else {
                    $closureData['retries'] -= 1;
                    static::setCacheValue($key, \Opis\Closure\serialize($closureData));
                }
                if (call_user_func($closureData['closure'], $this, $this->message->text ?? '')) {
                    static::getCache()->delete($key);
                }

                return '';
            }

            if (empty($this->message)) {
                return '';
            }

            $text = $this->message->text ?? '';
            Yii::$app->db
                ->createCommand()
                ->insert('{{%telegram_log}}', [
                    'chat_id' => $this->getFromId(),
                    'message' => $text,
                    'is_request' => true,
                    'created_at' => new Expression('NOW()')
                ])
                ->execute();


            foreach ($this->hears as $data) {
                $message = $data['message'];

                if ($data['personally'] && mb_strpos($text, $this->name, 0, 'UTF-8') === false && $this->message->chat->type !== 'private') {
                    continue;
                }
                if (mb_strpos($text, $message, 0, 'UTF-8') !== false || $message === '*') {
                    call_user_func($data['closure'], $this);
                }
            }
        }

        return '';
    }
}