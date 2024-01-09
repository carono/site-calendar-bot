<?php

namespace app\exceptions;

use Throwable;
use yii\base\Model;

class ValidationException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        if ($message instanceof Model) {
            $message = current($message->getFirstErrors());
        }
        parent::__construct($message, $code, $previous);
    }
}