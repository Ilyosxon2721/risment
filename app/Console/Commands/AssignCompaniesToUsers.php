<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Company;

class AssignCompaniesToUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:assign-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Assign companies to users who don\'t have one';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding users without companies...');
        
        $usersWithoutCompanies = User::doesntHave('companies')->get();
        
        if ($usersWithoutCompanies->isEmpty()) {
            $this->info('All users already have companies!');
            return 0;
        }
        
        $this->info("Found {$usersWithoutCompanies->count()} users without companies.");
        
        $bar = $this->output->createProgressBar($usersWithoutCompanies->count());
        $bar->start();
        
        foreach ($usersWithoutCompanies as $user) {
            // Create company for user
            $company = Company::create([
                'name' => $user->name . "'s Company",
                'contact_name' => $user->name,
                'email' => $user->email,
                'phone' => '', // Empty phone field
                'status' => 'active',
            ]);
            
            // Attach user as owner
            $user->companies()->attach($company->id, [
                'role_in_company' => 'owner',
            ]);
            
            $bar->advance();
        }
        
        $bar->finish();
        $this->newLine();
        $this->info('Successfully created companies for all users!');
        
        return 0;
    }
}
