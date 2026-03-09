<?php

use app\helpers\AIHelper;
use app\models\gpt\GptForm;
use kartik\file\FileInput;
use yii\helpers\Html;
use yii\helpers\Json;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

/**
 * @var GptForm $model
 * @var yii\web\View $this
 */

$selectedModels = (array)$model->models;
$cachedAnswers  = $model->answers;
$cachedPending  = Yii::$app->cache->get('gpt-form-pending') ?: [];

$this->registerCss(<<<CSS
@keyframes gpt-spin { to { transform: rotate(360deg); } }
.icon-spin { display:inline-block; animation:gpt-spin 1s linear infinite; }
.answer-body { white-space:pre-wrap; word-break:break-word; }
CSS);

// --- Main form ---
$form = ActiveForm::begin([
    'id'      => 'gpt-form',
    'options' => ['enctype' => 'multipart/form-data'],
]);

echo "<div class='panel panel-default'>";
echo "<div class='panel-heading'><strong>Модели для опроса</strong></div>";
echo "<div class='panel-body'><div class='row'>";
foreach (AIHelper::MODELS as $modelId => $modelName) {
    $checked = in_array($modelId, $selectedModels);
    $safeId  = 'chk_' . preg_replace('/[^a-z0-9]/i', '_', $modelId);
    echo "<div class='col-sm-4'><div class='checkbox'><label>";
    echo Html::checkbox('GptForm[models][]', $checked, ['value' => $modelId, 'id' => $safeId]);
    echo ' ' . Html::encode($modelName);
    echo "</label></div></div>";
}
echo "</div></div></div>";

echo "<div class='row'>";
echo "<div class='col-lg-6'>";
echo $form->field($model, 'system')->textarea(['rows' => 5]);
echo "</div><div class='col-lg-6'>";
echo $form->field($model, 'prompt')->textarea(['rows' => 5]);
echo "</div></div>";

echo FileInput::widget([
    'model'     => $model,
    'attribute' => 'image[]',
    'options'   => ['multiple' => true, 'id' => 'gpt-image-input'],
]);

echo Html::submitButton(
    '<span class="glyphicon glyphicon-send"></span> Спросить все модели',
    ['class' => 'btn btn-primary', 'id' => 'submit-btn']
);

ActiveForm::end();

// --- Answers area ---
echo "<div id='answers-area' style='margin-top:24px'>";

// Определяем, что показывать: ответы из кеша и/или спиннеры для pending
$showAnswers = $cachedAnswers || $cachedPending;

if ($showAnswers) {
    echo "<h4>Ответы моделей</h4>";
    echo "<div class='row' id='answer-cards'>";
    $displayModels = array_unique(array_merge(array_keys($cachedAnswers), $cachedPending));
    foreach ($displayModels as $modelId) {
        $modelName = AIHelper::MODELS[$modelId] ?? $modelId;
        $safeKey   = preg_replace('/[^a-z0-9]/i', '_', $modelId);
        $isPending = in_array($modelId, $cachedPending);
        $panelClass = $isPending ? 'panel-default' : 'panel-info';
        $bodyContent = $isPending
            ? '<span class="glyphicon glyphicon-refresh icon-spin"></span> Ожидаю ответа...'
            : Html::encode($cachedAnswers[$modelId]);
        echo "<div class='col-lg-4' id='col_{$safeKey}'>";
        echo "<div class='panel {$panelClass}'>";
        echo "<div class='panel-heading'><strong>" . Html::encode($modelName) . "</strong></div>";
        echo "<div class='panel-body answer-body' id='ans_{$safeKey}'>{$bodyContent}</div>";
        echo "</div></div>";
    }
    echo "</div>";
} else {
    echo "<div class='row' id='answer-cards'></div>";
}

// Consensus block
if ($cachedAnswers && !$cachedPending) {
    // Answers ready, no pending — show consensus form
    echo "<div id='consensus-block'>";
    echo "<div class='panel panel-default'>";
    echo "<div class='panel-heading'><strong>Анализ консенсуса</strong></div>";
    echo "<div class='panel-body'>";
    $cForm = ActiveForm::begin(['action' => Url::to(['/gpt/consensus']), 'method' => 'post', 'id' => 'consensus-form']);
    echo $cForm->field($model, 'consensusModel')->dropDownList(AIHelper::MODELS)->label('Модель-арбитр');
    echo Html::submitButton(
        '<span class="glyphicon glyphicon-check"></span> Определить консенсус',
        ['class' => 'btn btn-success']
    );
    ActiveForm::end();
    echo "</div></div></div>";
} else {
    echo "<div id='consensus-block' style='display:none'></div>";
}

echo "</div>"; // #answers-area

// --- Consensus result (from POST) ---
if ($model->consensusResult) {
    $arbitrName = Html::encode(AIHelper::MODELS[$model->consensusModel] ?? $model->consensusModel);
    echo "<div class='panel panel-success' id='consensus-result'>";
    echo "<div class='panel-heading'><strong>Вывод консенсуса</strong> ";
    echo "<small class='text-muted'>— арбитр: {$arbitrName}</small></div>";
    echo "<div class='panel-body answer-body'>" . Html::encode($model->consensusResult) . "</div>";
    echo "</div>";
}

// --- JS ---
$pushUrl      = Url::to(['/gpt/push']);
$statusUrl    = Url::to(['/gpt/status']);
$consensusUrl = Url::to(['/gpt/consensus']);
$allModels    = Json::encode(AIHelper::MODELS);
$csrfParam    = Yii::$app->request->csrfParam;

// Pre-build consensus form HTML for dynamic injection
ob_start();
$cFormJs = ActiveForm::begin(['action' => $consensusUrl, 'method' => 'post', 'id' => 'consensus-form']);
echo $cFormJs->field($model, 'consensusModel')->dropDownList(AIHelper::MODELS)->label('Модель-арбитр');
echo Html::submitButton(
    '<span class="glyphicon glyphicon-check"></span> Определить консенсус',
    ['class' => 'btn btn-success']
);
ActiveForm::end();
$consensusFormHtml = Json::encode(ob_get_clean());

// If there are still pending models on page load — start polling immediately
$initialPending = Json::encode($cachedPending);

$jsPushUrl    = Json::encode($pushUrl);
$jsStatusUrl  = Json::encode($statusUrl);
$jsCsrfParam  = Json::encode($csrfParam);

$this->registerJs(<<<JS
(function () {
    var MODELS            = {$allModels};
    var pushUrl           = {$jsPushUrl};
    var statusUrl         = {$jsStatusUrl};
    var consensusFormHtml = {$consensusFormHtml};
    var csrfParam         = {$jsCsrfParam};
    var initialPending    = {$initialPending};

    var pollTimer   = null;
    var pollTimeout = 300000; // 5 минут максимум
    var pollStart   = null;

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    function safeKey(modelId) {
        return modelId.replace(/[^a-z0-9]/gi, '_');
    }

    function getcsrf() {
        var el = document.querySelector('input[name="' + csrfParam + '"]');
        return el ? el.value : '';
    }

    function makeCardHtml(modelId, modelName, state, text) {
        var key = safeKey(modelId);
        var panelClass = state === 'done' ? 'panel-info' : (state === 'error' ? 'panel-danger' : 'panel-default');
        var body = state === 'loading'
            ? '<span class="glyphicon glyphicon-refresh icon-spin"></span> Ожидаю ответа...'
            : escHtml(text || '');
        return '<div class="col-lg-4" id="col_' + key + '">' +
            '<div class="panel ' + panelClass + '">' +
            '<div class="panel-heading"><strong>' + escHtml(modelName) + '</strong></div>' +
            '<div class="panel-body answer-body" id="ans_' + key + '">' + body + '</div>' +
            '</div></div>';
    }

    function setCardDone(modelId, text, isError) {
        var key   = safeKey(modelId);
        var body  = document.getElementById('ans_' + key);
        if (!body) return;
        var panel = body.closest ? body.closest('.panel') : body.parentElement;
        body.textContent = text;
        if (panel) {
            panel.className = panel.className.replace(/panel-(default|info|danger)/g, isError ? 'panel-danger' : 'panel-info');
        }
    }

    function showConsensusForm() {
        var block = document.getElementById('consensus-block');
        if (!block) return;
        block.innerHTML =
            '<div class="panel panel-default">' +
            '<div class="panel-heading"><strong>Анализ консенсуса</strong></div>' +
            '<div class="panel-body">' + consensusFormHtml + '</div></div>';
        block.style.display = '';
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
    }

    function startPolling(expectedModels) {
        stopPolling();
        pollStart = Date.now();

        pollTimer = setInterval(async function () {
            if (Date.now() - pollStart > pollTimeout) {
                stopPolling();
                return;
            }

            try {
                var resp = await fetch(statusUrl);
                var data = await resp.json();
                var answers = data.answers || {};
                var pending = data.pending || [];

                // Update cards that now have answers
                expectedModels.forEach(function (modelId) {
                    if (modelId in answers) {
                        var text    = answers[modelId];
                        var isError = text.indexOf('Ошибка:') === 0;
                        setCardDone(modelId, text, isError);
                    }
                });

                // All done?
                if (pending.length === 0) {
                    stopPolling();
                    document.getElementById('submit-btn').disabled = false;
                    showConsensusForm();
                }
            } catch (e) {
                // Network error — keep polling
            }
        }, 2000);
    }

    // If page loaded with pending models (worker still running) — resume polling
    if (initialPending.length > 0) {
        startPolling(initialPending);
    }

    // Form submit
    document.getElementById('gpt-form').addEventListener('submit', async function (e) {
        e.preventDefault();

        var fd             = new FormData(this);
        var selectedModels = fd.getAll('GptForm[models][]');
        var system         = fd.get('GptForm[system]') || '';
        var prompt         = fd.get('GptForm[prompt]') || '';

        if (!selectedModels.length) {
            alert('Выберите хотя бы одну модель');
            return;
        }

        stopPolling();

        var submitBtn = document.getElementById('submit-btn');
        submitBtn.disabled = true;

        // Hide old consensus/result
        var oldResult = document.getElementById('consensus-result');
        if (oldResult) oldResult.style.display = 'none';
        var consensusBlock = document.getElementById('consensus-block');
        consensusBlock.style.display = 'none';
        consensusBlock.innerHTML = '';

        // Build spinner cards
        var answersArea = document.getElementById('answers-area');
        var heading = answersArea.querySelector('h4');
        if (!heading) {
            answersArea.insertAdjacentHTML('afterbegin', '<h4>Ответы моделей</h4>');
        }
        var cardsRow = document.getElementById('answer-cards');
        cardsRow.innerHTML = '';
        selectedModels.forEach(function (modelId) {
            cardsRow.innerHTML += makeCardHtml(modelId, MODELS[modelId] || modelId, 'loading');
        });

        // Push all jobs to queue
        var pushData = new FormData();
        pushData.append(csrfParam, getcsrf());
        pushData.append('system', system);
        pushData.append('prompt', prompt);
        selectedModels.forEach(function (m) { pushData.append('models[]', m); });

        var imageInput = document.getElementById('gpt-image-input');
        if (imageInput && imageInput.files) {
            for (var f = 0; f < imageInput.files.length; f++) {
                pushData.append('image[]', imageInput.files[f]);
            }
        }

        try {
            var resp   = await fetch(pushUrl, { method: 'POST', body: pushData });
            var result = await resp.json();
            if (result.error) {
                alert(result.error);
                submitBtn.disabled = false;
                return;
            }
            // Start polling for answers
            startPolling(result.queued);
        } catch (err) {
            alert('Ошибка отправки: ' + err.message);
            submitBtn.disabled = false;
        }
    });
})();
JS);
?>
