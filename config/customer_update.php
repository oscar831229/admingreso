<?php

return [
    'delimiter' => ';',
    'enclosure' => '"',
    'insert_chunk' => 2000,
    'update_chunk' => 10000,
    'headers' => [
        'document_type',
        'document_number',
        'first_surname',
        'second_surname',
        'first_name',
        'second_name',
        'birthday_date',
        'gender',
        'address',
        'email',
    ],
    'document_types' => [
        'CC' => 1,
        'CE' => 2,
        'TI' => 4,
        'PA' => 5,
        'RC' => 6,
        'NI' => 7,
        'NU' => 8,
        'PE' => 11,
        'PT' => 12,
    ],
    'genders' => [
        'F' => 9,
        'M' => 10,
    ],
];
