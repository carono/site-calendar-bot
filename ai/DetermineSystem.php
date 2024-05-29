<?php

namespace app\ai;

use ReflectionClass;

class DetermineSystem extends System
{

    protected static function getClasses()
    {
        $alias = '@app/ai';
        $namespace = strtr($alias, ['/' => '\\', '@' => '']);
        $dir = \Yii::getAlias($alias);
        $result = [];
        foreach (glob("$dir/*.php") as $file) {
            $class = $namespace . '\\' . pathinfo($file, PATHINFO_FILENAME);
            $reflect = new \ReflectionClass($class);
            if (!$reflect->isAbstract() && $reflect->isSubclassOf(Command::class)) {
                $result[] = $class;
            }
        }
        return $result;
    }

    public function getMessage()
    {
        /**
         * @var $classes Command[]
         */
        $classes = static::getClasses();
        $messages = [];
        foreach ($classes as $class) {
            $model = new ReflectionClass($class);
            if ($promnt = $model->getProperty('determine')->getDefaultValue()) {
                $messages[] = [
                    'role' => 'system',
                    'content' => 'Напиши type=' . $model->getShortName() . ', ' . $promnt
                ];
            }
        }
        return $messages;
    }
}