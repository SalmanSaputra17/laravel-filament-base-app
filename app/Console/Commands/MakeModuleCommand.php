<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class MakeModuleCommand extends Command
{
    protected $signature = 'make:module {name : The name of the module}';

    protected $description = 'Create a new module with Model, Migration, Filament Resource, Policy, and Shield Permissions';

    public function handle(): int
    {
        $name = $this->argument('name');
        $name = ucfirst($name);
        $tableName = Str::snake(Str::plural($name));
        $modelPath = app_path("Models/{$name}.php");
        $migrationPath = database_path('migrations/'.date('Y_m_d_His')."_create_{$tableName}_table.php");
        $policyPath = app_path("Policies/{$name}Policy.php");
        $resourcePath = app_path("Filament/Resources/{$name}Resource.php");
        $resourcePagesPath = app_path("Filament/Resources/{$name}Resource/Pages");

        if (File::exists($modelPath)) {
            $this->error("Model {$name} already exists!");

            return self::FAILURE;
        }

        $this->info("Creating module: {$name}");

        $this->createMigration($name, $tableName, $migrationPath);
        $this->createModel($name, $modelPath);
        $this->createPolicy($name, $policyPath);
        $this->createFilamentResource($name, $resourcePath);
        $this->createFilamentResourcePages($name, $resourcePagesPath);

        $this->info("Module {$name} created successfully!");
        $this->info('');
        $this->info('Next steps:');
        $this->info('1. Run migrations: php artisan migrate');
        $this->info('2. Register the policy in app/Providers/AppServiceProvider.php');
        $this->info('3. Run php artisan shield:generate to generate permissions');

        return self::SUCCESS;
    }

    protected function createMigration(string $name, string $tableName, string $path): void
    {
        $migration = <<<PHP
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('{$tableName}', function (Blueprint \$table) {
            \$table->id();
            \$table->string('name');
            \$table->text('description')->nullable();
            \$table->foreignId('created_by')->nullable()->constrained('users');
            \$table->foreignId('updated_by')->nullable()->constrained('users');
            \$table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('{$tableName}');
    }
};
PHP;

        File::put($path, $migration);
        $this->info("Created migration: {$path}");
    }

    protected function createModel(string $name, string $path): void
    {
        $model = <<<PHP
<?php

namespace App\Models;

use App\Traits\HasAuthor;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class {$name} extends Model
{
    use HasAuthor, LogsActivity;

    protected \$table = '{$this->argument('name')}';

    protected \$guarded = [];

    protected function casts(): array
    {
        return [
            //
        ];
    }
}
PHP;

        File::put($path, $model);
        $this->info("Created model: {$path}");
    }

    protected function createPolicy(string $name, string $path): void
    {
        $policy = <<<PHP
<?php

namespace App\Policies;

use App\Models\\{$name};
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class {$name}Policy
{
    use HandlesAuthorization;

    public function viewAny(User \$user): bool
    {
        return \$user->can('{$this->argument('name')}.view_any');
    }

    public function view(User \$user, {$name} \${$this->camelCase($name)}): bool
    {
        return \$user->can('{$this->argument('name')}.view');
    }

    public function create(User \$user): bool
    {
        return \$user->can('{$this->argument('name')}.create');
    }

    public function update(User \$user, {$name} \${$this->camelCase($name)}): bool
    {
        return \$user->can('{$this->argument('name')}.update');
    }

    public function delete(User \$user, {$name} \${$this->camelCase($name)}): bool
    {
        return \$user->can('{$this->argument('name')}.delete');
    }

    public function restore(User \$user, {$name} \${$this->camelCase($name)}): bool
    {
        return \$user->can('{$this->argument('name')}.restore');
    }

    public function forceDelete(User \$user, {$name} \${$this->camelCase($name)}): bool
    {
        return \$user->can('{$this->argument('name')}.force_delete');
    }
}
PHP;

        File::put($path, $policy);
        $this->info("Created policy: {$path}");
    }

    protected function createFilamentResource(string $name, string $path): void
    {
        $resource = <<<PHP
<?php

namespace App\Filament\Resources;

use App\Filament\Resources\\{$name}Resource\Pages;
use App\Models\\{$name};
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;

class {$name}Resource extends Resource
{
    protected static ?string \$model = {$name}::class;

    protected static ?string \$navigationIcon = 'heroicon-o-folder';

    protected static ?string \$navigationGroup = 'Content';

    public static function form(Form \$form): Form
    {
        return \$form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->label('Name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Textarea::make('description')
                    ->label('Description')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table \$table): Table
    {
        return \$table
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID')
                    ->sortable(),
                Tables\Columns\TextColumn::make('name')
                    ->label('Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('createdBy.name')
                    ->label('Created By'),
                Tables\Columns\TextColumn::make('updatedBy.name')
                    ->label('Updated By'),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Created At')
                    ->dateTime('M Y, H:i'),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Updated At')
                    ->dateTime('M Y, H:i'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\List{$name}::route('/'),
            'create' => Pages\Create{$name}::route('/create'),
            'edit' => Pages\Edit{$name}::route('/{record}/edit'),
        ];
    }
}
PHP;

        File::put($path, $resource);
        $this->info("Created Filament Resource: {$path}");
    }

    protected function createFilamentResourcePages(string $name, string $path): void
    {
        File::makeDirectory($path, 0755, true);

        $listPage = <<<PHP
<?php

namespace App\Filament\Resources\\{$name}Resource\Pages;

use App\Filament\Resources\\{$name}Resource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class List{$name} extends ListRecords
{
    protected static string \$resource = {$name}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
PHP;

        $createPage = <<<PHP
<?php

namespace App\Filament\Resources\\{$name}Resource\Pages;

use App\Filament\Resources\\{$name}Resource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class Create{$name} extends CreateRecord
{
    protected static string \$resource = {$name}Resource::class;
}
PHP;

        $editPage = <<<PHP
<?php

namespace App\Filament\Resources\\{$name}Resource\Pages;

use App\Filament\Resources\\{$name}Resource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class Edit{$name} extends EditRecord
{
    protected static string \$resource = {$name}Resource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
PHP;

        File::put("{$path}/List{$name}.php", $listPage);
        File::put("{$path}/Create{$name}.php", $createPage);
        File::put("{$path}/Edit{$name}.php", $editPage);

        $this->info("Created Filament Resource Pages in: {$path}");
    }

    protected function camelCase(string $string): string
    {
        return lcfirst($string);
    }
}
