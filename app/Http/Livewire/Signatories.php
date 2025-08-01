<?php
public $name, $designation; // For Add Signatory form
public $prepared_id, $noted_by_id, $funds_availability_id, $approved_id; // For Assign Signatory form

public function save()
{
    // ...save logic...
    $this->reset(['name', 'designation']);
}

public function updateRoles()
{
    // ...update logic...
    $this->reset(['prepared_id', 'noted_by_id', 'funds_availability_id', 'approved_id']);
}