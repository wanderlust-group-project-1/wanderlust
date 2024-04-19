<?php

class GuideAvailabilityModel{
    use Model;
    protected string $table = 'guide_availability';
    protected array $allowedColumns = [
        'guide_id',
        'day',
        'start_time',
        'end_time',
    ];

    public function updateSchedule(array $data, int $id) {
        // Filter the data to include only allowed columns
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        // Update the guide profile for the current user
        return $this->update($id, $data);
    }

    public function getSchedule(int $scheduleId) {
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->select('*')->where('id', $scheduleId);

        return $this->query($q->getQuery(), $q->getData(), true);
    }

    public function deleteSchedule(int $scheduleId) {
        return $this->delete($scheduleId, 'id');
    }

    public function getSchedulesByGuideId(int $guideId, int $scheduleId): mixed {
        $q = new QueryBuilder();
        $q->setTable('guide_availability');
        $q->select('guide_availability.*')->where('guide_availability.guide_id', $guideId);

        return $this->query($q->getQuery(), $q->getData());
    }

    public function getScheduleByGuideId(int $guideId, int $scheduleId): mixed {
        $q = new QueryBuilder();
        $q->setTable('guide_availability');
        $q->select('guide_availability.*')->where('guide_availability.guide_id', $guideId)
            ->where('guide_availability.id', $scheduleId);

        return $this->query($q->getQuery(), $q->getData());
    }
}