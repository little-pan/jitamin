<div class="page-header">
<ul class="nav nav-tabs">
    <li <?= $this->app->setActive('TaskViewController', 'show') ?>>
            <?= $this->url->link('<i class="fa fa-newspaper-o"></i> '.t('Summary'), 'TaskViewController', 'show', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>
    </li>
    <li <?= $this->app->setActive('ActivityController', 'task') ?>>
            <?= $this->url->link('<i class="fa fa-history"></i> '.t('Activities'), 'ActivityController', 'task', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>
    </li>
    <li <?= $this->app->setActive('TaskViewController', 'transitions') ?>>
        <?= $this->url->link('<i class="fa fa-arrows-h"></i> '.t('Transitions'), 'TaskViewController', 'transitions', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>
    </li>
    <li <?= $this->app->setActive('TaskViewController', 'analytics') ?>>
        <?= $this->url->link('<i class="fa fa-bar-chart"></i> '.t('Analytics'), 'TaskViewController', 'analytics', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>
    </li>
    <?php if ($task['time_estimated'] > 0 || $task['time_spent'] > 0): ?>
    <li <?= $this->app->setActive('TaskViewController', 'timetracking') ?>>
        <?= $this->url->link('<i class="fa fa-clock-o"></i> '.t('Time tracking'), 'TaskViewController', 'timetracking', ['task_id' => $task['id'], 'project_id' => $task['project_id']]) ?>
    </li>
    <?php endif ?>
</ul>
</div>