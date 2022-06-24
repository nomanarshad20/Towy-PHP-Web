<?php


namespace App\Services\Admin;


class DashboardService
{
    public function index()
    {
        return view('admin.dashboard.dashboard');
    }
}
