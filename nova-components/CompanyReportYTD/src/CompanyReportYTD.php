<?php

namespace Sheets\CompanyReportYTD;

use Laravel\Nova\Nova;
use Laravel\Nova\Tool;

class CompanyReportYTD extends Tool
{
    /**
     * Perform any tasks that need to happen when the tool is booted.
     *
     * @return void
     */
    public function boot()
    {
        Nova::script('company-report-y-t-d', __DIR__.'/../dist/js/tool.js');
        Nova::style('company-report-y-t-d', __DIR__.'/../dist/css/tool.css');
    }

    /**
     * Build the view that renders the navigation links for the tool.
     *
     * @return \Illuminate\View\View
     */
    public function renderNavigation()
    {
        return ''; // view('company-report-y-t-d::navigation');
    }
}
