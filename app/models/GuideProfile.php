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

    public function updateGuideProfile(array $data) {
        $userId = UserMiddleware::getUser()['id'];
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->update([
                'description' => $data['description'],
                'languages' => $data['languages'],
                'certifications' => $data['certifications'],
            ]
        )->where('guide_id', $userId);
        return $this->query($q->getQuery(), $q->getData());
    }

    public function getGuideProfileByUserId(int $userId) {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('guide_profile.*')->where('guide_id', $userId);

        return $this->query($q->getQuery(), $q->getData());
    }
}