<?php

namespace TeamTeaTime\Forum\Http\Livewire;

class EventfulPaginatedComponent extends PaginatedComponent
{
    public function previousPage($pageName = 'page')
    {
        parent::previousPage($pageName);
        $this->dispatch('page-changed');
    }

    public function nextPage($pageName = 'page')
    {
        parent::nextPage($pageName);
        $this->dispatch('page-changed');
    }

    public function setPage($page, $pageName = 'page')
    {
        parent::setPage($page, $pageName);
        $this->dispatch('page-changed');
    }
}
