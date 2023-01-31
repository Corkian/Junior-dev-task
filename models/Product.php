<?php

namespace app\models;

use app\core\Database;

abstract class Product
{
    public string $sku;
    public string $name;
    public float $price;
    public string $type;
    public string $value;
    public static array $validTypes = ['DVD', 'Book', 'Furniture'];
    public array $data;

    public function __construct($input)
    {
        $this->data = $input;
    }

    public function validateData()
    {
        $errors = [];
        if ($this->validateSku()) {
            $errors[] = $this->validateSku();
        }
        if ($this->validateName()) {
            $errors[] = $this->validateName();
        }
        if ($this->validatePrice()) {
            $errors[] = $this->validatePrice();
        }
        if ($this->validateType()) {
            $errors[] = $this->validateType();
        }
        if ($this->validateValue()) {
            $errors[] = $this->validateValue();
        }
        return $errors;
    }

    private function validateSku()
    {
        if(!$this->data['sku']) {
            return "SKU was not entered!";
        }

        $db = new Database();
        if ($db->getProduct($this->data['sku'])) {
            return "SKU already used";
        }

        $this->sku = $this->data['sku'];
        return "";
    }

    private function validateName()
    {
        if(!$this->data['name']) {
            return "Name was not entered!";
        }

        if ($this->data['name'] === '') {
            return "Invalid name";
        }

        $this->name = $this->data['name'];
        return "";
    }

    private function validatePrice()
    {
        if(!$this->data['price']) {
            return "Price was not entered!";
        }

        if (!filter_var($this->data['price'], FILTER_VALIDATE_FLOAT) || !(strlen($this->data['price']) > 0) || !(floatval($this->data['price']) >= 0)) {
            return "Invalid price";
        }
        
        $this->price = floatval($this->data['price']);
        return "";
        
    }

    protected function reduceType($carry, $item){
        if($carry === true || $item === $carry){
            return true;
        }
        return $carry;
    }

    private function validateType()
    {
        if(!$this->data['type']) {
            return "Type was not entered!";
        }

        if(array_reduce($this::$validTypes, array($this, "reduceType"), $this->data['type']) !== true){
            return "Invalid type";
        }

        $this->type = $this->data['type'];
        return "";
    }

    abstract protected function validateValue();
}