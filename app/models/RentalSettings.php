<?php

class RentalSettingsModel{
    use Model;

    protected $table = 'rental_settings';


    public function updateSettings($id,$data){
        return $this->update($id,$data,'rentalservice_id');

        


    }
    

}