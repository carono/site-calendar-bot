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

$this->registerJsFile('https://cdn.jsdelivr.net/npm/marked/marked.min.js', ['position' => \yii\web\View::POS_HEAD]);

$this->registerCss(<<<CSS
@keyframes gpt-spin { to { transform: rotate(360deg); } }
.icon-spin { display:inline-block; animation:gpt-spin 1s linear infinite; }
.answer-body { white-space:pre-wrap; word-break:break-word; }

/* Markdown-рендер консенсуса */
.consensus-md h1,.consensus-md h2,.consensus-md h3 { margin-top:16px; margin-bottom:8px; font-weight:600; border-bottom:1px solid #d6e9c6; padding-bottom:4px; }
.consensus-md h1 { font-size:1.4em; }
.consensus-md h2 { font-size:1.2em; }
.consensus-md h3 { font-size:1.05em; }
.consensus-md p  { margin:0 0 10px; line-height:1.6; }
.consensus-md ul,.consensus-md ol { padding-left:24px; margin-bottom:10px; }
.consensus-md li { margin-bottom:4px; line-height:1.6; }
.consensus-md strong { font-weight:700; }
.consensus-md em { font-style:italic; }
.consensus-md code { background:#d6e9c6; padding:1px 5px; border-radius:3px; font-family:monospace; font-size:.92em; }
.consensus-md pre { background:#dff0d8; border:1px solid #d6e9c6; border-radius:4px; padding:10px; overflow-x:auto; }
.consensus-md pre code { background:none; padding:0; }
.consensus-md blockquote { border-left:4px solid #5cb85c; padding:6px 12px; margin:0 0 10px; color:#555; background:#f0faf0; border-radius:0 4px 4px 0; }
.consensus-md hr { border:0; border-top:1px solid #d6e9c6; margin:14px 0; }
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
echo ' ';
echo Html::button(
    '<span class="glyphicon glyphicon-stop"></span> Прервать',
    ['class' => 'btn btn-danger', 'id' => 'stop-btn', 'style' => 'display:none']
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
$showConsensusForm = $cachedAnswers && !$cachedPending;
echo "<div id='consensus-block'" . ($showConsensusForm ? '' : " style='display:none'") . ">";
if ($showConsensusForm) {
    echo "<div class='panel panel-default'>";
    echo "<div class='panel-heading'><strong>Анализ консенсуса</strong></div>";
    echo "<div class='panel-body'>";
    echo Html::dropDownList('consensus_model', $model->consensusModel, AIHelper::MODELS, ['class' => 'form-control', 'id' => 'consensus-model-select', 'style' => 'margin-bottom:8px']);
    echo Html::button(
        '<span class="glyphicon glyphicon-check"></span> Определить консенсус',
        ['class' => 'btn btn-success', 'id' => 'consensus-btn']
    );
    echo "</div></div>";
}
echo "</div>";

echo "</div>"; // #answers-area

// --- Consensus result ---
$arbitrName   = Html::encode(AIHelper::MODELS[$model->consensusModel] ?? $model->consensusModel);
$rawMd        = Html::encode($model->consensusResult); // для data-атрибута (HTML-escaped)
echo "<div id='consensus-result'" . ($model->consensusResult ? '' : " style='display:none'") . ">";
echo "<div class='panel panel-success'>";
echo "<div class='panel-heading'><strong>Вывод консенсуса</strong> <small class='text-muted' id='consensus-arbitr'>— арбитр: {$arbitrName}</small></div>";
echo "<div class='panel-body consensus-md' id='consensus-body' data-md='{$rawMd}'></div>";
echo "</div></div>";

// --- JS ---
$pushUrl      = Url::to(['/gpt/push']);
$statusUrl    = Url::to(['/gpt/status']);
$consensusUrl = Url::to(['/gpt/consensus']);
$allModels    = Json::encode(AIHelper::MODELS);
$csrfParam    = Yii::$app->request->csrfParam;
$initialPending = Json::encode($cachedPending);
$stopUrl        = Url::to(['/gpt/stop']);
$jsPushUrl      = Json::encode($pushUrl);
$jsStatusUrl    = Json::encode($statusUrl);
$jsConsensusUrl = Json::encode($consensusUrl);
$jsStopUrl      = Json::encode($stopUrl);
$jsCsrfParam    = Json::encode($csrfParam);

$this->registerJs(<<<JS
(function () {
    var MODELS         = {$allModels};
    var pushUrl        = {$jsPushUrl};
    var statusUrl      = {$jsStatusUrl};
    var consensusUrl   = {$jsConsensusUrl};
    var stopUrl        = {$jsStopUrl};
    var csrfParam      = {$jsCsrfParam};
    var initialPending = {$initialPending};

    var pollTimer   = null;
    var pollTimeout = 300000; // 5 минут максимум
    var pollStart   = null;

    function escHtml(str) {
        var d = document.createElement('div');
        d.appendChild(document.createTextNode(String(str)));
        return d.innerHTML;
    }

    function renderMarkdown(el, mdText) {
        if (typeof marked !== 'undefined') {
            el.innerHTML = marked.parse(mdText);
        } else {
            el.textContent = mdText;
        }
    }

    // Рендерим markdown если на странице уже есть закешированный результат
    (function () {
        var body = document.getElementById('consensus-body');
        if (body && body.dataset.md) {
            // data-md хранится HTML-encoded, декодируем через textarea
            var ta = document.createElement('textarea');
            ta.innerHTML = body.dataset.md;
            renderMarkdown(body, ta.value);
        }
    })();

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
        // Если форма ещё не построена — создаём её
        if (!document.getElementById('consensus-model-select')) {
            var opts = Object.entries(MODELS).map(function(e) {
                return '<option value="' + escHtml(e[0]) + '">' + escHtml(e[1]) + '</option>';
            }).join('');
            block.innerHTML =
                '<div class="panel panel-default">' +
                '<div class="panel-heading"><strong>Анализ консенсуса</strong></div>' +
                '<div class="panel-body">' +
                '<select id="consensus-model-select" class="form-control" style="margin-bottom:8px">' + opts + '</select>' +
                '<button id="consensus-btn" class="btn btn-success"><span class="glyphicon glyphicon-check"></span> Определить консенсус</button>' +
                '</div></div>';
        }
        block.style.display = '';
    }

    function handleConsensusClick() {
        var btn = document.getElementById('consensus-btn');
        var select = document.getElementById('consensus-model-select');
        if (!btn || !select) return;

        btn.disabled = true;
        btn.innerHTML = '<span class="glyphicon glyphicon-refresh icon-spin"></span> Анализирую...';

        var resultDiv  = document.getElementById('consensus-result');
        var resultBody = document.getElementById('consensus-body');
        var arbitrEl   = document.getElementById('consensus-arbitr');
        resultDiv.style.display = 'none';

        var data = new FormData();
        data.append(csrfParam, getcsrf());
        data.append('GptForm[consensusModel]', select.value);

        fetch(consensusUrl, { method: 'POST', body: data })
            .then(function(r) { return r.json(); })
            .then(function(res) {
                if (res.result) {
                    renderMarkdown(resultBody, res.result);
                    arbitrEl.textContent    = '— арбитр: ' + (res.modelName || select.value);
                    resultDiv.style.display = '';
                    resultDiv.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            })
            .catch(function(err) { alert('Ошибка: ' + err.message); })
            .finally(function() {
                btn.disabled = false;
                btn.innerHTML = '<span class="glyphicon glyphicon-check"></span> Определить консенсус';
            });
    }

    document.addEventListener('click', function(e) {
        if (e.target && e.target.closest && e.target.closest('#consensus-btn')) {
            handleConsensusClick();
        } else if (e.target && e.target.id === 'consensus-btn') {
            handleConsensusClick();
        }
    });

    function setStopBtn(visible) {
        var btn = document.getElementById('stop-btn');
        if (btn) btn.style.display = visible ? '' : 'none';
    }

    function stopPolling() {
        if (pollTimer) { clearInterval(pollTimer); pollTimer = null; }
        setStopBtn(false);
    }

    function startPolling(expectedModels) {
        setStopBtn(true);
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

    document.getElementById('stop-btn').addEventListener('click', async function () {
        var btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="glyphicon glyphicon-refresh icon-spin"></span> Останавливаю...';

        stopPolling();

        // Пометить pending-карточки как прерванные
        (Yii.app ? [] : document.querySelectorAll('[id^="ans_"]')).forEach &&
        document.querySelectorAll('[id^="ans_"]').forEach(function (el) {
            if (el.querySelector('.icon-spin')) {
                el.innerHTML = '<span class="text-muted">— прервано —</span>';
                var panel = el.closest ? el.closest('.panel') : el.parentElement;
                if (panel) panel.className = panel.className.replace(/panel-(default|info|danger)/g, 'panel-warning');
            }
        });

        var data = new FormData();
        data.append(csrfParam, getcsrf());
        try {
            await fetch(stopUrl, { method: 'POST', body: data });
        } catch (e) { /* игнорируем */ }

        btn.disabled = false;
        btn.innerHTML = '<span class="glyphicon glyphicon-stop"></span> Прервать';
        btn.style.display = 'none';
        document.getElementById('submit-btn').disabled = false;
    });

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
