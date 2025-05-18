<?php

namespace App\Console\Commands;

use App\Traits\BelongsToTenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\SplFileInfo;

class CheckMultiTenancyModels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tenant:check-models';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check if all models have the BelongsToTenant trait for multi-tenancy support';

    /**
     * Models that should be excluded from the check (don't need tenant_id)
     * 
     * @var array
     */
    protected $excludedModels = [
        'Tenant',            // Tenant model itself doesn't need tenant_id
        'SubscriptionPlan',  // SubscriptionPlan is global, not tenant-specific
        'PersonalAccessToken', // Laravel Sanctum token
        'Permission',        // Already handled by Spatie Permission package
        'Role',              // Already handled by Spatie Permission package
    ];

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking models for Multi-Tenancy support...');

        $modelsPath = app_path('Models');
        if (!is_dir($modelsPath)) {
            $this->error('Models directory not found!');
            return 1;
        }

        $modelFiles = File::files($modelsPath);
        $modelsWithoutTrait = [];
        $modelsWithTrait = [];

        foreach ($modelFiles as $file) {
            /** @var SplFileInfo $file */
            $className = $this->getClassNameFromFile($file);
            
            if (in_array($className, $this->excludedModels)) {
                $this->line("Skipping excluded model: <comment>{$className}</comment>");
                continue;
            }

            $fullClassName = "\\App\\Models\\{$className}";
            
            // Check if class exists and is instantiable
            if (!class_exists($fullClassName)) {
                $this->warn("Class {$fullClassName} does not exist or is not properly defined in file {$file->getFilename()}");
                continue;
            }
            
            // Check if model uses the trait
            $reflection = new \ReflectionClass($fullClassName);
            $usesTrait = $this->usesTrait($reflection, BelongsToTenant::class);
            
            if ($usesTrait) {
                $modelsWithTrait[] = $className;
            } else {
                $modelsWithoutTrait[] = $className;
            }
        }

        if (count($modelsWithTrait) > 0) {
            $this->info('Models with BelongsToTenant trait:');
            foreach ($modelsWithTrait as $model) {
                $this->line("- <info>{$model}</info>");
            }
        }

        if (count($modelsWithoutTrait) > 0) {
            $this->error('Models missing BelongsToTenant trait:');
            foreach ($modelsWithoutTrait as $model) {
                $this->line("- <comment>{$model}</comment>");
            }
            
            $this->warn("These models should implement the BelongsToTenant trait to ensure proper tenant isolation.");
            $this->line("Add the following to each model:");
            $this->line("<info>use App\\Traits\\BelongsToTenant;</info>");
            $this->line("And add the trait to the class:");
            $this->line("<info>use HasFactory, BelongsToTenant;</info>");
            
            return 1;
        } else {
            $this->info('All models have proper multi-tenancy support.');
            return 0;
        }
    }

    /**
     * Get the class name from a file
     * 
     * @param SplFileInfo $file
     * @return string|null
     */
    private function getClassNameFromFile(SplFileInfo $file)
    {
        return pathinfo($file->getFilename(), PATHINFO_FILENAME);
    }

    /**
     * Check if a class uses a specific trait
     * 
     * @param \ReflectionClass $class
     * @param string $traitName
     * @return bool
     */
    private function usesTrait(\ReflectionClass $class, string $traitName): bool
    {
        $traits = $class->getTraitNames();
        if (in_array($traitName, $traits)) {
            return true;
        }

        // Check parent classes for the trait
        $parent = $class->getParentClass();
        if ($parent) {
            return $this->usesTrait($parent, $traitName);
        }

        return false;
    }
}
