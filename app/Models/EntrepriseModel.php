<?php

namespace App\Models;

use CodeIgniter\Model;

class EntrepriseModel extends Model
{
    protected $table      = 'entreprise';
    protected $primaryKey = 'id';

    private function resolveTableName(array $candidates): ?string
    {
        foreach ($candidates as $table) {
            if ($this->db->tableExists($table)) {
                return $table;
            }
        }

        return null;
    }

    protected $allowedFields = ['nom', 'telephone', 'email'];

    protected $returnType = 'array';

    public function possedeContrats(int $entrepriseId): bool
    {
        $contratTable = $this->resolveTableName(['contrats', 'contrat']);

        if (! $contratTable) {
            return false;
        }

        $nombre = $this->db->table($contratTable)
            ->where('entreprise_id', $entrepriseId)
            ->countAllResults();

        return $nombre > 0;
    }
}
