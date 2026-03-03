<?php

namespace App\Services\Stays;

interface StaysAdapterInterface
{
    /**
     * Testa a conexão com a API Stays
     */
    public function testConnection(): array;

    /**
     * Retorna dados do searchfilter (amenities e inventory com _id e _mstitle) para resolver nomes.
     * Retorno: ['amenities' => [...], 'inventory' => [...]] ou [] em caso de erro.
     */
    public function getSearchFilter(): array;

    /**
     * Lista todas as propriedades
     */
    public function listProperties(): array;

    /**
     * Obtém detalhes de uma propriedade específica
     */
    public function getPropertyDetails(string $propertyId): array;

    /**
     * Obtém fotos de uma propriedade
     */
    public function getPropertyPhotos(string $propertyId): array;

    /**
     * Obtém disponibilidade de uma propriedade
     */
    public function getAvailability(string $propertyId, string $dateFrom, string $dateTo): array;

    /**
     * Obtém preços/tarifas de uma propriedade
     */
    public function getRates(string $propertyId, string $dateFrom, string $dateTo): array;

    /**
     * Cria uma reserva
     */
    public function createReservation(array $payload): array;

    /**
     * Obtém detalhes de uma reserva
     */
    public function getReservation(string $reservationId): array;

    /**
     * Lista reservas (se suportado)
     */
    public function listReservations(string $dateFrom, string $dateTo): array;

    /**
     * Busca listagens disponíveis para um período (Booking API).
     * Params: from (YYYY-MM-DD), to (YYYY-MM-DD), guests (int, opcional).
     * Retorno: array de listagens com _t_mainImageMeta.url, bookingPrice._mctotal, _mstitle, address, etc.
     */
    public function searchListings(array $params): array;

    /**
     * Calcula preço para uma ou mais listagens em um período (Booking API).
     * Retorno: array de [{ _idlisting, _mctotal, from, to, ... }].
     */
    public function calculatePrice(array $listingIds, string $from, string $to, int $guests = 1, ?string $promocode = null): array;
}
