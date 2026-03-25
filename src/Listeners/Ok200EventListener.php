<?php

namespace Ok200\Analytics\Listeners;

use Ok200\Analytics\Jobs\RecordLogin;
use Ok200\Analytics\Jobs\RecordOrder;
use Ok200\Analytics\Jobs\RecordUser;

class Ok200EventListener
{
    public function handle(object $event, string $analyticsEvent, array $mapping): void
    {
        $email = $this->resolve($event, $mapping['email'] ?? null);

        if (! $email) {
            return;
        }

        $userSha1 = sha1($email);

        match ($analyticsEvent) {
            'login' => RecordLogin::dispatch($userSha1),
            'user' => RecordUser::dispatch($userSha1),
            'order' => RecordOrder::dispatch(
                userSha1: $userSha1,
                orderValue: $this->resolve($event, $mapping['order_value'] ?? null) ?? 0,
                orderId: (string) ($this->resolve($event, $mapping['order_id'] ?? null) ?? ''),
            ),
            default => null,
        };
    }

    /**
     * Resolve a dot-notated path from an event object.
     *
     * e.g. 'user.email' resolves $event->user->email (or $event->user['email'])
     */
    protected function resolve(object $event, ?string $path): mixed
    {
        if ($path === null) {
            return null;
        }

        $segments = explode('.', $path);
        $current = $event;

        foreach ($segments as $segment) {
            if (is_object($current) && isset($current->{$segment})) {
                $current = $current->{$segment};
            } elseif (is_array($current) && array_key_exists($segment, $current)) {
                $current = $current[$segment];
            } else {
                return null;
            }
        }

        return $current;
    }
}
