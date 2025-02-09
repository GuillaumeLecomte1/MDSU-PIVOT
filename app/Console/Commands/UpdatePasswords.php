<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class UpdatePasswords extends Command
{
    protected $signature = 'users:update-passwords';

    protected $description = 'Update all user passwords to use Bcrypt';

    public function handle()
    {
        $users = User::all();
        $bar = $this->output->createProgressBar(count($users));
        $bar->start();

        foreach ($users as $user) {
            // On suppose que le mot de passe actuel est 'password'
            $user->password = Hash::make('password');
            $user->save();
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Tous les mots de passe ont été mis à jour avec succès !');
        $this->info('Le mot de passe par défaut est : password');
    }
}
