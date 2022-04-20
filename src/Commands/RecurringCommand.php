<?php

namespace tanthammar\Recurring\Commands;

use Illuminate\Console\Command;

class RecurringCommand extends Command
{
    public $signature = 'recurring';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
