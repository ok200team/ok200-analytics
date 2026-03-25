<?php

namespace Ok200\Analytics\Jobs;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Ok200\Analytics\Ok200Client;

class RecordLogin implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public function __construct(
        public string $userSha1,
    ) {}

    public function handle(Ok200Client $client): void
    {
        if (config('ok200.production_only') && config('app.env') !== 'production') {
            return;
        }

        $payload = [
            'user_sha1' => $this->userSha1,
            'event' => 'login',
            'domain' => config('ok200.domain'),
        ];

        try {
            $client->send($payload);
        } catch (Exception $exception) {
            if ($exception->getCode() == 429) {
                dispatch(new self($this->userSha1))->delay(now()->addMinutes(2));

                Log::warning('OK200 Analytics: rate limited on login event, retrying in 2 minutes.');

                return;
            }

            throw $exception;
        }
    }
}
