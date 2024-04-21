<?php

class GuideprofileModel {
    use Model;

    protected string $table = 'guide_profile';

    protected array $allowedColumns = [
        'guide_id',
        'description',
        'languages',
        'certifications',
    ];

    public function updateGuideProfile(array $data): void {
        $userId = $_SESSION['USER']->id;

        // Filter the data to include only allowed columns
        $filteredData = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        // Update the guide profile for the current user
        $this->update($userId, $filteredData, 'guide_id');
    }

    public function getGuideProfileByUserId(int $userId) {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_profile.*')->where('guide_id', $userId);

        return $this->query($q->getQuery(), $q->getData());
    }
}