<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateRepositoryFileCommand extends Command
{
    /**
     * @const string repository dir path
     */
    public const REPOSITORIES_PATH = 'app/Repositories/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:repository
                            {repositoryName}
                            {--interface= : Implement the designated Interface}
                            {--i|--ignore : Whether to check if the model exists}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates new repository and interface classes';

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $dirName;

    /**
     * @var string
     */
    private $repositoryFileName;

    /**
     * @var string
     */
    private $interfaceName;

    /**
     * @var string
     */
    private $interfaceFileName;

    /**
     * @var bool
     */
    private $isIgnore;

    /**
     * Create a new command instance.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->className = $this->argument('repositoryName');

        if (!$this->className) {
            $this->error('Repository Name invalid.');
        }

        $this->isIgnore = $this->option('ignore');

        if (!$this->isExistModel() && !$this->isIgnore) {
            $this->error('Model not exist');

            return;
        }

        $this->dirName = $this->className;

        if (!$this->isExistDirectory()) {
            $this->createDirectory();
        }

        $this->repositoryFileName = self::REPOSITORIES_PATH . $this->dirName . '/' . $this->className . 'Repository.php';
        $this->interfaceFileName = self::REPOSITORIES_PATH . $this->dirName . '/' . $this->className . 'RepositoryInterface.php';
        if ($this->isExistFiles()) {
            $this->error('Repository already exists.');

            return;
        }

        $this->interfaceName = $this->option('interface') ?: $this->argument('repositoryName');

        $this->createRepositoryFile();

        if (!$this->option('interface')) {
            $this->createInterFaceFile();
        }

        $this->info('Repository created successfully.');
    }

    /**
     * Create Repository File.
     */
    private function createRepositoryFile(): void
    {
        $content = <<<EOD
            <?php

            declare(strict_types=1);

            namespace App\\Repositories\\{$this->dirName};

            use App\\Models\\{$this->className};\n
            EOD;

        !($this->option('interface')) ?: $content .= "use App\\Repositories\\{$this->interfaceName}\\{$this->interfaceName}RepositoryInterface;\n";

        $content .= "\n" . <<<EOD
            class {$this->className}Repository implements {$this->interfaceName}RepositoryInterface
            {
                public function __construct(private readonly {$this->className} \$model)
                {
                }
            }

            EOD;

        file_put_contents($this->repositoryFileName, $content);
    }

    /**
     * Create Interface class for the created Repository class.
     */
    private function createInterFaceFile(): void
    {
        $content = <<<EOD
            <?php

            declare(strict_types=1);

            namespace App\\Repositories\\{$this->dirName};

            interface {$this->className}RepositoryInterface
            {

            }
            EOD;

        file_put_contents($this->interfaceFileName, $content);
    }

    /**
     * Confirm the same Files.
     */
    private function isExistFiles(): bool
    {
        return file_exists($this->repositoryFileName) && file_exists($this->interfaceFileName);
    }

    /**
     * Check if the same directory exists.
     */
    private function isExistDirectory(): bool
    {
        return file_exists(self::REPOSITORIES_PATH . $this->dirName);
    }

    /**
     * Create directory.
     */
    private function createDirectory(): void
    {
        mkdir(self::REPOSITORIES_PATH . $this->dirName, 0755, true);
    }

    private function isExistModel(): bool
    {
        return file_exists('app/Models/' . $this->className . '.php');
    }
}
