<?php

namespace App\Console\Commands;

use App\Models\CreditCard;
use App\Models\User;
use Carbon\Carbon;
use Carbon\Exceptions\InvalidFormatException;
use DateTime;
use Exception;
use Illuminate\Console\Command;
use JsonMachine\Exception\InvalidArgumentException;
use \JsonMachine\Items;
use JsonMachine\JsonDecoder\DecodingError;

class InsertJsonFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'insert';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Insert json';

    /**
     * Execute the console command.
     *
     * @return int
     * @throws InvalidArgumentException
     */
    public function handle(): int
    {
        $file = dirname(__DIR__, 3) . "/challenge/challenge.json";
        $challenge = Items::fromFile($file);
        $totalRecords = 0;
        $endDate = Carbon::now()->subYears(18);
        $startDate = Carbon::now()->subYears(65);

        foreach ($challenge as $data) {
            if (User::query()->where('email', '=', $data->email)->exists())
                continue;

            $date = null;
            if(!empty($data->date_of_birth)) {
                try {
                    $date = Carbon::parse($data->date_of_birth);
                } catch (Exception $e) {
                    $date_array = date_parse(str_replace("/", "-", $data->date_of_birth));
                    $date = date('Y-m-d H:i:s', mktime($date_array['hour'], $date_array['minute'], $date_array['second'], $date_array['month'], $date_array['day'], $date_array['year']));
                    $date = Carbon::parse($date);
                }
            }

            if($date instanceof DateTime)
                if (!$date->between($startDate, $endDate))
                    continue;

            $user = User::create([
                'name' => $data->name,
                'address' => $data->address,
                'checked' => $data->checked,
                'description' => $data->description,
                'interest' => $data->interest,
                'date_of_birth' => $date,
                'email' => $data->email,
                'account' => $data->account,
            ]);

            CreditCard::create([
                'type' => $data->credit_card->type,
                'number' => $data->credit_card->number,
                'name' => $data->credit_card->name,
                'expiration_date' => $data->credit_card->expirationDate,
                'user_id' => $user->id
            ]);

            $totalRecords++;
        }

        echo "Inserted a total of ".$totalRecords." record";

        return 0;
    }
}
