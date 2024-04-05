<?php

class PackageModel
{
    use Model;

    protected string $table = 'package';
    protected array $allowedColumns = [
        'guide_id',
        'price',
        'max_group_size',
        'max_distance',
        'transport_needed',
        'places'
    ];

    public function createPackage(array $data)
    {
        if ($this->validatePackageData($data)) {
            return $this->insert($data);
        }

        return false;
    }

    public function updatePackage(int $packageId, array $data)
    {
        if ($this->validatePackageData($data)) {
            return $this->update($packageId, $data, 'id');
        }

        return false;
    }

    private function validatePackageData(array $data)
    {
        $requiredFields = ['guide_id', 'price', 'max_group_size', 'max_distance', 'transport_needed', 'places'];

        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                // Field is required, but it is missing or empty
                return false;
            }
        }

        // Validate numeric fields
        $numericFields = ['guide_id', 'price', 'max_group_size', 'max_distance'];

        foreach ($numericFields as $field) {
            if (!is_numeric($data[$field])) {
                // Field should be numeric, but it is not
                return false;
            }
        }

        // Additional custom validation can be added here

        return true;
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
}
