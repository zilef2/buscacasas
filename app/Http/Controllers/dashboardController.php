<?php

namespace App\Http\Controllers;

use App\helpers\MyGlobalHelp;
use App\helpers\Myhelp;
use App\helpers\MyModels;
use App\Models\docentes;
use App\Models\Inspeccion;
use App\Models\prestamoHistorico;
use App\Models\Role;
use App\Models\User;
use Carbon\Carbon;
use Doctrine\DBAL\Query\QueryException;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Inertia\Inertia;
use Inertia\Response;

class dashboardController extends Controller
{

    public function Dashboard($nombredoc = null): Response
    {

        return Inertia::render('Dashboard', [
            'users' => (int)User::count(),
            'roles' => (int)Role::count(),
        ]);
    }

}
