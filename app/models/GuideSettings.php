<?php

class GuideSettingsModel{
    use Model;

    protected $table = 'rental_settings';
    protected $allowedColumns = [
        'renting_state',
        'recovery_period',
    ];

    public function updateSettings($id,$data){
        return $this->update($id,$data,'rentalservice_id');

        


    }
    

}