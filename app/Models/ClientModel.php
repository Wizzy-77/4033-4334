<?php

namespace App\Models;

use CodeIgniter\Model;

class ClientModel extends Model
{
    protected $table            = 'client';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $allowedFields    = ['telephone', 'solde'];

    // Désactive la gestion automatique des dates par CodeIgniter
    protected $useTimestamps    = false;
}