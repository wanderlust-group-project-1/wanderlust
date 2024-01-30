<?php

class LocationModel 
{
    use Model;

    protected string $table = 'locations';
    
    protected array $allowedColumns = [
        'latitude',
        'longitude'
    ];

    public function createLocation(string $lantitude, string $longitude) {
        $data = [
            'latitude' => $lantitude,
            'longitude' => $longitude
        ];

        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->insert($data);
    }
}