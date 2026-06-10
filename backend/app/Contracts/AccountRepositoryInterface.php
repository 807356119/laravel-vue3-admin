<?php

namespace App\Contracts;

interface AccountRepositoryInterface
{
    public function getUserAccounts(int $userId, array $filters, int $perPage);

    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function toggleFavorite(int $id);

    public function getDecryptedPassword(int $id);
}
