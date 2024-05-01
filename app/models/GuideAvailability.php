<?php

class GuideAvailabilityModel{
    use Model;
    protected string $table = 'guide_availability';
    protected array $allowedColumns = [
        'guide_id',
        'date',
        'availability',
    ];

    public function updateSchedule(array $data): void {
        $guideId = $data['guide_id'];
        $date = $data['date']+1;

        

        // Filter the data to include only allowed columns
        // $filteredData = array_filter($data, function ($key) {
        //     return in_array($key, $this->allowedColumns);
        // }, ARRAY_FILTER_USE_KEY);

        // Update the guide profile for the current user
        // $this->update($guideId, $filteredData, 'guide_id');
        $q = new QueryBuilder();
        $q->setTable($this->table);
        $q->update([
            'availability' => $data['availability']
        ])->where('guide_id', $guideId)
            ->where('date', $date);
    }

    public function createSchedule(array $data): void {
       $this->insert($data);
    }

    public function getSchedulesByGuideId(int $guideId, int $scheduleId): mixed {
        $q = new QueryBuilder();
        $q->setTable('guide_availability');
        $q->select('guide_availability.*')->where('guide_availability.guide_id', $guideId);

        return $this->query($q->getQuery(), $q->getData());
    }

    public function getScheduleByGuideIdandDate(int $guideId, string $day): mixed {
        $q = new QueryBuilder();
        $q->setTable('guide_availability');
        $q->select('guide_availability.*')->where('guide_availability.guide_id', $guideId)
            ->where('guide_availability.date', $day);

        return $this->query($q->getQuery(), $q->getData());
    }

    public function getDaysByMonth(int $guideId, array $data): mixed {
        $q = new QueryBuilder();
        $q = "CALL RetrieveAvailableDays(:guideId, :currentMonth, :currentYear)";
        $params = [
            'guideId' => $guideId,
            'currentMonth' => $data['currentMonth'],
            'currentYear' => $data['currentYear']
        ];
        return $this->query($q, $params);
    }
}