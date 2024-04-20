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

    // public function removeItem(array $data) {
    //     $data = array_filter($data, function ($key) {
    //         return in_array($key, $this->allowedColumns);
    //     }, ARRAY_FILTER_USE_KEY);

    //     return $this->delete($data['item_id'], 'id');
    // }

    public function getAvailableItems(array $data) {
        $q = new QueryBuilder();

        // $data have equipment_id start_date end_date
        
        // $q->setTable('item');
        // $q->select('item.*')
        //     ->leftJoin('rent_item', 'item.id', 'rent_item.item_id')
        //     ->leftJoin('rent', 'rent_item.rent_id', 'rent.id')
        //     ->where('item.equipment_id', $data['equipment_id'])
        //     ->where('rent.start_date', $data['end_date'] , '>')
        //     ->orWhere('rent.end_date', $data['start_date'] , '<')
        //     ->where('item.equipment_id', $data['equipment_id'])
        //     ->orWhere('rent.id', null, 'IS')
        //     ->where('item.equipment_id', $data['equipment_id']);

        // show($q->getQuery());
        // show ($q->getData());
        // return $this->query($q->getQuery(), $q->getData());

        $q = 'CALL getAvailableItems(:equipment_id, :start_date, :end_date)';
        $data = [
            'equipment_id' => $data['equipment_id'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date']
        ];

        return $this->query($q, $data);

       
    }

    public function makeUnavailableTemporarily(int $id) {
        $q = 'CALL makeItemUnavailableTemporarily(:id)';
        return $this->query($q, ['id' => $id])[0];
    }

    public function makeUnavailablePermanently(int $id) {
        $q = 'CALL makeItemUnavailablePermanently(:id)';
        return $this->query($q, ['id' => $id])[0];
    }

    public function makeAvailable(int $id) {
        $q = 'CALL makeItemAvailable(:id)';
        return $this->query($q, ['id' => $id])[0];
    }

    public function makeUnavailableByEquipment(int $id) {
        $this->update($id, ['status' => 'unavailable'], 'equipment_id');
    }





}