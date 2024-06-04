<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateUsecaseCommand extends Command
{
    /**
     * @const string Use Case dir path
     */
    public const USECASE_PATH = 'app/UseCases/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:usecase {useCaseName}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new Use Case class';

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
    private $useCaseFileName;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (preg_match('/\//', $this->argument('useCaseName'))) {
            preg_match('/(.*)(?=\/)/', $this->argument('useCaseName'), $matches);
            $this->dirName = $matches[0];
            preg_match('/[^\/]+$/', $this->argument('useCaseName'), $matches);
            $this->className = $matches[0];
        } else {
            $this->className = $this->argument('useCaseName');
        }

        if (!$this->className) {
            $this->error('UseCase Class Name invalid');

            return;
        }

        if (!file_exists(self::USECASE_PATH)) {
            mkdir(self::USECASE_PATH, 0755, true);
        }

        $this->useCaseFileName = self::USECASE_PATH . ($this->dirName ? $this->dirName . '/' : null) . $this->className . 'UseCase.php';

        if ($this->isExistFiles()) {
            $this->error('UseCase already exist');

            return;
        }

        if ($this->dirName && !$this->isExistDirectory()) {
            $this->createDirectory();
        }

        $this->createUseCaseFile();
        $this->info('UseCase created successfully');
    }

    /**
     * Create Use Case File.
     */
    private function createUseCaseFile(): void
    {
        $nameSpace = '';
        if ($this->dirName) {
            foreach (explode('/', $this->dirName) as $dir) {
                $nameSpace .= '\\' . $dir;
            }
        }
        $content = "<?php\n\ndeclare(strict_types=1);\n\nnamespace App\\UseCases{$nameSpace};\n\nclass {$this->className}UseCase\n{\n    public function __invoke()\n    {\n    }\n}\n";

        file_put_contents($this->useCaseFileName, $content);
    }

    /**
     * Check if the same files exists.
     */
    private function isExistFiles(): bool
    {
        return file_exists($this->useCaseFileName);
    }

    /**
     * Create directory.
     */
    private function createDirectory(): void
    {
        mkdir(self::USECASE_PATH . $this->dirName, 0755, true);
    }

    /**
     * Check if the same directory exists.
     */
    private function isExistDirectory(): bool
    {
        return file_exists(self::USECASE_PATH . $this->dirName);
    }
}
