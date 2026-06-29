<?php
namespace App\Helpers;

class Validation {
    private $errors = [];
    private $data = [];
    
    public function __construct($data) {
        $this->data = $data;
    }
    
    public function required($field, $message = null) {
        if (!isset($this->data[$field]) || empty(trim($this->data[$field]))) {
            $this->errors[$field] = $message ?? "Le champ $field est requis.";
        }
        return $this;
    }
    
    public function email($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_EMAIL)) {
                $this->errors[$field] = $message ?? "L'email n'est pas valide.";
            }
        }
        return $this;
    }
    
    public function minLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) < $length) {
            $this->errors[$field] = $message ?? "Le champ $field doit contenir au moins $length caractères.";
        }
        return $this;
    }
    
    public function maxLength($field, $length, $message = null) {
        if (isset($this->data[$field]) && strlen($this->data[$field]) > $length) {
            $this->errors[$field] = $message ?? "Le champ $field ne doit pas dépasser $length caractères.";
        }
        return $this;
    }
    
    public function numeric($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!is_numeric($this->data[$field])) {
                $this->errors[$field] = $message ?? "Le champ $field doit être un nombre.";
            }
        }
        return $this;
    }
    
    public function integer($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!filter_var($this->data[$field], FILTER_VALIDATE_INT)) {
                $this->errors[$field] = $message ?? "Le champ $field doit être un nombre entier.";
            }
        }
        return $this;
    }
    
    public function inArray($field, $allowed, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            if (!in_array($this->data[$field], $allowed)) {
                $this->errors[$field] = $message ?? "La valeur du champ $field n'est pas valide.";
            }
        }
        return $this;
    }
    
    public function date($field, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date = \DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            if (!$date || $date->format('Y-m-d') !== $this->data[$field]) {
                $this->errors[$field] = $message ?? "La date n'est pas valide (format YYYY-MM-DD).";
            }
        }
        return $this;
    }
    
    public function dateAfter($field, $date, $message = null) {
        if (isset($this->data[$field]) && !empty($this->data[$field])) {
            $date1 = \DateTime::createFromFormat('Y-m-d', $this->data[$field]);
            $date2 = \DateTime::createFromFormat('Y-m-d', $date);
            if ($date1 && $date2 && $date1 <= $date2) {
                $this->errors[$field] = $message ?? "La date doit être postérieure à $date.";
            }
        }
        return $this;
    }
    
    public function isValid() {
        return empty($this->errors);
    }
    
    public function getErrors() {
        return $this->errors;
    }
    
    public function getData() {
        return $this->data;
    }
}