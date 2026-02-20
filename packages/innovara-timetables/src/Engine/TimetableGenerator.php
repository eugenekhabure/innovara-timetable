<?php

namespace Innovara\Timetables\Engine;

use Innovara\Timetables\Engine\Rules\RuleInterface;
use Innovara\Timetables\Models\TimetableEntry;

class TimetableGenerator
{
    /** @var RuleInterface[] */
    protected array $rules = [];

    /** @var TimetableEntry[] */
    protected array $entries = [];

    protected array $context = [];

    public function __construct(array $context = [])
    {
        $this->context = $context;
    }

    public function addRule(RuleInterface $rule): self
    {
        $this->rules[] = $rule;
        return $this;
    }

    /**
     * Try to place an entry. Returns true if placed, false if blocked by rules.
     */
    public function tryPlace(TimetableEntry $candidate, ?string &$failedReason = null): bool
    {
        foreach ($this->rules as $rule) {
            if (!$rule->passes($candidate, $this->entries, $this->context)) {
                $failedReason = $rule->message();
                return false;
            }
        }

        $this->entries[] = $candidate;
        return true;
    }

    /**
     * Get placed entries
     * @return TimetableEntry[]
     */
    public function entries(): array
    {
        return $this->entries;
    }
}
