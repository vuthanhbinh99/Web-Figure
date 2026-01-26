<?php
function generateId($prefix){
    return $prefix . strtoupper(substr(uniqid(), -6));
} 
?>