<?php

namespace Joeycoonce\FreshStart\Commands;

use Illuminate\Console\Command;

class FreshStartCommand extends Command
{
    public $signature = 'fresh-start';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
