<?php

namespace Ylateef\LaPromo;

use Carbon\Carbon;
use Ylateef\LaPromo\Models\Promocode;
use Ylateef\LaPromo\Exceptions\AlreadyUsedException;
use Ylateef\LaPromo\Exceptions\UnauthenticatedException;
use Ylateef\LaPromo\Exceptions\InvalidPromocodeException;

trait Rewardable
{
    /**
     * Get the promocodes that are related to user.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promocodes()
    {
        return $this->belongsToMany(Promocode::class, config('promocodes.relation_table'))
            ->withPivot('used_at');
    }

    /**
     * Apply promocode to user and get callback.
     *
     * @param string $code
     * @param null|\Closure $callback
     *
     * @return null|\Ylateef\LaPromo\Model\Promocode
     * @throws \Ylateef\LaPromo\Exceptions\AlreadyUsedException
     */
    public function applyCode($code, $callback = null)
    {
        try {
            if ($promocode = Promocodes::check($code)) {
                if ($promocode->users()->wherePivot('user_id', $this->id)->exists()) {
                    throw new AlreadyUsedException;
                }

                $promocode->users()->attach($this->id, [
                    'used_at' => Carbon::now(),
                ]);

                $promocode->load('users');

                if (is_callable($callback)) {
                    $callback($promocode);
                }

                return $promocode;
            }
        } catch (InvalidPromocodeException $exception) {
            //
        }

        if (is_callable($callback)) {
            $callback(null);
        }

        return null;
    }

    /**
     * Redeem promocode to user and get callback.
     *
     * @param string $code
     * @param null|\Closure $callback
     *
     * @return null|\Ylateef\LaPromo\Model\Promocode
     * @throws \Ylateef\LaPromo\Exceptions\AlreadyUsedException
     */
    public function redeemCode($code, $callback = null)
    {
        return $this->applyCode($code, $callback);
    }
}
