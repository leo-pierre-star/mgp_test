<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Contracts\Cache\CacheInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ContactRateLimiter
{
    private const CACHE_PREFIX = 'contact_last_';
    private const RATE_LIMIT_SECONDS = 3600;

    public function __construct(private readonly CacheInterface $cache)
    {
    }

    public function canSend(User $user): bool
    {
        $last = $this->getLastTimestamp($user);
        if ($last === 0) {
            return true;
        }
        return (time() - $last) >= self::RATE_LIMIT_SECONDS;
    }

    public function getRetryAfterSeconds(User $user): int
    {
        $last = $this->getLastTimestamp($user);
        if ($last === 0) {
            return 0;
        }
        $elapsed = time() - $last;
        $remaining = self::RATE_LIMIT_SECONDS - $elapsed;
        return $remaining > 0 ? $remaining : 0;
    }

    public function markSent(User $user): void
    {
        $key = $this->key($user);
        $this->cache->delete($key);
        $now = time();
        $this->cache->get($key, function (ItemInterface $item) use ($now) {
            $item->expiresAfter(self::RATE_LIMIT_SECONDS);
            return $now;
        });
    }

    private function getLastTimestamp(User $user): int
    {
        $key = $this->key($user);
        return (int)$this->cache->get($key, function (ItemInterface $item) {
            $item->expiresAfter(self::RATE_LIMIT_SECONDS);
            return 0;
        });
    }

    private function key(User $user): string
    {
        return self::CACHE_PREFIX . $user->getId();
    }
}
