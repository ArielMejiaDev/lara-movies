<?php


namespace App\Actions;


class PenaltyCalculator
{
    const PENALTY_FEE = 10;
    const NO_PENALTY_TEXT = 'no penalty';

    protected $daysBetween;

    public function calculateFromDate($from, $to = null)
    {
        if(is_null($to)) {
            $to = now();
        }
        $this->penaltyDays($from, $to);

        return $this->getPenaltyAttribute();
    }

    /**
     * @return int
     */
    protected function penaltyDays($from, $to)
    {
        return $this->daysBetween = $from->diffInDays($to, false);
    }

    /**
     * @return bool
     */
    protected function applyPenalty()
    {
        return $this->daysBetween > 0;
    }

    /**
     * @return float|int|string
     */
    protected function getPenaltyAttribute()
    {
        if($this->applyPenalty()) {
            return $this->daysBetween * self::PENALTY_FEE;
        }
        return self::NO_PENALTY_TEXT;
    }
}
