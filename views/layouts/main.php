<?php

use app\Presentation\Assets\AdminAsset;
use yii\helpers\Html;
use yii\helpers\Url;

AdminAsset::register($this);

/** @var string $content */
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?= Html::csrfMetaTags() ?>
    <title><?= Html::encode($this->title ?: 'Telegram Monitor Admin') ?></title>
    <?php $this->head() ?>
</head>
<body class="hold-transition sidebar-mini">
<?php $this->beginBody() ?>

<div class="wrapper">
    <!-- Navbar -->
    <nav class="main-header navbar navbar-expand navbar-white navbar-light">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
            </li>
        </ul>
    </nav>

    <!-- Main Sidebar Container -->
    <aside class="main-sidebar sidebar-dark-primary elevation-4">
        <a href="<?= Url::to(['/site/index']) ?>" class="brand-link">
            <span class="brand-text font-weight-light">TG Monitor</span>
        </a>

        <div class="sidebar">
            <nav class="mt-2">
                <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu">
                    <li class="nav-item">
                        <a href="<?= Url::to(['/user/index']) ?>" class="nav-link <?= Yii::$app->controller->id == 'user' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-users"></i>
                            <p>Пользователи</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= Url::to(['/project/index']) ?>" class="nav-link <?= Yii::$app->controller->id == 'project' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-project-diagram"></i>
                            <p>Проекты</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= Url::to(['/keyword/index']) ?>" class="nav-link <?= Yii::$app->controller->id == 'keyword' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-key"></i>
                            <p>Ключевые слова</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= Url::to(['/group/index']) ?>" class="nav-link <?= Yii::$app->controller->id == 'group' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-broadcast-tower"></i>
                            <p>Группы</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= Url::to(['/match/index']) ?>" class="nav-link <?= Yii::$app->controller->id == 'match' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-search"></i>
                            <p>Совпадения</p>
                        </a>
                    </li>
                    <li class="nav-header">ЛОГИ</li>
                    <li class="nav-item">
                        <a href="<?= Url::to(['/digest-log/index']) ?>" class="nav-link <?= Yii::$app->controller->id == 'digest-log' ? 'active' : '' ?>">
                            <i class="nav-icon fas fa-history"></i>
                            <p>Дайджесты</p>
                        </a>
                    </li>
                </ul>
            </nav>
        </div>
    </aside>

    <!-- Content Wrapper -->
    <div class="content-wrapper">
        <div class="content-header">
            <div class="container-fluid">
                <div class="row mb-2">
                    <div class="col-sm-6">
                        <h1 class="m-0"><?= Html::encode($this->title) ?></h1>
                    </div>
                </div>
            </div>
        </div>

        <div class="content">
            <div class="container-fluid">
                <?= $content ?>
            </div>
        </div>
    </div>

    <!-- Main Footer -->
    <footer class="main-footer">
        <strong>&copy; <?= date('Y') ?> Telegram Monitor.</strong>
    </footer>
</div>

<?php $this->endBody() ?>
</body>
</html>
<?php $this->endPage() ?>
