<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Client;
use Illuminate\Console\Command;

class exportcsv extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'csv:export';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Simple command to export client data to CSV format';

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
     * @return int
     */
    public function handle()
    {

        $file_url = 'storage/export.csv';
        $handle = fopen($file_url, 'w');


        fputcsv($handle, ['id', 'name', 'surname', 'latest_payment_date', 'latest_payment_amount']);

        /**
         * In order to export the correct data, I perform a whereHas query,
         * asking the db to fetch only records that oblidge to the
         * payment's created_at date in the last 30 days. This query will still fetch all
         * data. Then I order the payments by descending order on their created_at value.
         * Finally, I chunk the results in order not to overwhelm the memory in
         * case of large volumes of data.
         */

        Client::whereHas('payments', function ($query) {
            return $query->where('created_at', '>', Carbon::now()->subDays(1500));
        })->with(['payments' => function ($q){
            $q->orderBy('created_at', 'DESC');
        }])
        ->chunk(100, function ($clients) use ($handle) {
            foreach ($clients as $client) {
                $row['id'] = $client->id;
                $row['name'] = $client->name;
                $row['surname'] = $client->surname;
                $row['latest_payment_date'] = $client->payments->first()->created_at;
                $row['latest_payment_amount'] = $client->payments->first()->amount;
                fputcsv($handle, $row);
            }
        });



        // foreach ($clients as $client) {
        //     $row['id'] = $client->id;
        //     $row['name'] = $client->name;
        //     $row['surname'] = $client->surname;
        //     $row['latest_payment_date'] = $client->payments->first()->created_at;
        //     $row['latest_payment_amount'] = $client->payments->first()->amount;
        //     fputcsv($handle, $row);
        // }

        fclose($handle);

        $this->info("CSV file generated. You can locate it in $file_url");
    }
}
