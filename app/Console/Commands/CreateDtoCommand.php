<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateDtoCommand extends Command
{
    /**
     * @const string DTO dir path
     */
    public const DTO_PATH = 'app/Dtos/';

    /**
     * @const string Use Case DTO dir path
     */
    public const USECASE_DTO_PATH = 'app/Dtos/Usecase/';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:dto {dtoName : name of DTO}{--u|--usecase : Specify when make the DTO for Usecase}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new DTO class';

    /**
     * @var string
     */
    private $className;

    /**
     * @var string
     */
    private $dtoPath;

    /**
     * @var string
     */
    private $dirName;

    /**
     * @var string
     */
    private $dtoFileName;

    /**
     * @var bool
     */
    private $isUsecase;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if (preg_match('/\//', $this->argument('dtoName'))) {
            preg_match('/(.*)(?=\/)/', $this->argument('dtoName'), $matches);
            $this->dirName = $matches[0];
            preg_match('/[^\/]+$/', $this->argument('dtoName'), $matches);
            $this->className = ucfirst($matches[0]);
        } else {
            $this->className = ucfirst($this->argument('dtoName'));
        }

        $this->isUsecase = $this->option('usecase');

        if (!$this->className) {
            $this->error('DTO Class Name invalid');

            return;
        }

        if ($this->isUsecase) {
            $this->dtoPath = self::USECASE_DTO_PATH;
        } else {
            $this->dtoPath = self::DTO_PATH;
        }

        if (!file_exists($this->dtoPath)) {
            mkdir($this->dtoPath, 0755, true);
        }

        $this->dtoFileName = $this->dtoPath . ($this->dirName ? $this->dirName . '/' : null) . $this->className . 'Dto.php';

        if ($this->isExistFiles()) {
            $this->error('DTO already exist');

            return;
        }

        if ($this->dirName && !$this->isExistDirectory()) {
            $this->createDirectory();
        }

        $this->createDtoFile();
        $this->info('DTO created successfully');
    }

    /**
     * Create DTO File.
     */
    private function createDtoFile(): void
    {
        $nameSpace = '';
        if ($this->isUsecase) {
            $nameSpace .= '\\' . 'UseCase';
        }

        if ($this->dirName) {
            foreach (explode('/', $this->dirName) as $dir) {
                $nameSpace .= '\\' . $dir;
            }
        }
        $content = <<<EOF
            <?php

            declare(strict_types=1);

            namespace App\\Dtos{$nameSpace};
            EOF;

        if ($nameSpace && $this->isUsecase) {
            $content .= <<<EOF


                use App\Dtos\UseCase\UseCaseDto;
                EOF;
        }

        // class content
        if ($this->isUsecase) {

            // extends is UseCaseDto
            $content .= <<<EOF


                class {$this->className}Dto extends UseCaseDto
                {
                    // DTO property name should be same as request name

                    // e.g.
                    /** @var int */
                    public int \$userId;    // This is the property to set controller that is not in the request

                    /** @var int */
                    public int  \$page;

                    /** @var int */
                    public int \$per_page;
                }
                EOF;
        } else {

            // extends is none
            $content .= <<<EOF


                class {$this->className}Dto
                {
                    // e.g.
                    /** @var int */
                    public int \$user_id;

                    /** @var string */
                    public ?string \$notification_setting;

                    /** @var string */
                    public string \$to_email;

                    /** @var \Illuminate\Mail\Mailable */
                    public \$mailable;
                }
                EOF;
        }

        file_put_contents($this->dtoFileName, $content);
    }

    /**
     * Check if the same files exists.
     */
    private function isExistFiles(): bool
    {
        return file_exists($this->dtoFileName);
    }

    /**
     * Create directory.
     */
    private function createDirectory(): void
    {
        mkdir($this->dtoPath . $this->dirName, 0755, true);
    }

    /**
     * Check if the same directory exists.
     */
    private function isExistDirectory(): bool
    {
        return file_exists($this->dtoPath . $this->dirName);
    }
}
