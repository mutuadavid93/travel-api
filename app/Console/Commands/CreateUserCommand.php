<?php

namespace App\Console\Commands;

use App\Models\{User, Role};
use Illuminate\Console\Command;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\{DB, Hash, Validator};

class CreateUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a new user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Terminal Prompts
        $user["name"] = $this->ask("Name of the new user");
        $user["email"] = $this->ask("Email of the new user");
        // TIP: secret() shows asterisks.
        $user["password"] = $this->secret("Password of the new user");

        // Roles
        $roleName = $this->choice("Role of the new user", ["admin", "editor"], 1);

        // Check if the role exists in database
        $role = Role::where("name", $roleName)->first();

        // Validate commands
        $validator = Validator::make($user, [
            "name" => ["required", "string", "max:255"],
            "email" => ["required", "string", "email", "max:255", "unique:" . User::class],
            "password" => ["required", Password::defaults()]
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
                return -1;
            }
        }

        if (!$role) {
            $this->error("Role not found");
            return -1; // failure
        }

        // TIP: Whenever you have database statements which depend on each other, 
        // always use a transaction. Makes sure all statements run to completion.

        DB::transaction(function () use ($user, $role) {
            // Hash the password
            $user["password"] = Hash::make($user["password"]);
            $newUser = User::create($user);
            // Associate users to roles
            $newUser->roles()->attach($role->id);
        });

        $this->info("User {$user["email"]} created successfully");

        // If successful return 0 which translates to success
        return 0;
    }
}
