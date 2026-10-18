<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create or promote an administrator account securely';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Nama admin'));
        $email = strtolower(trim((string) $this->ask('Email admin')));

        if ($name === '' || ! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('Nama wajib diisi dan email harus valid.');

            return self::FAILURE;
        }

        $this->line('Ketik password minimal 12 karakter, lalu tekan Enter. Karakter tidak akan ditampilkan demi keamanan.');
        $password = (string) $this->secret('Password admin');
        $passwordConfirmation = (string) $this->secret('Ulangi password admin');
        if (strlen($password) < 12 || ! hash_equals($password, $passwordConfirmation)) {
            $this->error('Password harus minimal 12 karakter dan kedua isian harus sama.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'role' => 'admin',
            ]
        );

        $this->info("Akun admin untuk {$email} siap digunakan.");

        return self::SUCCESS;
    }
}
