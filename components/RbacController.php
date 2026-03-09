<?php

namespace app\components;

use carono\yii2rbac\RoleManagerFilter;
use yii\web\Controller;

class RbacController extends Controller
{
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'access' => RoleManagerFilter::class
        ]);
    }
}