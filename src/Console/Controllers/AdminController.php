<?php

namespace app\Console\Controllers;

use app\Infrastructure\Persistence\UserRecord;
use yii\console\Controller;
use yii\console\ExitCode;
use yii\helpers\Console;
use Yii;

class AdminController extends Controller
{
    /**
     * Создает или назначает администратора.
     * Запрашивает Telegram ID и пароль интерактивно.
     */
    public function actionCreateAdmin()
    {
        $telegramId = $this->prompt('Введите Telegram ID пользователя:', ['required' => true]);
        
        $password = $this->promptPassword('Введите пароль для входа в админку:');
        $passwordConfirm = $this->promptPassword('Повторите пароль:');

        if ($password !== $passwordConfirm) {
            $this->stderr("❌ Пароли не совпадают!\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        if (strlen($password) < 4) {
            $this->stderr("❌ Пароль слишком короткий (мин. 4 символа)!\n", Console::FG_RED);
            return ExitCode::UNSPECIFIED_ERROR;
        }

        $user = UserRecord::findOne(['telegram_id' => $telegramId]);
        
        if (!$user) {
            $user = new UserRecord();
            $user->telegram_id = $telegramId;
            $user->username = 'admin';
            $user->status = 'active';
            $this->stdout("Создание нового пользователя с Telegram ID: $telegramId\n", Console::FG_YELLOW);
        } else {
            $this->stdout("Обновление существующего пользователя с Telegram ID: $telegramId\n", Console::FG_YELLOW);
        }

        $user->is_admin = true;
        $user->setPassword($password);

        if ($user->save()) {
            $this->stdout("✅ Администратор успешно сохранен!\n", Console::FG_GREEN);
            return ExitCode::OK;
        } else {
            $this->stderr("❌ Ошибка сохранения администратора:\n", Console::FG_RED);
            print_r($user->getErrors());
            return ExitCode::UNSPECIFIED_ERROR;
        }
    }

    /**
     * Надежный метод для скрытого ввода пароля (имитация поведения Symfony Console)
     */
    private function promptPassword($text)
    {
        if (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') {
            return $this->prompt($text, ['required' => true, 'hideInput' => true]);
        }

        $this->stdout($text . ' ');

        // Сохраняем состояние терминала и отключаем эхо
        $sttyMode = shell_exec('stty -g');
        shell_exec('stty -echo');

        $input = fgets(STDIN);

        // Восстанавливаем состояние терминала
        if ($sttyMode) {
            shell_exec("stty $sttyMode");
        } else {
            shell_exec('stty echo');
        }

        $this->stdout("\n");

        return trim($input);
    }
}
