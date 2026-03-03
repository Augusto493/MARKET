<?php

namespace Database\Seeders;

use App\Models\Owner;
use App\Models\Property;
use App\Models\PropertyPhoto;
use App\Models\PropertyAmenity;
use Illuminate\Database\Seeder;

class DemoPropertiesSeeder extends Seeder
{
    public function run(): void
    {
        $owner = Owner::find(1);
        if (! $owner) {
            $this->command->error('Owner ID=1 não encontrado. Rode primeiro o setup do admin.');
            return;
        }

        $properties = [
            [
                'stays_property_id'     => 'demo-apt-101',
                'nome'                  => 'Apartamento Premium Frente Mar - Ed. Barramares',
                'descricao'             => 'Apartamento de alto padrão com vista panorâmica para o mar de Balneário Camboriú. Decoração sofisticada, cozinha americana equipada, varanda gourmet e acesso direto à praia. Perfeito para famílias e casais que buscam o máximo em conforto e localização.',
                'descricao_curta'       => 'Apt frente mar com varanda gourmet e vista panorâmica. 3 quartos, 2 banheiros.',
                'capacidade_hospedes'   => 6,
                'quartos'               => 3,
                'camas'                 => 4,
                'banheiros'             => 2,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Centro',
                'latitude'              => -26.9926,
                'longitude'             => -48.6347,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => true,
                'prioridade'            => 10,
                'titulo_marketing'      => 'Vista Mar Incrível — Frente à Praia Central',
                'tags'                  => ['frente-mar', 'vista-mar', 'premium', 'varanda', 'ar-condicionado', 'wifi'],
                'amenities'             => ['Wi-Fi', 'Ar-condicionado', 'TV Smart', 'Cozinha equipada', 'Varanda', 'Churrasqueira', 'Estacionamento', 'Piscina'],
                'photo_url'             => 'https://images.unsplash.com/photo-1566073771259-6a8506099945?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-102',
                'nome'                  => 'Studio Moderno a 100m da Praia - Barra Sul',
                'descricao'             => 'Studio moderno e aconchegante a apenas 100 metros da praia de Balneário Camboriú. Ideal para casais ou viajantes solo. Decoração contemporânea, cama king size, cozinha compacta totalmente equipada e varanda com rede.',
                'descricao_curta'       => 'Studio moderno 100m da praia, cama king, varanda com rede.',
                'capacidade_hospedes'   => 2,
                'quartos'               => 1,
                'camas'                 => 1,
                'banheiros'             => 1,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Barra Sul',
                'latitude'              => -26.9987,
                'longitude'             => -48.6312,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => true,
                'prioridade'            => 9,
                'titulo_marketing'      => 'Romântico & Moderno — 100m da Praia',
                'tags'                  => ['studio', 'casal', 'praia', 'moderno', 'wifi'],
                'amenities'             => ['Wi-Fi', 'Ar-condicionado', 'TV Smart', 'Cozinha compacta', 'Varanda', 'Netflix'],
                'photo_url'             => 'https://images.unsplash.com/photo-1522708323590-d24dbb6b0267?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-casa-201',
                'nome'                  => 'Casa de Praia com Piscina Privativa - Taquarinhas',
                'descricao'             => 'Casa espaçosa e bem equipada com piscina privativa, garden e churrasqueira coberta. Localizada no tranquilo bairro Taquarinhas, a 5 minutos de carro da praia principal. Ideal para grupos e famílias que buscam privacidade e conforto.',
                'descricao_curta'       => 'Casa c/ piscina privativa, churrasqueira e garden. 4 quartos.',
                'capacidade_hospedes'   => 10,
                'quartos'               => 4,
                'camas'                 => 6,
                'banheiros'             => 3,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Taquarinhas',
                'latitude'              => -26.9810,
                'longitude'             => -48.6501,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => true,
                'prioridade'            => 8,
                'titulo_marketing'      => 'Piscina Privativa + Churras — Perfeita para Grupos',
                'tags'                  => ['piscina', 'churrasqueira', 'casa', 'familia', 'privacidade'],
                'amenities'             => ['Wi-Fi', 'Piscina privativa', 'Churrasqueira', 'Ar-condicionado', 'TV Smart', 'Cozinha completa', 'Estacionamento 3 carros', 'Lavanderia'],
                'photo_url'             => 'https://images.unsplash.com/photo-1575517111839-3a3843ee7f5d?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-103',
                'nome'                  => 'Cobertura Duplex Vista Montanha e Mar',
                'descricao'             => 'Cobertura duplex exclusiva com terraço privativo e vista de 360° para o mar e a Serra Catarinense. 2 andares de puro luxo, sala de estar ampla, suíte master com closet e banheiro com banheira de imersão. Experiência única em Balneário Camboriú.',
                'descricao_curta'       => 'Cobertura duplex com terraço privativo e vista 360°. 3 suítes.',
                'capacidade_hospedes'   => 8,
                'quartos'               => 3,
                'camas'                 => 4,
                'banheiros'             => 3,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Centro',
                'latitude'              => -26.9941,
                'longitude'             => -48.6380,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => false,
                'prioridade'            => 7,
                'titulo_marketing'      => 'Cobertura Exclusiva — Vista Mar & Montanha 360°',
                'tags'                  => ['cobertura', 'luxo', 'vista-mar', 'terraço', 'banheira'],
                'amenities'             => ['Wi-Fi', 'Banheira de imersão', 'Sauna', 'Churrasqueira', 'Terraço privativo', 'TV Smart 65"', 'Home theater', 'Estacionamento'],
                'photo_url'             => 'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-104',
                'nome'                  => 'Apartamento 2 Quartos — Praia dos Amores',
                'descricao'             => 'Apartamento charmoso e bem localizado na Praia dos Amores, a área mais badalada de Balneário Camboriú. 2 quartos, sala ampla, cozinha equipada e sacada com vista para a cidade. A poucos passos de restaurantes, bares e lojas.',
                'descricao_curta'       => 'Apt 2 quartos na Praia dos Amores. Ótima localização.',
                'capacidade_hospedes'   => 4,
                'quartos'               => 2,
                'camas'                 => 3,
                'banheiros'             => 1,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Praia dos Amores',
                'latitude'              => -26.9872,
                'longitude'             => -48.6289,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => false,
                'prioridade'            => 6,
                'titulo_marketing'      => 'Localização Perfeita — Praia dos Amores',
                'tags'                  => ['boa-localização', 'praia-dos-amores', 'restaurantes', 'compras'],
                'amenities'             => ['Wi-Fi', 'Ar-condicionado', 'TV Smart', 'Cozinha equipada', 'Sacada', 'Estacionamento'],
                'photo_url'             => 'https://images.unsplash.com/photo-1502672260266-1c1ef2d93688?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-105',
                'nome'                  => 'Flat Executivo — Próximo ao Parque Unipraias',
                'descricao'             => 'Flat moderno e funcional a 200m do Parque Unipraias e do bondinho de Laranjeiras. Ideal para turistas que querem explorar todas as atrações de Balneário Camboriú. Estacionamento, Wi-Fi de alta velocidade e academia no prédio inclusa.',
                'descricao_curta'       => 'Flat perto do Unipraias. Wi-Fi fibra, academia inclusa.',
                'capacidade_hospedes'   => 3,
                'quartos'               => 1,
                'camas'                 => 2,
                'banheiros'             => 1,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Barra Norte',
                'latitude'              => -26.9763,
                'longitude'             => -48.6430,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => false,
                'prioridade'            => 5,
                'titulo_marketing'      => 'Perto do Unipraias — Wi-Fi Fibra + Academia',
                'tags'                  => ['unipraias', 'academia', 'executivo', 'fibra', 'estacionamento'],
                'amenities'             => ['Wi-Fi fibra', 'Academia', 'Ar-condicionado', 'TV Smart', 'Estacionamento', 'Serviço de limpeza'],
                'photo_url'             => 'https://images.unsplash.com/photo-1631049307264-da0ec9d70304?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-casa-202',
                'nome'                  => 'Casa Encantada na Interpraias — Pet Friendly',
                'descricao'             => 'Casa encantadora na região da Interpraias com jardim arborizado e pet friendly. Ambiente familiar, seguro e tranquilo a 800m da praia. 3 quartos sendo 1 suíte, quintal com área verde, churrasqueira e espaço para crianças brincarem.',
                'descricao_curta'       => 'Casa pet friendly com jardim. Interpraias, 800m da praia.',
                'capacidade_hospedes'   => 7,
                'quartos'               => 3,
                'camas'                 => 5,
                'banheiros'             => 2,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Interpraias',
                'latitude'              => -27.0048,
                'longitude'             => -48.6198,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => false,
                'prioridade'            => 4,
                'titulo_marketing'      => 'Pet Friendly + Jardim — Interpraias Tranquilo',
                'tags'                  => ['pet-friendly', 'jardim', 'familia', 'interpraias', 'churrasqueira'],
                'amenities'             => ['Wi-Fi', 'Pet friendly', 'Churrasqueira', 'Jardim', 'Ar-condicionado', 'TV Smart', 'Estacionamento 2 carros'],
                'photo_url'             => 'https://images.unsplash.com/photo-1564013799919-ab600027ffc6?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-106',
                'nome'                  => 'Loft Industrial Chique — Centro BC',
                'descricao'             => 'Loft com decoração industrial chique no coração de Balneário Camboriú. Pé-direito alto, tijolo aparente, móveis autorais e iluminação de destaque. A 3 minutos a pé da praia. Para quem busca uma experiência de hospedagem diferente e estilosa.',
                'descricao_curta'       => 'Loft estiloso perto da praia. Design industrial, 3min a pé.',
                'capacidade_hospedes'   => 2,
                'quartos'               => 1,
                'camas'                 => 1,
                'banheiros'             => 1,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Centro',
                'latitude'              => -26.9955,
                'longitude'             => -48.6356,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => false,
                'prioridade'            => 3,
                'titulo_marketing'      => 'Loft Estiloso — 3 min a pé da Praia Central',
                'tags'                  => ['loft', 'design', 'casal', 'perto-praia', 'estiloso'],
                'amenities'             => ['Wi-Fi fibra', 'Ar-condicionado', 'Apple TV', 'Cozinha Nespresso', 'Amenities premium'],
                'photo_url'             => 'https://images.unsplash.com/photo-1586023492125-27b2c045efd7?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-107',
                'nome'                  => 'Apartamento 3 Suítes — Condomínio Riviera',
                'descricao'             => 'Apartamento luxuoso de 3 suítes no renomado Condomínio Riviera com estrutura de resort. Piscina de borda infinita, SPA, fitness center, restaurante e serviço de concierge. Vista privilegiada para o mar. O melhor da hotelaria em um apartamento.',
                'descricao_curta'       => 'Apt 3 suítes em resort 5★. Piscina infinita, SPA, concierge.',
                'capacidade_hospedes'   => 8,
                'quartos'               => 3,
                'camas'                 => 5,
                'banheiros'             => 3,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Centro',
                'latitude'              => -26.9910,
                'longitude'             => -48.6330,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => true,
                'prioridade'            => 9,
                'titulo_marketing'      => 'Resort 5★ — Piscina Infinita + SPA + Concierge',
                'tags'                  => ['resort', 'luxo', 'piscina-infinita', 'spa', 'concierge', 'vista-mar'],
                'amenities'             => ['Wi-Fi', 'Piscina de borda infinita', 'SPA', 'Academia', 'Restaurante', 'Concierge', 'Room service', 'Estacionamento'],
                'photo_url'             => 'https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?w=800&q=80',
            ],
            [
                'stays_property_id'     => 'demo-apt-108',
                'nome'                  => 'Kitnet Econômica Bem Localizada — BC Centro',
                'descricao'             => 'Kitnet compacta e econômica no centro de Balneário Camboriú, ótima para quem quer economizar hospedagem sem abrir mão de localização. A 500m da praia, próximo ao centrão de compras. Cama de casal, banheiro, cozinha e arrumação inclusa.',
                'descricao_curta'       => 'Kitnet econômica 500m da praia. Limpeza e wi-fi incluso.',
                'capacidade_hospedes'   => 2,
                'quartos'               => 1,
                'camas'                 => 1,
                'banheiros'             => 1,
                'cidade'                => 'Balneário Camboriú',
                'bairro'                => 'Centro',
                'latitude'              => -26.9969,
                'longitude'             => -48.6401,
                'ativo'                 => true,
                'publicado_marketplace' => true,
                'destaque'              => false,
                'prioridade'            => 1,
                'titulo_marketing'      => 'Econômico & Central — 500m da Praia',
                'tags'                  => ['economico', 'kitnet', 'centro', 'casal', 'custo-beneficio'],
                'amenities'             => ['Wi-Fi', 'Ar-condicionado', 'TV', 'Cozinha compacta', 'Limpeza inclusa'],
                'photo_url'             => 'https://images.unsplash.com/photo-1555854877-bab0e564b8d5?w=800&q=80',
            ],
        ];

        $created = 0;
        $skipped = 0;

        foreach ($properties as $data) {
            // Verificar se já existe
            if (Property::where('stays_property_id', $data['stays_property_id'])->exists()) {
                $skipped++;
                continue;
            }

            $amenities = $data['amenities'];
            $photoUrl  = $data['photo_url'];
            unset($data['amenities'], $data['photo_url']);

            $property = Property::create(array_merge($data, [
                'owner_id'       => $owner->id,
                'stays_synced_at' => now(),
            ]));

            // Criar foto principal
            PropertyPhoto::create([
                'property_id'   => $property->id,
                'url'           => $photoUrl,
                'thumbnail_url' => $photoUrl,
                'principal'     => true,
                'ordem'         => 0,
                'legenda'       => $property->nome,
            ]);

            // Criar comodidades
            foreach ($amenities as $amenity) {
                PropertyAmenity::create([
                    'property_id' => $property->id,
                    'nome'        => $amenity,
                    'icone'       => null,
                    'categoria'   => 'geral',
                ]);
            }

            $created++;
        }

        $this->command->info("✅ DemoPropertiesSeeder: {$created} imóveis criados, {$skipped} já existiam.");
    }
}
