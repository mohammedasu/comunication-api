<?php

namespace App\Repositories;

use App\Models\WhatsappTemplate;

class WhatsappTemplateRepository
{
    protected $model;

    public function __construct()
    {
        $this->model = new WhatsappTemplate();
    }

    public function getTemplates($templateString, $varCount = 0)
    {
        return $this->model->where('template_id', 'like', $templateString . '%')
            ->where('variable_count', $varCount)->first();
    }

    public function fetch($where)
    {
        return $this->model->where($where)->first();
    }
}
