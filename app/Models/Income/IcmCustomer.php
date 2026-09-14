<?php

namespace App\Models\Income;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class IcmCustomer extends Model
{
    protected $table = 'icm_customers';

    protected $fillable = [
        'document_number','document_type','first_name','second_name','first_surname','second_surname',
        'birthday_date','gender','icm_municipality_id','address','phone','email','type_regime_id',
        'type_liability_id','tax_detail_id','type_organization_id','last_liquidation_date',
        'icm_types_income_id','icm_affiliate_category_id','user_created','user_updated'
    ];

    public function hasSearchFilters(array $filters)
    {
        return !empty($filters['document_number']) || !empty($filters['name']) || !empty($filters['phone']) || !empty($filters['email']);
    }

    protected function buildSearchQuery(array $filters)
    {
        $query = DB::table('icm_customers as c');

        if (!empty($filters['document_number'])) {
            $query->where('c.document_number', trim($filters['document_number']));
        }

        if (!empty($filters['name'])) {
            $terms = preg_split('/\s+/', trim($filters['name']), -1, PREG_SPLIT_NO_EMPTY);
            $terms = array_slice($terms, 0, 4);

            foreach ($terms as $term) {
                $value = $term.'%';

                $query->where(function ($q) use ($value) {
                    $q->where('c.first_name', 'LIKE', $value)
                      ->orWhere('c.second_name', 'LIKE', $value)
                      ->orWhere('c.first_surname', 'LIKE', $value)
                      ->orWhere('c.second_surname', 'LIKE', $value);
                });
            }
        }

        if (!empty($filters['phone'])) {
            $query->where('c.phone', 'LIKE', trim($filters['phone']).'%');
        }

        if (!empty($filters['email'])) {
            $query->where('c.email', 'LIKE', trim($filters['email']).'%');
        }

        return $query;
    }

    public function getCustomersSearch(array $filters, $start = 0, $length = 25)
    {
        if (!$this->hasSearchFilters($filters)) return collect();

        return $this->buildSearchQuery($filters)
            ->selectRaw("
                c.id,
                c.document_number,
                TRIM(CONCAT_WS(' ',
                    NULLIF(c.first_name,''),
                    NULLIF(c.second_name,''),
                    NULLIF(c.first_surname,''),
                    NULLIF(c.second_surname,'')
                )) AS name,
                c.phone,
                c.email
            ")
            ->orderBy('c.id', 'desc')
            ->offset((int)$start)
            ->limit((int)$length)
            ->get();
    }

    public function getCustomersSearchCount(array $filters)
    {
        if (!$this->hasSearchFilters($filters)) return 0;

        return $this->buildSearchQuery($filters)->count('c.id');
    }
}
