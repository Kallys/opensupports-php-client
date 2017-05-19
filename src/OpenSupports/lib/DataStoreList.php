<?php

namespace OpenSupports\Lib;

use OpenSupports\Models\DataStore;

class DataStoreList implements \IteratorAggregate {
    private $list = [];

    public static function getList($type, $beanList) {
        $dataStoreList = new DataStoreList();

        foreach ($beanList as $bean) {
            $dataStoreList->add(new $type($bean));
        }

        return $dataStoreList;
    }

    public function getIterator() {
        return new \ArrayIterator($this->list);
    }

    public function add(DataStore $dataStore) {
        $this->list[] = $dataStore;
    }

    public function remove(DataStore $dataStore) {
        $dataStoreIndexInList = $this->getIndexInListOf($dataStore);

        if ($dataStoreIndexInList == -1) {
            return false;
        } else {
            unset($this->list[$dataStoreIndexInList]);
            return true;
        }
    }

    public function includesId($id) {
        $includes = false;

        foreach($this->list as $item) {
            if($item->id == $id) {
                $includes = true;
            }
        }

        return $includes;
    }

    public function isEmpty() {
        return empty($this->list);
    }

    public function toBeanList() {
        $beanList = [];

        foreach($this->list as $item) {
            $item->updateBeanProperties();
            $beanList[] = $item->getBeanInstance();
        }

        return $beanList;
    }

    public function toArray() {
        $array = [];

        foreach($this->list as $item) {
            $item->updateBeanProperties();
            $array[] = $item->toArray();
        }

        return $array;
    }

    private function getIndexInListOf($dataStore) {
        foreach ($this->list as $itemIdInList => $item) {
            if ($item->id === $dataStore->id) {
                return $itemIdInList;
            }
        }

        return -1;
    }
}