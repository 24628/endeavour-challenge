<?php

namespace App\Console\Commands;

use App\Models\CreditCard;
use App\Models\User;
use Carbon\Exceptions\InvalidFormatException;
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

        foreach ($challenge as $data) {

            if (User::query()->where('email', '=', $data->email)->exists())
                continue;

            $user = User::create([
                'name' => $data->name,
                'address' => $data->address,
                'checked' => $data->checked,
                'description' => $data->description,
                'interest' => $data->interest,
                'date_of_birth' => $data->date_of_birth !== null ? $data->date_of_birth : "not found",
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
