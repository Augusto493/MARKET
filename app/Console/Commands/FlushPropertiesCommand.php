<?php

namespace App\Console\Commands;

use App\Models\Property;
use Illuminate\Console\Command;

class FlushPropertiesCommand extends Command
{
    protected $signature = 'stays:flush-properties {--force : Confirmar sem perguntar }';

    protected $description = 'Exclui todos os imóveis (properties) do sistema. Use antes de uma nova sincronização limpa.';

    public function handle()
    {
        $count = Property::count();
        if ($count === 0) {
            $this->info('Nenhum imóvel no sistema.');
            return 0;
        }

        if (! $this->option('force') && ! $this->confirm("Excluir permanentemente {$count} imóvel(is)?")) {
            return 0;
        }

        Property::query()->forceDelete();
        $this->info("{$count} imóvel(is) excluído(s).");
        return 0;
    }
}
