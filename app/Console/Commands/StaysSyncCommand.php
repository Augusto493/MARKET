<?php

namespace App\Console\Commands;

use App\Models\Owner;
use App\Models\SyncLog;
use App\Services\Stays\StaysAdapterFactory;
use App\Services\Stays\StaysService;
use Illuminate\Console\Command;

class StaysSyncCommand extends Command
{
    protected $signature = 'stays:sync {ownerId?} {--full} {--days=180}';

    protected $description = 'Sincroniza propriedades da Stays.net';

    public function handle()
    {
        $ownerId = $this->argument('ownerId');
        $fullSync = $this->option('full');
        $days = (int) $this->option('days');

        if ($ownerId) {
            $owners = Owner::where('id', $ownerId)->get();
        } else {
            $owners = Owner::active()->get();
        }

        if ($owners->isEmpty()) {
            $this->error('Nenhum owner encontrado.');
            return 1;
        }

        foreach ($owners as $owner) {
            $this->info("Sincronizando owner: {$owner->nome} (ID: {$owner->id})");
            
            $syncLog = SyncLog::create([
                'owner_id' => $owner->id,
                'tipo' => $fullSync ? 'full' : 'incremental',
                'status' => 'success',
                'started_at' => now(),
            ]);

            try {
                // Criar adapter baseado nas credenciais do owner
                $adapter = StaysAdapterFactory::forOwner($owner);
                $service = new StaysService($adapter);

                // Testar conexão
                $test = $adapter->testConnection();
                if (!$test['success']) {
                    throw new \Exception('Falha na conexão: ' . ($test['message'] ?? 'Erro desconhecido'));
                }

                $this->info('✓ Conexão estabelecida');

                // Catálogo de comodidades (searchfilter) para exibir nomes em vez de IDs
                $searchFilter = $adapter->getSearchFilter();
                $amenitiesCatalog = StaysService::buildAmenitiesCatalog($searchFilter);
                if (! empty($amenitiesCatalog)) {
                    $this->info('  Catálogo de comodidades: ' . count($amenitiesCatalog) . ' itens');
                }

                // Listar propriedades
                $properties = $adapter->listProperties();
                $this->info("Encontradas " . count($properties) . " propriedades");

                $created = 0;
                $updated = 0;
                $failed = 0;

                foreach ($properties as $propertyData) {
                    try {
                        $property = $service->syncProperty($propertyData['id'], $owner, $amenitiesCatalog);
                        
                        if ($property) {
                            if ($property->wasRecentlyCreated) {
                                $created++;
                            } else {
                                $updated++;
                            }

                            // Sincronizar disponibilidade e preços
                            $this->info("  Sincronizando disponibilidade e preços para: {$property->nome}");
                            $service->syncAvailability($property, $days);
                            $service->syncRates($property, $days);
                        } else {
                            $failed++;
                        }
                    } catch (\Exception $e) {
                        $failed++;
                        $this->error("  Erro ao sincronizar propriedade {$propertyData['id']}: " . $e->getMessage());
                    }
                }

                // Atualizar log
                $syncLog->update([
                    'properties_synced' => count($properties),
                    'properties_created' => $created,
                    'properties_updated' => $updated,
                    'properties_failed' => $failed,
                    'finished_at' => now(),
                    'status' => $failed > 0 ? 'partial' : 'success',
                ]);

                // Atualizar owner
                $owner->update([
                    'sync_status' => $failed > 0 ? 'erro' : 'ok',
                    'last_sync_at' => now(),
                    'last_sync_error' => $failed > 0 ? "{$failed} propriedades falharam" : null,
                ]);

                $this->info("✓ Sincronização concluída: {$created} criadas, {$updated} atualizadas, {$failed} falharam");
            } catch (\Exception $e) {
                $syncLog->update([
                    'status' => 'error',
                    'error_message' => $e->getMessage(),
                    'finished_at' => now(),
                ]);

                $owner->update([
                    'sync_status' => 'erro',
                    'last_sync_error' => $e->getMessage(),
                ]);

                $this->error("✗ Erro: " . $e->getMessage());
            }
        }

        return 0;
    }
}
