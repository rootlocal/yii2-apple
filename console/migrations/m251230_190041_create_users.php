<?php

use common\models\User;
use yii\db\Migration;
use yii\helpers\Console;

class m251230_190041_create_users extends Migration
{

    private array $users = [
        [
            'username' => 'admin',
            'password' => 'admin',
            'email' => 'admin@example.com',
            'status' => User::STATUS_ACTIVE,
        ],

        [
            'username' => 'manager',
            'password' => 'manager',
            'email' => 'manager@example.com',
            'status' => User::STATUS_ACTIVE,
        ],

        [
            'username' => 'customer',
            'password' => 'customer',
            'email' => 'customer@example.com',
            'status' => User::STATUS_ACTIVE,
        ],
    ];

    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        foreach ($this->users as $item) {
            $exitsUser = User::find()
                ->where(['OR',
                    ['username' => $item['username']],
                    ['email' => $item['email']],
                ])
                ->one();

            if ($exitsUser !== null) {
                $message = sprintf(
                    'Пользователь (id "%d") с таким логином ("%s") или email ("%s") уже существует.',
                    $exitsUser->id,
                    $item['username'],
                    $item['email'],
                );

                $message .= ' Данный пользователь будет пропущен' . PHP_EOL;
                Console::stdout($message);
                continue;
            }

            $user = new User();
            $user->username = $item['username'];
            $user->password = $item['password'];
            $user->email = $item['email'];
            $user->generateAuthKey();

            if (!$user->save(false)) {
                Console::stdout(sprintf('Ошибка создания пользователя: "%s"', $user->username) . PHP_EOL);
                return false;
            }

        }
        return true;
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        foreach ($this->users as $item) {
            $user = User::find()->where(['username' => $item['username']])->one();

            if ($user !== null) {
                $user->delete();
            }
        }

        return true;
    }

}
