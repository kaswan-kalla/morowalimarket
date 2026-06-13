<?php

namespace App\Models;

use CodeIgniter\Model;

/**
 * Model untuk tabel addresses
 */
class AddressModel extends Model
{
    protected $table            = 'addresses';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'array';
    protected $useSoftDeletes   = true;
    protected $allowedFields    = [
        'user_id', 'label', 'recipient_name', 'phone', 'address',
        'city', 'province', 'postal_code', 'is_default'
    ];
    protected $useTimestamps    = true;
    protected $dateFormat       = 'datetime';
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $deletedField     = 'deleted_at';

    protected $validationRules  = [
        'label'           => 'required|max_length[100]',
        'recipient_name' => 'required|max_length[100]',
        'phone'          => 'required|max_length[20]',
        'address'        => 'required',
        'city'           => 'required|max_length[100]',
        'province'       => 'required|max_length[100]',
        'postal_code'    => 'required|max_length[10]',
    ];

    /**
     * Ambil alamat user
     */
    public function getByUser(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->orderBy('is_default', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
    }

    /**
     * Ambil alamat default user
     */
    public function getDefault(int $userId)
    {
        return $this->where('user_id', $userId)
                    ->where('is_default', 1)
                    ->first();
    }

    /**
     * Set alamat sebagai default (reset yang lain)
     */
    public function setDefault(int $addressId, int $userId)
    {
        $this->db->transStart();
        $this->where('user_id', $userId)->set(['is_default' => 0])->update();
        $this->update($addressId, ['is_default' => 1]);
        $this->db->transComplete();
        return $this->db->transStatus();
    }
}
