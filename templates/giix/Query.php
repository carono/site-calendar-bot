<?php


namespace app\templates\giix;


use Nette\PhpGenerator\Method;

class Query extends \carono\giix\templates\model\Query
{
    /**
     * @param Method $method
     * @return bool
     */
    public function notDeleted($method)
    {
        $columns = $this->params['tableSchema']->columns;
        $tableName = $this->params['tableName'];
        if (isset($columns['deleted_at'])) {
            $method->addComment('@return $this');
            $code = <<<PHP
return \$this->andWhere(['{{%$tableName}}.[[deleted_at]]' => null]);
PHP;
            $method->addBody($code);
        } else {
            return false;
        }
    }

    /**
     * @param Method $method
     * @return bool
     */
    public function byCompanies($method)
    {
        $columns = $this->params['tableSchema']->columns;
        $tableName = $this->params['tableName'];
        if (!isset($columns['company_id'])) {
            return false;
        }
        $body = <<<PHP
  return \$this->andWhere(['{{%$tableName}}.[[company_id]]' => \$ids ?: \Yii::\$app->user->companyid]);
PHP;

        $method->addParameter('ids', []);
        $method->addBody($body);
        $method->addComment('@param array|integer $ids');
        $method->addComment('@return $this');
    }

    /**
     * @param Method $method
     */
    public function available($method)
    {
        $columns = $this->params['tableSchema']->columns;
        $method->addComment('@return $this');
        $chain = [];
        if (isset($columns['deleted_at'])) {
            $chain[] = 'notDeleted()';
        }
        if (isset($columns['company_id'])) {
            $chain[] = 'byCompanies()';
        }

        $code = implode('->', $chain);
        $code = $code ? '->' . $code : $code;

        $php = <<<PHP
    return \$this$code;
PHP;

        $method->addBody($php);
    }

    public function filter($method)
    {
        $method->addComment('@var array|\yii\db\ActiveRecord $model');
        $method->addComment('@return $this');
        $method->addParameter('model', null);
        $this->phpNamespace->addUse('carono\yii2helpers\QueryHelper');
        $regular = <<<PHP
if (\$model instanceof \app\interfaces\Search){
    \$model->updateQuery(\$this);
} elseif (\$model instanceof \yii\db\ActiveRecord){
    QueryHelper::regular(\$model, \$this);
}
PHP;
        $method->addBody($regular);
        $method->addBody('return $this;');
    }

}