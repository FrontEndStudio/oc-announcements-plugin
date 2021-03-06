<?php namespace Fes\Notice\Components;

use Cms\Classes\Page;
use Cms\Classes\ComponentBase;
use Lang;
use Exception;
use SystemException;
use Fes\Notice\Models\Record;
use Fes\Notice\Models\Category;

class NoticeBySection extends ComponentBase
{
    public $records;
    public $noRecordsMessage;

    public function componentDetails()
    {
        return [
            'name'        => 'fes.notice::lang.components.by_section_name',
            'description' => 'fes.notice::lang.components.by_section_description'
        ];
    }

    public function defineProperties()
    {
        return [
            'noRecordsMessage' => [
                'title'             => 'fes.notice::lang.components.no_records',
                'description'       => 'fes.notice::lang.components.no_records_description',
                'type'              => 'string',
                'default'           => Lang::get('fes.notice::lang.components.no_records_default'),
                'showExternalParam' => false,
            ],
            'sortColumn' => [
                'title'             => 'fes.notice::lang.components.sort_column',
                'description'       => 'fes.notice::lang.components.sort_column_description',
                'type'              => 'autocomplete',
                'group'             => 'fes.notice::lang.components.sorting',
                'showExternalParam' => false
            ],
            'sortDirection' => [
                'title'             => 'fes.notice::lang.components.sort_direction',
                'type'              => 'dropdown',
                'showExternalParam' => false,
                'group'             => 'fes.notice::lang.components.sorting',
                'options'           => [
                    'asc'   => 'fes.notice::lang.components.order_direction_asc',
                    'desc'  => 'fes.notice::lang.components.order_direction_desc'
                ]
            ],
            'section' => [
                'title'             => 'fes.notice::lang.components.section',
                'description'       => 'fes.notice::lang.components.section_description',
                'default'           => 'calendar',
                'type'              => 'dropdown'
            ]
        ];
    }

    public function getsectionOptions()
    {

        $options = [];

        $sections = array (
                        'calendar' => 'fes.notice::lang.record.calendar',
                        'news' => 'fes.notice::lang.record.news',
                        'download' => 'fes.notice::lang.record.download'
                    );

        foreach ($sections as $k => $v) {
            $options[$k] = $v;
        }

        return $options;

    }

    //
    // Rendering and processing
    //

    public function onRun()
    {
        $this->prepareVars();
        $query = Record::wherePublished('1')->where('section', $this->property('section'));
        $query = $this->sort($query);
        $this->records = $query->with('category')->get();
    }

    protected function prepareVars()
    {
        $this->noRecordsMessage = $this->page['noRecordsMessage'] = Lang::get($this->property('noRecordsMessage'));
    }

    protected function sort($query)
    {

        $sortColumn = trim($this->property('sortColumn'));

        if (!strlen($sortColumn)) {
            return $query;
        }

        $sortDirection = trim($this->property('sortDirection'));

        if ($sortDirection !== 'desc') {
            $sortDirection = 'asc';
        }

        return $query->orderBy($sortColumn, $sortDirection);
    }
}
