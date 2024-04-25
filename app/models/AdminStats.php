<?php


class AdminStatsModel
{
    use Model;

    public function getRentalServiceCount()
    {

        $q = new QueryBuilder();

        $q->setTable('rental_services');
        $q->count()->where("status", "accepted");


        return $this->query($q->getQuery(), $q->getData());
    }

    public function getCustomerCount()
    {

        $q = new QueryBuilder();

        $q->setTable('customers');
        $q->count();


        return $this->query($q->getQuery(), $q->getData());
    }

    public function getGuideCount()
    {

        $q = new QueryBuilder();

        $q->setTable('guides');
        $q->count();


        return $this->query($q->getQuery(), $q->getData());
    }

    public function getTipsCount()
    {

        $q = new QueryBuilder();

        $q->setTable('tips');
        $q->count();


        return $this->query($q->getQuery(), $q->getData());
    }

    public function getRentComplainsCount()
    {

        $q = new QueryBuilder();

        $q->setTable('rent_return_complaints');
        $q->count();


        return $this->query($q->getQuery(), $q->getData());
    }

    public function getOrdersCount()
    {

        $q = new QueryBuilder();

        $q->setTable('rent');
        $q->count();


        return $this->query($q->getQuery(), $q->getData());
    }
}
