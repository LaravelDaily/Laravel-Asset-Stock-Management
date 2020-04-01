<?php

namespace App\Console\Commands;

use App\Team;
use App\User;
use App\Stock;
use App\Transaction;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use App\Notifications\AdminDailyReportNotification;

/**
 * Class DailyReportCommand
 * @package App\Console\Commands
 */
class DailyReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'daily-report:send';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Daily report of hospitals for admins';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $dangerStock  = $this->dangerStocksByTeam();
        $transactions = $this->dailyTransactions();
        $admins       = User::whereHas('roles', function ($q) {
            return $q->where('id', 1);
        })->get();

        foreach ($admins as $admin) {
            $admin->notify(new AdminDailyReportNotification($dangerStock, $transactions));
        }
    }

    /**
     * @return mixed
     */
    private function dailyTransactions()
    {
        //count sum of transactions grouped by team and asset
        $transactions = Transaction::select(['transactions.asset_id', 'transactions.team_id', DB::raw('sum(stock) as sum')])
            ->with(['team', 'asset'])
            ->groupBy(['team_id', 'asset_id'])
            ->join('teams', 'transactions.team_id', '=', 'teams.id')
            ->orderBy('teams.name')
            ->orderByDesc('sum')
            ->get();

        $stocks = Stock::all();

        //set current_stock for every transaction
        foreach ($transactions as $transaction) {
            $transaction->current_stock = $stocks->where('team_id', $transaction->team_id)
                ->where('asset_id', $transaction->asset_id)
                ->first()
                ->current_stock;
        }

        return $transactions;
    }

    /**
     * @return Builder[]|Collection
     */
    private function dangerStocksByTeam()
    {
        return Team::with(['stocks' => function ($q) {
            return $q->select(['stocks.*', 'assets.*'])
                ->join('assets', function ($join) {
                    $join->on('stocks.asset_id', '=', 'assets.id');
                })
                ->where('assets.danger_level', '>', 0)
                ->whereRaw('stocks.current_stock < assets.danger_level');
        }])
            ->orderBy('name')
            ->get();
    }
}
