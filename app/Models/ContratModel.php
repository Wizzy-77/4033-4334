<?php

namespace App\Models;

use CodeIgniter\Model;


class ContratModel extends Model
{
    protected $table      = 'contrats';
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

    private function resolveTables(): array
    {
        return [
            'contrat' => $this->resolveTableName(['contrats', 'contrat']),
            'entreprise' => $this->resolveTableName(['entreprise', 'entreprises']),
            'statut' => $this->resolveTableName(['statut', 'statuts']),
        ];
    }

    protected $allowedFields = [
        'sujet',
        'entreprise_id',
        'description',
        'date_signature',
        'date_expiration',
        'statut_id',
    ];

    protected $returnType = 'array';

   
    public function getListe(string $recherche = '', ?int $statutId = null): array
    {
        $tables = $this->resolveTables();

        if (! $tables['contrat'] || ! $tables['entreprise'] || ! $tables['statut']) {
            return [];
        }

        $builder = $this->db->table($tables['contrat'] . ' c');

        $builder->select('c.id, c.sujet,c.date_signature,c.date_expiration, c.date_creation, e.nom AS entreprise_nom, s.id AS statut_id, s.nom AS statut_nom');
        $builder->join($tables['entreprise'] . ' e', 'e.id = c.entreprise_id');
        $builder->join($tables['statut'] . ' s', 's.id = c.statut_id');

        if ($recherche !== '') {
            $builder->groupStart();
                $builder->like('c.sujet', $recherche);
                $builder->orLike('e.nom', $recherche);

                if (is_numeric($recherche)) {
                    $builder->orWhere('c.id', (int) $recherche);
                }
            $builder->groupEnd();
        }

        if ($statutId !== null) {
            $builder->where('s.id', $statutId);
        }

        $builder->orderBy('c.id', 'DESC');

        return $builder->get()->getResultArray();
    }

    
    public function getDetail(int $id): ?array
    {
        $tables = $this->resolveTables();

        if (! $tables['contrat'] || ! $tables['entreprise'] || ! $tables['statut']) {
            return null;
        }

        $builder = $this->db->table($tables['contrat'] . ' c');

        $builder->select('c.*, e.nom AS entreprise_nom, e.telephone AS entreprise_telephone, e.email AS entreprise_email, s.nom AS statut_nom');
        $builder->join($tables['entreprise'] . ' e', 'e.id = c.entreprise_id');
        $builder->join($tables['statut'] . ' s', 's.id = c.statut_id');
        $builder->where('c.id', $id);

        $resultat = $builder->get()->getRowArray();

        return $resultat ?: null;
    }
}
