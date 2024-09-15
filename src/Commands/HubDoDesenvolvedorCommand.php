<?php

namespace PauloRLima\HubDoDesenvolvedor\Commands;

use Illuminate\Console\Command;

class HubDoDesenvolvedorCommand extends Command
{
    public $signature = 'hubdodesenvolvedor';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
