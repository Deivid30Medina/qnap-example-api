<?php

namespace App\Models;

class FolderSearch
{
    public string $id;
    public string $cls;
    public string $text;
    public int $no_setup;
    public int $is_cached;
    public int $draggable;
    public string $iconCls;
    public int $max_item_limit;
    public int $real_total;

    public function __construct(array $data)
    {
        $this->id = $data['id'] ?? '';
        $this->cls = $data['cls'] ?? '';
        $this->text = $data['text'] ?? '';
        $this->no_setup = $data['no_setup'] ?? 0;
        $this->is_cached = $data['is_cached'] ?? 0;
        $this->draggable = $data['draggable'] ?? 0;
        $this->iconCls = $data['iconCls'] ?? '';
        $this->max_item_limit = $data['max_item_limit'] ?? 0;
        $this->real_total = $data['real_total'] ?? 0;
    }
}
