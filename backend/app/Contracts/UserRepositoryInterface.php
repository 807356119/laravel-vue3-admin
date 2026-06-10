<?php

namespace App\Contracts;

interface UserRepositoryInterface
{
    public function getAllUsers(array $filters, int $perPage);

    public function findById(int $id);

    public function create(array $data);

    public function update(int $id, array $data);

    public function delete(int $id);

    public function toggleStatus(int $id);

    public function resetPassword(int $id, string $password);

    public function findByEmail(string $email);
}
