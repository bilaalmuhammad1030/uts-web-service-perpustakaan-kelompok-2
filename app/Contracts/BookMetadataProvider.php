<?php

namespace App\Contracts;

interface BookMetadataProvider
{
    public function cari(string $query): array;
}
