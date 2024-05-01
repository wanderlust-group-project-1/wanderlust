<?php

class PackageModel
{
    use Model;

    protected string $table = 'package';
    protected array $allowedColumns = [
        'name',
        'guide_id',
        'price',
        'max_group_size',
        'max_distance',
        'transport_needed',
        'places'
    ];
    public function createPackage(array $data){

        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        $data['guide_id'] = $_SESSION['USER']->id;

        // show($data);

        $this->insert($data);
    }


    public function updatePackage(array $data, int $id)
    {

        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->update($id, $data);
    }
    

    private function validatePackageData(array $data)
    {
        // $requiredFields = ['guide_id', 'price', 'max_group_size', 'max_distance', 'transport_needed', 'places'];

        // foreach ($requiredFields as $field) {
        //     if (!isset($data[$field]) || empty($data[$field])) {
        //         // Field is required, but it is missing or empty
        //         return false;
        //     }
        // }

        // // Validate numeric fields
        // $numericFields = ['guide_id', 'price', 'max_group_size', 'max_distance'];

        // foreach ($numericFields as $field) {
        //     if (!is_numeric($data[$field])) {
        //         // Field should be numeric, but it is not
        //         return false;
        //     }
        // }

        // // Additional custom validation can be added here

        // return true;
        return empty($this->errors);
    }

    public function getPackage(int $packageId)
    {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('*')->where('id', $packageId);

        return $this->query($q->getQuery(), $q->getData(), true);
    }

    public function deletePackage(int $packageId)
    {
        return $this->delete($packageId, 'id');
    }

    public function getPackagesForGuide(int $guideId)
    {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('*')->where('guide_id', $guideId);

        return $this->query($q->getQuery(), $q->getData());
    }

    public function getPackagesByGuide(int $guideId): mixed
    {
        $q = new QueryBuilder();
        $q->setTable('package');
        $q->select('package.*')->where('package.guide_id', $guideId);

        return $this->query($q->getQuery(), $q->getData());
    }

    public function getPackageByGuide(int $guideId, int $packageId): mixed
    {
        $q = new QueryBuilder();
        $q->setTable('package');
        $q->select('package.*')->where('package.guide_id', $guideId)->where('package.id', $packageId);

        return $this->query($q->getQuery(), $q->getData());
    }
}
