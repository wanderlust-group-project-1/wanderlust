<?php

class FindGuideModel {
    use Model;

    function getSuitableGuides($data)
    {
        $q = new QueryBuilder();
        $q = "CALL GetSuitableGuides(:max_group_size, :date, :places, :transport_needed)";
        $data = [
            'max_group_size' => $data['no_of_people'],
            'date' => $data['date'],
            'places' => $data['location'],
            'transport_needed' => $data['transport_supply']
        ];
        return $this->query($q, $data);
    }

    function getGuideDetails($data)
    {
        $q = new QueryBuilder();
        $q = "CALL ViewGuideProfile(:guide_id)";
        $data = [
            'guide_id' => $data
        ];
        return $this->query($q, $data);
    }

    function getGuidePackages($data)
    {
        $q = new QueryBuilder();
        $q = "CALL GetGuidePackages(:package_id)";
        $data = [
            'package_id' => $data
        ];
        return $this->query($q, $data);
    }


}