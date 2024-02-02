<?php 

class ItemModel {
    use Model;


    protected string $table = 'item';
    protected array $allowedColumns = [
        'equipment_id',
        ];


    public function createItem(int $id) {
        return $this->insert(['equipment_id' => $id]);
    }

    public function createItems(array $data) {
        $count = $data['count'];

        for ($i = 0; $i < $count; $i++) {
            $this->createItem($data['equipment_id']);
        }
    }

    public function removeItem(array $data) {
        $data = array_filter($data, function ($key) {
            return in_array($key, $this->allowedColumns);
        }, ARRAY_FILTER_USE_KEY);

        return $this->delete($data);
    }
}